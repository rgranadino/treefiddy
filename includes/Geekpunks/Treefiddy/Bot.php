<?php
/**
 * Bot.php
 * Bot Model
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Treefiddy;
use Geekpunks\Common\Model;
/**
 * Bot class, orchestrates all supporting classes
 * and provides some helper/abstraction models for each 
 * component for the bot
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Bot {
    /**
     * the working directory for the bot
     * @var str
     */
    protected $_botDir = null;
    /**
     * Configuration instance
     * @var Config
     */
    protected $_config = null;
    /**
     * Irc model instance
     * @var Irc
     */
    protected $_irc    = null;
    /**
     * Events model instance
     * @var Events
     */
    protected $_events = null;
    
    /**
     * Modules model instance
     * @var Modules
     */
    protected $_modules = null;
    /**
     * constructure, setups up all the component models
     * @param str $botDir
     */
    public function __construct($botDir)
    {
        $this->_botDir  = $botDir;
        $this->_config  = new Config($botDir.'/config.ini');
        $this->_irc     = new Irc($this);
        $this->_events  = new Events($this);
        $this->_modules = new Modules($botDir.'/modules', $this); 
    }
    /**
     * register a bot event
     * @param str $type
     * @param array $callback
     */
    public function registerEvent($type, array $callback)
    {
        $this->_events->register($type, $callback);
    }
    /**
     * dispatch a bot event
     * @param unknown_type $type
     * @param Model $data
     */
    public function dispatchEvent($type, Model $data)
    {
        $this->_events->dispatch($type, $data);
    }
    /**
     * run bot, connects and starts listening/dispatchign events
     */
    public function run()
    {
        $this->_irc->connect();
        $this->_irc->listen();
    }
    /**
     * bet got directory
     * @return str
     */
    public function getBotDir()
    {
        return $this->_botDir;
    }
    
    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
}