<?php
/**
 * Events.php - Events model
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Treefiddy;
use Geekpunks\Common\Model as Model;
/**
 * Events class for registering and dispatching events
 * @todo add unregister method
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Events {
    /**
     * list of event observers
     * @var array
     */
    protected $_observers = array();
    
    /**
     * register event listener
     * @param str $type
     * @param array $callback
     */
    public function register($type, callable $callback)
    {
        if (!isset($this->_observers[$type])) {
            $this->_observers[$type] = array();
        }
        if (!in_array($callback, $this->_observers[$type])) {
            $this->_observers[$type][] = $callback;
        }
    }
    /**
     * dispatch an event
     * @todo add try/catch block
     * @todo log/throw error if we can't run callback?
     * @param str $type
     * @param Model $data
     */
    public function dispatch($type, Model $data)
    {
        if (!empty($this->_observers[$type])) {
            foreach ($this->_observers[$type] as $callback) {
                if (is_callable($callback)) {
                    call_user_func($callback, $data); 
                }
            }
        }
    }
}