<?php
/**
 * Config.php
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Treefiddy;
use Geekpunks\Common\Config as CommonConfig;
/**
 * Bot config class
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Config extends CommonConfig {
    /**
     * Bot's Nick
     * @todo write a wrapper method for this or remove completey
     * @var str
     */
    protected $_nick   = '';
    /**
     * Bot's Name
     * @todo write a wrapper method for this or remove completey
     * @var str
     */
    protected $_name    = '';
    /**
     * Bot's Real Name
     * @todo write a wrapper method for this or remove completey
     * @var str
     */
    protected $_realName = '';
    
    //server settings
    /**
     * Server Name
     * @todo write a wrapper method for this or remove completey
     * @var str
     */
    protected $_server   = '';
    /**
     * Port to connect to server on
     * @todo write a wrapper method for this or remove completey
     * @var int
     */
    protected $_port     = 6667;
    /**
     * List of channels to join
     *
     * @var array
     */
    protected $_chans    = array();
    /**
     * Whether or not to rejoin after being kicked
     * @todo write a wrapper method for this or remove completey
     * @var bool
     */
    protected $_rejoin   = false;
    
    /**
     * Nickserv nick
     * @todo write a wrapper method for this or remove completey
     * @var str
     */
    protected $_nickservNick = '';
    /**
     * command to send to nickserv on connect
     * @todo write a wrapper method for this or remove completey
     * @var str
     */
    protected $_nickserveCmd = '';
    
    /**
     * constructor
     * @param str $configPath
     */
    public function __construct($configPath)
    {
        parent::__construct($configPath);
        $requiredConfig = array('bot'=>array('nick','name','real_name'), 'server'=>array('name'));
        $this->_validateConfig($requiredConfig);
    }
    /**
     * get list of channels to join
     * @todo return an array instead
     * @return str
     */
    public function getChannels()
    {
        return $this->getConfigValue('server', 'chans');
    }
}