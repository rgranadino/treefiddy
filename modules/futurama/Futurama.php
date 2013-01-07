<?php
/**
 * Futurama.php 
 * treefiddy futurama quotes module
 * @author Evan Brown <evan@bcin.net>
 */

use Geekpunks\Common\Model;
use Geekpunks\Treefiddy\Irc_ChannelEvent;
use Geekpunks\Treefiddy\Irc;
use Geekpunks\Treefiddy\Bot;
use Geekpunks\Treefiddy\ModuleInterface;
/**
 * futurama quotes module
 * @author Evan Brown <evan@bcin.net>
 */
class Futurama implements ModuleInterface {
    /**
     * quotes file location
     * @var str
     */
    protected $_quotesFile = '';
    /**
     * futurama module constructor 
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->_quotesFile = $bot->getModulesDir().'/futurama/quotes.txt';
        $bot->registerEvent(Irc::EVENT_CHAN_MSG, array($this, 'chanHandler'));
    }
    /**
     * sends quote to channel
     * @todo store in memory?
     * @param Model $eventData
     */
    public function chanHandler(Model $eventData) 
    {
        if ($eventData instanceof Irc_ChannelEvent) {
            $message = $eventData->getMessage();
            if (preg_match('/^!futurama/', $message) != 1) {
                return;
            }
            $quote = 'Could not read quotes file! :(';
            if (is_readable($this->_quotesFile)) {
                $quotes = file($this->_quotesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $quote  = $quotes[rand(0,(count($quotes)-0))];
            }
            $channel = $eventData->getChannel();
            $eventData->getIrc()->sendChannelMessage($channel, $quote);
        }
    }
}
