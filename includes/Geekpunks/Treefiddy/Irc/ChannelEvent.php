<?php
/**
 * EventData.php
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Treefiddy;
use Geekpunks\Common\Model;
/**
 * IRC Event data model container
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Irc_ChannelEvent extends Irc_MessageEvent {
    /**
     * get channel that triggered event
     * @return str
     */
    public function getChannel()
    {
        return $this->getData('channel');
    }
}