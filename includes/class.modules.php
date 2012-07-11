<?php
/**
 * class.modules.php
 * Loads bot's modules
 * @author Rolando Granadino <beeplogic@gmail.com>
 */

/**
 * modules class
 * @author Rolando Granadino <beeplogic@gmail.com>
 */

class modules {
    /**
     * List of module objects
     *
     * @var array
     * @throws exception
     */
    public $modules = array();
    /**
     * Loads modules for irc bot
     *
     * @param config $config
     * @param Net_SmartIRC $smartIRC
     * @param str $botDir
     */
    public function __construct(config &$config, Net_SmartIRC &$smartIRC) {
        $moduleDir = $config->botDir.'/modules';
        if (!is_readable($moduleDir)) {
            throw new Exception('Cannot read module path.');
        }
        $d = dir($moduleDir);
        while (false !== ($entry = $d->read())) {
            $modulePath = $moduleDir.'/'.$entry;
            $moduleFile = "$modulePath/class.{$entry}.php";            
            if ($entry != '.' && $entry != '..' && is_dir($modulePath) && is_file($moduleFile)) {
                include_once($moduleFile);
                eval('$this->modules[\''.$entry.'\'] = new '.$entry.'($smartIRC,$this,$config);');
            }
        }
    }
}
?>