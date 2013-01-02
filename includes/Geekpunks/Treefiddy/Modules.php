<?php
/**
 * Modules.php
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Treefiddy;
use Geekpunks\Common\Exception as Exception;
/**
 * Modules class to load bot modules based on a convention
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Modules {
    /**
     * list of modules
     * @var array
     */
    protected $_modules = array();
    /**
     * Module directory
     * @var str
     */
    protected $_dir     = null;
    /**
     * 
     * @var Bot
     */
    protected $_bot     = null;
    /**
     * constructor
     * @param str $directory
     * @param Bot $bot
     */
    public function __construct($directory, Bot $bot)
    {
        $this->_dir = $directory;
        $this->_bot = $bot;
        $this->_loadModules();
    }
    /**
     * load all possible modules
     * @todo make sure we don't have duplicate modules, e.g. fooBar/Foobar.php and FooBar/Foobar.php
     * @throws Exception
     */
    protected function _loadModules()
    {
        $moduleDir = $this->_dir;
        if (!is_readable($moduleDir)) {
            throw new Exception('Cannot read module path.');
        }
        $dir = dir($moduleDir);
        while (false !== ($entry = $dir->read())) {
            $className  = ucfirst(strtolower($entry));
            $modulePath = $moduleDir.'/'.$entry;
            $moduleFile = "$modulePath/{$className}.php";
            if ($entry != '.' && $entry != '..' && is_dir($modulePath) && is_file($moduleFile)) {
                include_once $moduleFile;
                if (class_exists($className) && in_array(__NAMESPACE__.'\ModuleInterface', class_implements($className))) {
                    $this->_modules[$entry] = new $className($this->_bot);
                }
            }
        }
    }
}