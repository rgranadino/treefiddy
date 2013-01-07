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
class Irc_ChannelEvent extends Model {
    
    /**
     * related IRC model for event
     * @var Irc
     */
    protected $_irc = null;
    /**
     * constructor
     * @param Irc $irc
     */
    public function __construct(Irc $irc)
    {
        $this->_irc = $irc;
    }
    /**
     * get IRC model for this event
     * @return \Geekpunks\Treefiddy\Irc
     */
    public function getIrc()
    {
        return $this->_irc;
    }
    /**
     * get the from string for the channel message
     * @return str
     */
    public function getFrom()
    {
        return $this->getData('from');
    }
    /**
     * get nick which sent the message
     * @return str
     */
    public function getNick()
    {
        return $this->getData('nick');
    }
    /**
     * get ident of channel message sender
     * @return str
     */
    public function getIdent()
    {
        return $this->getData('ident');
    }
    /**
     * get host of channel message sender
     * @return str
     */
    public function getHost()
    {
        return $this->getData('host');
    }
    /**
     * get channel that triggered event
     * @return str
     */
    public function getChannel()
    {
        return $this->getData('channel');
    }
    /**
     * get channel message
     * @return str
     */
    public function getMessage()
    {
        return $this->getData('message');
    }
    /**
     * get raw message
     * @return str
     */
    public function getRawMessage()
    {
        return $this->getData('raw');
    }
}