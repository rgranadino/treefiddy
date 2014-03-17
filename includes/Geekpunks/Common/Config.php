<?php
/**
 * Config.php - simple common config class
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Common;
/**
 * Common Config class
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Config {

    /**
     * file path location
     * @var str
     */
    protected $filePath = null;
    
    /**
     * config array
     * @var array
     */
    protected $config   = null;
    
    /**
     * constructor
     * @param str $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->_loadConfig();
    }

    /**
     * load configuration file
     * @throws Exception
     */
    protected function _loadConfig()
    {
        $configPath = $this->filePath;
        if (!is_readable($configPath)) {
            throw new Exception("Cannot read config file: {$configPath}");
        }
        $config         = parse_ini_file($configPath, true);
        if ($config === false) {
            throw new Exception("Failed to parse ini file: {$configPath}");
        }
        $this->config = $config;
    }
    /**
     * validate config against an array of sections and keys
     * e.g
     * array(
     *   'bot' => array('nick', 'real_name'),
     *   'foo' => array('bar', 'baz')
     * ) 
     * @param array $requiredConfig
     * @throws Exception
     */
    protected function _validateConfig(array $requiredConfig)
    {
        $config = $this->config;
        foreach ($requiredConfig as $sectionKey => $configSection) {
            foreach ($configSection as $configKey) {
                if (!isset($config[$sectionKey][$configKey])) {
                    throw new Exception("Missing Config value: [{$sectionKey}][{$configKey}]");
                }
            }
        }
    }
    
    /**
     * get config value by section and key
     * @param str $section
     * @param str $key
     * @return str | NULL
     */
    public function getConfigValue($section, $key)
    {
        $return = null;
        if (isset($this->config[$section]) && isset($this->config[$section][$key])) {
            return $this->config[$section][$key];
        }
        return $return;
    }
}
