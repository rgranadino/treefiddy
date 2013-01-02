<?php
/**
 * bot.php - Treefiddy bootstrap
 */
use Geekpunks\Treefiddy\Bot;
//@todo remove these?
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('America/Los_Angeles');

$botDir = dirname(__FILE__);
require_once $botDir.'/includes/SplClassLoader.php';

$loader = new SplClassLoader('Geekpunks', $botDir.'/includes');
$loader->register();
try {
    $bot = new Bot($botDir);
    $bot->run();
} catch (Exception $e) {
    echo $e->getMessage()."\n";
    exit(1);
}
