<?php
/**
 * class.horoscope.php
 * Contains horoscope module class
 * @author Rolando Granadino <beeplogic@gmail.com>
 * @todo clean up!
 */
/**
 * horoscope module
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class horoscope {
    public function __construct(Net_SmartIRC &$irc, modules &$modules, config &$config)
    {
        $irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^!zodiac .*', $modules->modules['horoscope'], 'zodiac_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^!horoscope .*',$modules->modules['horoscope'], 'zodiac_func');
    }
	function zodiac_func(Net_SmartIRC &$irc, Net_SmartIRC_data &$data)
	{
		$signs=array('aquarius','aries','cancer','capricorn','gemini','leo','libra','pisces','sagittarius','scorpio','taurus','virgo');
		$chinese=array('rat','ox','tiger','rabbit','dragon','snake','horse','goat','monkey','rooster','dog','pig');

		$usign=explode(' ',$data->message,2);
		if(in_array(strtolower($usign[1]),$signs) ){
//|| in_array(strtolower($usign[1]),$chinese) ){
			$usign[1]=strtolower($usign[1]);
			$url = 'http://widgets.fabulously40.com/horoscope.json?sign='."{$usign[1]}";
 			$fp=file_get_contents($url);
	 		if ($fp) {
				$json = json_decode($fp);
				$msg = $json->{'horoscope'}->{'horoscope'};
				$msg = $data->nick.': '.$json->{'horoscope'}->{'sign'}.' - '.$msg;
				if (strlen($msg) > 430) {
					$msgs = str_split($msg,430);
					foreach ($msgs as $key=>$item) {
						$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$item);
					}
				} else {
 					$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$msg);
				}
 			}else{
 				$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.': Something went horribly wrong. Sorry, no horoscope for you!');
 			}
		} elseif (in_array(strtolower($usign[1]),$chinese)) {
			$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.', The chinese refuse to give us their horoscopes at the time being. Check back later.');
                } elseif (trim($usign[1]) == 'help') {
				$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.', Snyax: help [zodiac|chinese]');
		} elseif (trim($usign[1]) == 'help zodiac') {
				$zodiac = array('Capricorn - Dec. 22nd ~ Jan. 19th     Aquarius - Jan. 20th ~ Feb. 18th',
						'Pisces - Feb. 19th ~ Mar. 20th        Aries - Mar. 21st ~ Apr. 19th',
						'Taurus - Apr. 20th ~ May  20th       Gemini - May  21st ~ Jun. 21st',
						'Cancer - Jun. 22nd ~ Jul. 22nd          Leo - Jul. 23rd ~ Aug. 22nd',
						'Virgo - Aug. 23rd ~ Sep. 22nd        Libra - Sep. 23rd ~ Oct. 22nd',
						'Scorpio - Oct. 23rd ~ Nov. 21st  Sagittarius - Nov. 22nd ~ Dec. 21st');
				foreach ($zodiac as $key=>$sign) {
					$irc->message(SMARTIRC_TYPE_QUERY,$data->nick,$sign);
				}
		} elseif (trim($usign[1]) == 'help chinese') {
				$zodiac = array('Rat - 1972, 1984, 1996, 2008       Ox - 1973, 1985, 1997, 2009',
						'Tiger - 1974, 1986, 1998, 2010   Rabbit - 1975, 1987, 1999, 2011',
						'Dragon - 1976, 1988, 2000, 2012    Snake - 1977, 1989, 2001, 2013',
						'Horse - 1978, 1990, 2002, 2014      Ram - 1979, 1991, 2003, 2015',
						'Monkey - 1980, 1992, 2004, 2016  Rooster - 1981, 1993, 2005, 2017',
						'Dog - 1982, 1994, 2006, 2018      Pig - 1983, 1995, 2007, 2019');
				foreach ($zodiac as $key=>$sign) {
					$irc->message(SMARTIRC_TYPE_QUERY,$data->nick,$sign);
				}
		}else{
			$irc->message(SMARTIRC_TYPE_CHANNEL,$data->channel,$data->nick.': Please type a valid sign, or \'!zodiac help\'.');
		}
	}
}
?>
