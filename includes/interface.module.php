<?php
/**
 * interface.module.php
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
/**
 * module interface
 *
 */
interface module {
    public function __construct(Net_SmartIRC &$irc, modules &$modules, config &$config);
}
?>