<?php
use Geekpunks\Treefiddy\Events;
use Geekpunks\Common\Model;
class EventsTest extends PHPUnit_Framework_TestCase
{
    public function testEvents()
    {
        $events = new Events();
        $data   = new Model();
        $data->setData('foo', 'bar');
        $data->setData('tree', 'fiddy');
        $events->register('test', function($data) {
            $this->assertInstanceOf('Geekpunks\Common\Model', $data);
            $this->assertEquals('bar', $data->getData('foo'));
        });
        //test multiple callbacks
        $events->register('test', function($data) {
            $this->assertInstanceOf('Geekpunks\Common\Model', $data);
            $this->assertEquals('fiddy', $data->getData('tree'));
        });
        $events->dispatch('test', $data);
    }
}