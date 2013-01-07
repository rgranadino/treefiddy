<?php
/**
 * Horoscope.php
 * Contains horoscope module class
 * @author Rolando Granadino <beeplogic@gmail.com>
 * @todo clean up!
 */
use Geekpunks\Common\Model;
use Geekpunks\Treefiddy\Irc_ChannelEvent;
use Geekpunks\Treefiddy\Irc;
use Geekpunks\Treefiddy\Bot;
use Geekpunks\Treefiddy\ModuleInterface;
/**
 * horoscope module
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Horoscope implements ModuleInterface {
    /**
     * horoscope module constructor
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $bot->registerEvent(Irc::EVENT_CHAN_MSG, array($this, 'chanHandler'));
    }
    /**
     * channel message event handler
     * @param Model $eventData
     */
    function chanHandler(Model $eventData)
    {
        if ($eventData instanceof Irc_ChannelEvent) {
            $message = $eventData->getMessage();
            if (preg_match('/^!(zodiac|horoscope) .*/', $message) != 1) {
                return;
            }
            $signs   = array('aquarius','aries','cancer','capricorn','gemini','leo','libra','pisces','sagittarius','scorpio','taurus','virgo');
            $chinese = array('rat','ox','tiger','rabbit','dragon','snake','horse','goat','monkey','rooster','dog','pig');
            $message = $eventData->getMessage();
            $channel = $eventData->getChannel();
            $nick    = $eventData->getNick();
            $irc     = $eventData->getIrc();
            $usign   = explode(' ', $message, 2);
            //@todo strtolower[1] here
            if (in_array(strtolower($usign[1]), $signs)) {
                $usign[1] = strtolower($usign[1]);
                $url      = 'http://widgets.fabulously40.com/horoscope.json?sign='.$usign[1];
                $fp       = file_get_contents($url);
                 if ($fp) {
                    $json = json_decode($fp);
                    $msg  = $json->{'horoscope'}->{'horoscope'};
                    $msg  = $nick.': '.$json->{'horoscope'}->{'sign'}.' - '.$msg;
                    if (strlen($msg) > 430) {//@TODO move this into irc model!
                        $msgs = str_split($msg,430);
                        foreach ($msgs as $key => $item) {
                            $irc->sendChannelMessage($channel, $item);
                        }
                    } else {
                         $irc->sendChannelMessage($channel,$msg);
                    }
                 } else {
                     $irc->sendChannelMessage($channel, $nick.': Something went horribly wrong. Sorry, no horoscope for you!');
                 }
            } elseif (in_array(strtolower($usign[1]),$chinese)) {
                $irc->sendChannelMessage($channel, $nick.', The chinese refuse to give us their horoscopes at the time being. Check back later.');
            } elseif (trim($usign[1]) == 'help') {
                $irc->sendChannelMessage($channel, $nick.', Snyax: help [zodiac|chinese]');
            } elseif (trim($usign[1]) == 'help zodiac') {
                $zodiac = array('Capricorn - Dec. 22nd ~ Jan. 19th     Aquarius - Jan. 20th ~ Feb. 18th',
                        'Pisces - Feb. 19th ~ Mar. 20th        Aries - Mar. 21st ~ Apr. 19th',
                        'Taurus - Apr. 20th ~ May  20th       Gemini - May  21st ~ Jun. 21st',
                        'Cancer - Jun. 22nd ~ Jul. 22nd          Leo - Jul. 23rd ~ Aug. 22nd',
                        'Virgo - Aug. 23rd ~ Sep. 22nd        Libra - Sep. 23rd ~ Oct. 22nd',
                        'Scorpio - Oct. 23rd ~ Nov. 21st  Sagittarius - Nov. 22nd ~ Dec. 21st');
                foreach ($zodiac as $key=>$sign) {
                    $irc->sendPrivateMessage($nick, $sign);
                }
            } elseif (trim($usign[1]) == 'help chinese') {
                $zodiac = array('Rat - 1972, 1984, 1996, 2008       Ox - 1973, 1985, 1997, 2009',
                        'Tiger - 1974, 1986, 1998, 2010   Rabbit - 1975, 1987, 1999, 2011',
                        'Dragon - 1976, 1988, 2000, 2012    Snake - 1977, 1989, 2001, 2013',
                        'Horse - 1978, 1990, 2002, 2014      Ram - 1979, 1991, 2003, 2015',
                        'Monkey - 1980, 1992, 2004, 2016  Rooster - 1981, 1993, 2005, 2017',
                        'Dog - 1982, 1994, 2006, 2018      Pig - 1983, 1995, 2007, 2019');
                foreach ($zodiac as $key=>$sign) {
                    $irc->sendPrivateMessage($nick, $sign);
                }
            } else {
                $irc->sendChannelMessage($channel, $nick.': Please type a valid sign, or \'!zodiac help\'.');
            }
        }
    }
}
