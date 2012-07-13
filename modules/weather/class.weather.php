<?php
/**
 * class.weather.php
 * contains weather module
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
/**
 * weather module
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class weather implements module {
    private $config = null;
    private $args;
    private $key;

    public function __construct(Net_SmartIRC &$irc, modules &$modules, config &$config)
    {
        $irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^weather .*', $modules->modules['weather'], 'weather_func');
	$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^forecast .*', $modules->modules['weather'], 'forecast_func');
        $this->config = &$config;
        $this->set_key();
    }

    private function set_key()
    {
        $this->key = $key = $this->config->config['weather']['key'];
        if (empty($key)) {
            $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,'wunderground.com Key not set in config!');
        }
    }

    private function get_url($type)
    {
        switch($type){
            case "weatherbyname" :
                return "http://api.wunderground.com/api/{$this->key}/geolookup/conditions/q/".$this->args[1]."/".$this->args[0].".json";
		break;
            case "weatherbyzip" :
                return "http://api.wunderground.com/api/{$this->key}/geolookup/conditions/q/{$this->args[0]}.json";
		break;
            default:
		return "http://api.wunderground.com/api/{$this->key}/geolookup/conditions/q/37075.json";
		break;
        }
    }

    private function validate_args($data)
    {
    	$zstate    = explode(' ',$data,2);
	$this->args      = explode(',',$zstate[1]);//0==zip | 1==STATE
        if(isset($this->args[1])){
            return true;
	}else {
            $allstates = array (
                 "9950099929",
                 "3500036999",
                 "7160072999", "7550275505",
                 "8500086599",
                 "9000096199",
                 "8000081699",
                 "0600006999",
                 "2000020099", "2020020599",
                 "1970019999",
                 "3200033999", "3410034999",
                 "3000031999",
                 "9670096798", "9680096899",
                 "5000052999",
                 "8320083899",
                 "6000062999",
                 "4600047999",
                 "6600067999",
                 "4000042799", "4527545275",
                 "7000071499", "7174971749",
                 "0100002799",
                 "2033120331", "2060021999",
                 "0380103801", "0380403804", "0390004999",
                 "4800049999",
                 "5500056799",
                 "6300065899",
                 "3860039799",
                 "5900059999",
                 "2700028999",
                 "5800058899",
                 "6800069399",
                 "0300003803", "0380903899",
                 "0700008999",
                 "8700088499",
                 "8900089899",
                 "0040000599", "0639006390", "0900014999",
                 "4300045999",
                 "7300073199", "7340074999",
                 "9700097999",
                 "1500019699",
                 "0280002999", "0637906379",
                 "2900029999",
                 "5700057799",
                 "3700038599", "7239572395",
                 "7330073399", "7394973949", "7500079999", "8850188599",
                 "8400084799",
                 "2010520199", "2030120301", "2037020370", "2200024699",
                 "0500005999",
                 "9800099499",
                 "4993649936", "5300054999",
                 "2470026899",
                 "8200083199"); 
            foreach ($allstates as $ziprange) {
                if ((($this->args[0] >= substr($ziprange, 0, 5)) && ($this->args[0] <= substr($ziprange,5))) && strlen($this->args[0]) == 5) {
		    return true;
                }
            }
        }
        return false;
    }

    function weather_func(Net_SmartIRC &$irc, Net_SmartIRC_data &$data)
    {
        $valid = $this->validate_args($data->message);
        if ($valid) {
            $url = $this->get_url((isset($this->args[1]) ? 'weatherbyname' : 'weatherbyzip'));
            $cachedUrl = $this->config->botDir.'/modules/weather/tmp/'.$this->args[0];
            if (is_file($cachedUrl) && ( (time() - filemtime($cachedUrl)) < 60 * 30 ) ) {
                $url = $cachedUrl;
            } 
            $fp  = fopen($url,'r');
            if ($fp) {
                $jsondata = '';
                while(!feof($fp)) $jsondata .= fgets($fp, 1000);
                fclose($fp);
                try {
                    if ($cachedUrl != $url) {
                        file_put_contents($cachedUrl,$jsondata);
                    }
                    $json  = json_decode($jsondata);
                    //$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$jsondata);
                    $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.": Weather for ".$json->{'current_observation'}->{'display_location'}->{'full'}.' '.$json->{'current_observation'}->{'observation_time'}.". The temperature is currently at ".$json->{'current_observation'}->{'temperature_string'}." which feels like ".$json->{'current_observation'}->{'feelslike_string'}.". Forecast is ".$json->{'current_observation'}->{'weather'}.", Visibility is ".$json->{'current_observation'}->{'visibility_mi'}.", Pressure is currently ".$json->{'current_observation'}->{'pressure_in'}.", while Dew Point is at ".$json->{'current_observation'}->{'dewpoint_string'}." and humidity is ".$json->{'current_observation'}->{'relative_humidity'}.". Winds are currently ".$json->{'current_observation'}->{'wind_string'}.".");
                } catch (Exception $e) {
                    $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.': Failed to parse json.');
                }
            }else {
                $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.': Something went horribly wrong, possibly the weather json feed is down? Check wunderground.com for details.');
            }
        }else {
            $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.': Please type a valid zip code.');
        }
    }
}
?>
