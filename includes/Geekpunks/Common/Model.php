<?php
/**
 * Model.php - Contains Basic Model class
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Common;
/**
 * Basic model class with basic getter/setter methods
 * used to encapsulate data structure vs using an array
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class Model {
    /**
     * data array
     * @var array
     */
    protected $data = array();
    
    /**
     * get data for a given $key
     * @param unknown_type $key
     */
    public function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }
    /**
     * add data from an array to object 
     * @param array $data
     * @return \Geekpunks\Common\Model
     */
    public function addData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
        return $this;
    }
    /**
     * set a key/value pair on the model
     * @param string $key
     * @param mixed $value
     * @return \Geekpunks\Common\Model
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
}