<?php
/**
 * class.rtca.php
 * contains real time congress api module
 * @author Evan Brown <noggin@gmail.com>
 */
/**
 * real time congress api module
 * @author Evan Brown <noggin@gmail.com>
 */
class rtca implements module {
    private $config = null;
    public function __construct(Net_SmartIRC &$irc, modules &$modules, config &$config)
    {
        $irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^rtca .*', $modules->modules['rtca'], 'rtca_func');
        $this->config = &$config;
    }
	function rtca_func(Net_SmartIRC &$irc, Net_SmartIRC_data &$data)
	{
	    	$zstate    = explode(' ',$data->message,2);
		$args      = explode(',',$zstate[1]);//0==zip | 1==STATE
 
	        $key       = $this->config->config['rtca']['key'];
	        if (empty($key)) {
	                $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,'SunlightLabs Key not set in config!');
	            }
	    $url       = "http://api.realtimecongress.org/api/v1/floor_updates.json?apikey={$key}";
            $cachedUrl = $this->config->botDir.'/modules/rtca/tmp/floor_updates.json';
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
                    $json  = json_decode($jsondata, TRUE);
		    $message = $data->nick.": At ".$json['floor_updates'][0]['timestamp']." the ".$json['floor_updates'][0]['chamber']." had the event of ";
		    foreach ($json['floor_updates'][0]['events'] as $event){
			$message .= $event." / ";
		    }
                    $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$message);
                } catch (Exception $e) {
                    $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.': Failed to parse json.');
                }
            }else {
                    $irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.': Something went horribly wrong, possibly the sunlightlabs json feed is down?');
            }
    }
}
?>
