<?php
/**
 * Irc.php
 * IRC wrapper class help abstract dependency of SmartIRC class
 * @todo remove coupling with Bot class, replace with setters? - this would be an overall approach
 */
namespace Geekpunks\Treefiddy;
use Geekpunks\Common\Model as Model;
/**
 * Irc wrapper model
 * abstracts our IRC components away
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Irc {
    /**
     * event constants
     */
    const EVENT_CHAN_MSG = 'irc_chan_msg';
    /**
     * 
     * @var \Net_SmartIRC
     */
    protected $_irc = null;
    /**
     * Bot instance
     * @var Bot
     */
    protected $_bot = null;
    /**
     * load Smart IRC class
     * @todo listen for other events
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        require_once $bot->getBotDir().'/includes/SmartIRC/SmartIRC.php';
        $this->_bot = $bot;
        $this->_irc = new \Net_SmartIRC();
        $this->_irc->setDebug(SMARTIRC_DEBUG_ACTIONHANDLER);
        $this->_irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '.*', $this, 'channelHandler');
    }
    /**
     * connect to IRC server
     */
    public function connect()
    {
        //config values
        $config          = $this->_bot->getConfig();
        //server details
        $serverName      = $config->getConfigValue('server', 'name');
        $serverPort      = $config->getConfigValue('server', 'port');
        $nickServNick    = $config->getConfigValue('server', 'nickserv_nick');
        $nickServCmd     = $config->getConfigValue('server', 'nickserv_cmd');
        $channels        = $config->getChannels();
        //bot details
        $botNick         = $config->getConfigValue('bot', 'nick');
        $botName         = $config->getConfigValue('bot', 'name');
        $botRealName     = $config->getConfigValue('bot', 'real_name');
        //misc
        $smartIrcMode    = $config->getConfigValue('smartIRC', 'mode');
        $useSockets      = $config->getConfigValue('smartIRC', 'sockets');
        
        $this->_irc->setChannelSyncing(true);
        $this->_irc->setUseSockets($useSockets);
        
        //@todo this'll get added to config.inc eventually
        $this->_irc->setCtcpVersion("Treefiddy v2.0.0 - PHP5.3");
        //connect
        $this->_irc->connect($serverName, $serverPort);
        $this->_irc->login($botNick, $botNick, $smartIrcMode, $botName);
        
        //nick serv stuff
        if (!empty($nickServNick) && !empty($nickServCmd)) {
            $this->_irc->message(SMARTIRC_TYPE_QUERY, $nickServNick, $nickServCmd);
        }
        $this->_irc->join($channels);
    }
    /**
     * beging listening
     */
    public function listen()
    {
        $this->_irc->listen();
        $this->_irc->disconnect();
    }
    /**
     * Channel message handler, dispatches bot event
     * @todo implement IRCEventData to allow for easier handlers? would reduce checks for if $model->getData('irc') and can support getters
     * @param \Net_SmartIRC $irc
     * @param \Net_SmartIRC_data $data
     */
    public function channelHandler(\Net_SmartIRC $irc, \Net_SmartIRC_data $data)
    {
        $dataModel = new Irc_ChannelEvent($this);
        $msgData   = array(
                'from'    => $data->from,
                'nick'    => $data->nick,
                'ident'   => $data->ident,
                'host'    => $data->host,
                'channel' => $data->channel,
                'message' => $data->message,
                'raw'     => $data->rawmessage,
        );
        $dataModel->addData($msgData);
        $this->_bot->dispatchEvent(self::EVENT_CHAN_MSG, $dataModel);
    }
    /**
     * send channel message
     * @param str $channel
     * @param str $message
     */
    public function sendChannelMessage($channel, $message)
    {
        $this->_irc->message(SMARTIRC_TYPE_CHANNEL, $channel, $message);
    }
    /**
     * send private message
     * @param str $nick
     * @param str $message
     */
    public function sendPrivateMessage($nick, $message)
    {
        $this->_irc->message(SMARTIRC_TYPE_QUERY, $nick, $message);
    }
}