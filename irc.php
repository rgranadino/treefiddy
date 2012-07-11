<?php
/**
 * @author Rolando Granadino <beeplogic@gmail.com>
 * 
 */
//gets scripts path
$botDir = dirname(__FILE__);
//load config
require_once($botDir.'/includes/class.config.php');
try {
    $config = new config($botDir);
} catch (Exception $error){
    echo $error->getMessage()."\n";
    exit(1);
}
//load SmartIRC
require_once($botDir.'/includes/SmartIRC/SmartIRC.php');
//instantiate SmartIRC
$irc = &new Net_SmartIRC();

try {
    require_once($botDir.'/includes/class.modules.php');
    require_once($botDir.'/includes/interface.module.php');
    $modules = new modules($config,$irc);
} catch (Exception $error) {
    echo $error->getMessage()."\n";
    exit(1);
}

$irc->setDebug(SMARTIRC_DEBUG_IRCMESSAGES); //this'll get added to config.inc eventually
$irc->setUseSockets($config->useSockets); 
$irc->setChannelSynching(TRUE);
$irc->setCtcpVersion("Treefiddy v0.8.4 - PHP5");
$irc->connect($config->server, $config->port);
$irc->login($config->nick,$config->realName, $config->mode,$config->name);
//nick serv stuff
if (!empty($config->nickservNick) && !empty($config->nickservCmd)) {
    $irc->message(SMARTIRC_TYPE_QUERY,$config->nickservNick,$config->nickservCmd);
}
$irc->join($config->chans);
$irc->listen();
$irc->disconnect();
?>