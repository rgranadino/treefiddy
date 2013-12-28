<?php
/**
 * Weather.php
 * contains weather module
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
use Geekpunks\Common\Model;
use Geekpunks\Treefiddy\Irc_ChannelEvent;
use Geekpunks\Treefiddy\Irc;
use Geekpunks\Treefiddy\Bot;
use Geekpunks\Treefiddy\ModuleInterface;
/**
 * weather module
 * @todo figure out a way to pass along bot instance with event data
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Weather implements ModuleInterface {
    private $key;
    private $bot;
    /**
     * weather module constructor
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->key = $bot->getConfig()->getConfigValue('weather', 'key');
        $bot->registerEvent(Irc::EVENT_CHAN_MSG, array($this, 'chanHandler'));
    }
    /**
     * handle channel event
     * @param Model $eventData
     */
    public function chanHandler(Model $eventData)
    {
        if ($eventData instanceof Irc_ChannelEvent) {
            $message = $eventData->getMessage();
            if (preg_match('/^(weather|forecast) .*/', $message) != 1) {
                return;
            }
            $chanMessage  = '';
            $msg          = explode(' ', $message, 2);
            //try to parse location
            //we expect a zip code or a "city,state" format
            $location     = explode(',', $msg[1], 2);
            $error        = false;
            $errorMsg     = '';
            $locationInfo = array();
            if (isset($location[1])) {//if there's a comma assume we've got the right format
                $locationInfo['city']  = trim($location[0]);
                $locationInfo['state'] = trim($location[1]);
            } else {//if zip try to do some validation
                $locationInfo['zip'] = $location[0];
                if (!$this->validateZip($location[0])) {
                    $errorMsg = 'Invalid Zip code provided';
                    $error    = true;
                }
            }
            if ($error) {
                $chanMessage = $errorMsg;
            } else if ($msg[0] == 'weather') {
                $chanMessage = $this->getWeather($locationInfo, $this->bot->getModulesDir());
            } else {
                $chanMessage = '@TODO IMPLEMENT ME';
            }
            $channel = $eventData->getChannel();
            $eventData->getIrc()->sendChannelMessage($channel, $chanMessage);
        }
    }

    /**
     * get weather URL, detect how we're searching zip or city, state
     * @param array $locationInfo
     * @return string
     */
    private function getWeatherUrl(array $locationInfo)
    {
        if (isset($locationInfo['zip'])) {
            return "http://api.wunderground.com/api/{$this->key}/geolookup/conditions/q/{$locationInfo['zip']}.json";
        }
        //sanitize city name
        $city = str_replace(' ', '_', $locationInfo['city']);
        return "http://api.wunderground.com/api/{$this->key}/geolookup/conditions/q/{$locationInfo['state']}/{$city}.json";
    }

    /**
     * validate zip code
     * @param st $data
     * @return boolean
     */
    private function validateZip($zip)
    {
        $allstates = array (
             '9950099929',
             '3500036999',
             '7160072999', '7550275505',
             '8500086599',
             '9000096199',
             '8000081699',
             '0600006999',
             '2000020099', '2020020599',
             '1970019999',
             '3200033999', '3410034999',
             '3000031999',
             '9670096798', '9680096899',
             '5000052999',
             '8320083899',
             '6000062999',
             '4600047999',
             '6600067999',
             '4000042799', '4527545275',
             '7000071499', '7174971749',
             '0100002799',
             '2033120331', '2060021999',
             '0380103801', '0380403804', '0390004999',
             '4800049999',
             '5500056799',
             '6300065899',
             '3860039799',
             '5900059999',
             '2700028999',
             '5800058899',
             '6800069399',
             '0300003803', '0380903899',
             '0700008999',
             '8700088499',
             '8900089899',
             '0040000599', '0639006390', '0900014999',
             '4300045999',
             '7300073199', '7340074999',
             '9700097999',
             '1500019699',
             '0280002999', '0637906379',
             '2900029999',
             '5700057799',
             '3700038599', '7239572395',
             '7330073399', '7394973949', '7500079999', '8850188599',
             '8400084799',
             '2010520199', '2030120301', '2037020370', '2200024699',
             '0500005999',
             '9800099499',
             '4993649936', '5300054999',
             '2470026899',
             '8200083199'
        ); 
        foreach ($allstates as $ziprange) {
            if (
                (($zip >= substr($ziprange, 0, 5)) && ($zip <= substr($ziprange,5))) 
                && strlen($zip) == 5) {
                return true;
            }
        }
        return false;
    }

    /**
     * get weather for a given $locationInfo
     * cached response for thirty minutes to avoid API overages
     * @param array $locationInfo
     * @param str $moduleDir
     * @return string
     */
    function getWeather(array $locationInfo, $moduleDir)
    {
        $return = '';
        $url = $this->getWeatherUrl($locationInfo);
        $cachedUrl = $moduleDir.'/weather/tmp/'.md5(json_encode($locationInfo));
        if (is_file($cachedUrl) && ( (time() - filemtime($cachedUrl)) < 60 * 30 ) ) {
            $url = $cachedUrl;
        }
        $fp  = fopen($url, 'r');
        if ($fp) {
            $jsondata = '';
            while (!feof($fp)) $jsondata .= fgets($fp, 1000);
            fclose($fp);
            try {
                if ($cachedUrl != $url && is_writable($cachedUrl)) {
                    file_put_contents($cachedUrl, $jsondata);
                }
                $json   = json_decode($jsondata);
                $return = 'Weather for '.$json->{'current_observation'}->{'display_location'}->{'full'}.' '.$json->{'current_observation'}->{'observation_time'}.". The temperature is currently at ".$json->{'current_observation'}->{'temperature_string'}." which feels like ".$json->{'current_observation'}->{'feelslike_string'}.". Forecast is ".$json->{'current_observation'}->{'weather'}.", Visibility is ".$json->{'current_observation'}->{'visibility_mi'}.", Pressure is currently ".$json->{'current_observation'}->{'pressure_in'}.", while Dew Point is at ".$json->{'current_observation'}->{'dewpoint_string'}." and humidity is ".$json->{'current_observation'}->{'relative_humidity'}.". Winds are currently ".$json->{'current_observation'}->{'wind_string'};
            } catch (Exception $e) {
                $return = 'Failed to parse json.';
            }
        } else {
            $return = 'Something went horribly wrong, possibly the weather json feed is down? Check wunderground.com for details.';
        }
        return $return;
    }
}
