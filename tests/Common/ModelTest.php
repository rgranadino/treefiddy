<?php
use Geekpunks\Common\Model;
class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testSetData()
    {
        $model = new Model();
        $model->setData('test', 'foo');
        $this->assertEquals('foo', $model->getData('test'));
    }
    public function testAddDataArray()
    {
        $model = new Model();
        $model->addData(array('test' => 'foo', 'testing' => 'bar'));
        $this->assertEquals('foo', $model->getData('test'));
        $this->assertEquals('bar', $model->getData('testing'));
    }
    public function testNullData()
    {
        $model = new Model();
        $this->assertNull($model->getData('test'));
    }
}