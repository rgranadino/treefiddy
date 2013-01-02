<?php
/**
 * Module Interface
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
namespace Geekpunks\Treefiddy;
/**
 * Basic interface all modules must implement
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
interface ModuleInterface {
    public function __construct(Bot $bot);
}