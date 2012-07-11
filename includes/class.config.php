<?php
/**
 * class.config.php
 * Contains class for loading configuratino
 * @author Rolando Granadino <beeplogic@gmail.com>
 * @package treefiddy
 */
/**
 * config class for treefiddy
 */
class config {
    //bot's personal settings
    /**
     * Bot's Nick
     *
     * @var str
     */
    public $nick   = '';
    /**
     * Bot's Name
     *
     * @var str
     */
    public $name    = '';
    /**
     * Bot's Real Name
     *
     * @var str
     */
    public $realName = '';
    
    //server settings
    /**
     * Server Name
     *
     * @var str
     */
    public $server   = '';
    /**
     * Port to connect to server on
     *
     * @var int
     */
    public $port     = 6667;
    /**
     * List of channels to join
     *
     * @var array
     */
    public $chans    = array();
    /**
     * Whether or not to rejoin after being kicked
     *
     * @var bool
     */
    public $rejoin   = false;
    
    //public $reconnect = false;
    /**
     * Nickserv nick
     *
     * @var str
     */
    public $nickservNick = '';
    /**
     * command to send to nickserv on connect
     *
     * @var str
     */
    public $nickserveCmd = '';
    
    //SmartIRC settings
    /**
     * SmartIRC mode
     *
     * @var int
     */
    public $mode = 8;
    /**
     * Whether or not to use sockets (requires socket extension)
     *
     * @var bool
     */
    public $useSockets   = true;
    
    //admin settings
    /**
     * List of admin nicks
     *
     * @var array
     */
    public $master       = array();
    /**
     * Master password used by master nicks
     *
     * @var str
     */
	public $password     = '';
    /**
     * Contains bot's path
     *
     * @var str
     */
	public $botDir       = '';
    /**
     * Contains config in array format
     * @var array
     */
    public $config       = array();
    
	//functions
	/**
	 * The constructor loads the config file and populates values
	 *
	 * @param str $configPath
	 * @throws Exception
	 */
	public function __construct($botDir)
	{
	    $configPath = $botDir.'/config.ini';
	    if (!is_readable($configPath)) {
	        throw new Exception("Cannot read config file: {$configPath} .");
	    }
	    $config         = parse_ini_file($configPath,true);	    
	    $requiredConfig = array('bot'=>array('nick','name','real_name'), 'server'=>array('name'));
	    foreach ($requiredConfig as $sectionKey => $configSection) {
	        foreach ($configSection as $configKey) {
	            if (!isset($config[$sectionKey][$configKey])) {
	                throw new Exception("Missing Config value: [{$sectionKey}][$configKey]");
	            }
	        }
	    }
	    $this->botDir           = $botDir;
	    //bot config
	    $botConfig              = $config['bot'];
	    $this->name             = $botConfig['name'];
	    $this->nick             = $botConfig['nick'];
	    $this->realName         = $botConfig['real_name'];
	    //server config
	    $serverConfig           = $config['server'];
	    $this->server           = $serverConfig['name'];
	    
	    if (isset($serverConfig['port']) && !empty($serverConfig['port'])) {
	        $this->port         = (int) $serverConfig['port'];
	    }
	    if (isset($serverConfig['chans'])) {
	        $this->chans        = explode(',',$serverConfig['chans']);
	    }
	    if (isset($serverConfig['auto_rejoin']) && strtolower($serverConfig['auto_rejoin']) == 'true') {
	        $this->rejoin       = true;
	    }
	    if (isset($serverConfig['nickserv_nick']) && !empty($serverConfig['nickserv_nick'])) {
	        $this->nickservNick = $serverConfig['nickserv_nick'];
	    }
	    if (isset($serverConfig['nickserv_cmd']) && !empty($serverConfig['nickserv_cmd'])) {
	        $this->nickservCmd = $serverConfig['nickserv_cmd'];
	    }
        unset($config['bot']);
	    //smartIRC settings
	    $smartIrcConfig         = $config['smartIRC'];
	    if (isset($smartIrcConfig['mode']) && !empty($smartIrcConfig['mode'])) {
	        $this->smartIRCmode = (int) $smartIrcConfig['mode'];
	    }
	    if (isset($smartIrcConfig['sockets']) && strtolower($smartIrcConfig['sockets']) == 'false') {
	        $this->useSockets   = false;
	    }
        unset($config['smartIRC']);
	    //admin section
	    $adminConfig            = $config['admin'];
	    if (isset($adminConfig['nicks'])) {
	        $this->master       = explode(',',$adminConfig['nicks']);
	    }
	    if (isset($adminConfig['password']) && !empty($adminConfig['password'])) {
	        $this->password     = $adminConfig['password'];
	    }
        unset($config['admin']);
        //leave any set data for module use
        $this->config          = $config;
	}
}
?>
