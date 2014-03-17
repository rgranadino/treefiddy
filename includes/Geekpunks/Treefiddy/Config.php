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