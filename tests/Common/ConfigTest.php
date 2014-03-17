<?php
use Geekpunks\Common\Config;
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Geekpunks\Common\Exception
     */
    public function testBadFilePath()
    {
        $config = new Config('/does/not/exist');
    }
    /**
     * @expectedException Geekpunks\Common\Exception
     */
    public function testBadFormat()
    {
        PHPUnit_Framework_Error_Warning::$enabled = false;
        $config = new Config('tests/Common/bad.ini');
        PHPUnit_Framework_Error_Warning::$enabled = true;
    }
    /**
     * @expectedException Geekpunks\Common\Exception
     */
    public function testValidateConfig()
    {
        $class = new ReflectionClass('Geekpunks\Common\Config');
        $method = $class->getMethod('_validateConfig');
        $method->setAccessible(true);
        $obj = new Config('tests/Common/required.ini');
        $method->invokeArgs($obj, array( array('this'=> array('is', 'required'))));
    }
    public function testConfigValue()
    {
        $obj = new Config('tests/Common/required.ini');
        $this->assertEquals('foo', $obj->getConfigValue('this', 'is'));
    }
}