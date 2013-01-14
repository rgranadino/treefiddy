<?php
/**
 * Admin.php
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
use Geekpunks\Treefiddy\Irc_MessageEvent;

use Geekpunks\Common\Model;
use Geekpunks\Treefiddy\Irc_ChannelEvent;
use Geekpunks\Treefiddy\Irc;
use Geekpunks\Treefiddy\Bot;
use Geekpunks\Treefiddy\ModuleInterface;
/**
 * admin module
 * when the bot has ops on a chan it'll allow it perform certain op functions
 * functionality will be added over time...at least thats the plan ("still")
 * functions should be in the following form /msg bot command arg password
 * @author Rolando Granadino <beeplogic@gmail.com>
 * @todo write help messages
 */
class Admin implements ModuleInterface {
    
    /**
     * constructor sets up event handlers
     *
     * @param Net_SmartIRC $irc
     * @param modules $modules
     * @param config $config
     */
    public function __construct(Bot $bot)
    {
        $bot->registerEvent(IRC::EVENT_PRIV_MSG, array($this, 'msgHandler'));
    }
    /**
     * admin message handler
     * all commands use the following structure:
     * ^cmd password some value
     * @todo some sort of help message/validation for each command?
     * @param Model $eventData
     */
    public function msgHandler(Model $eventData)
    {
        if ($eventData instanceof Irc_MessageEvent) {
            $adminCommands = array(
                'join',
                'op',
                'deop',
                '\+m',
                '-m',
                'kick',
                'part'
            );
            $message   = $eventData->getMessage();
            $pattern   = implode('|', $adminCommands);
            //@todo pattern will print out regex escapes too...
            $helpMessage = 'Please type a valid command: '.$pattern;
            $irc         = $eventData->getIrc();
            if (preg_match('/^('.$pattern.')/', $message) == 1) {
                $args     = explode(' ', $message, 3);
                $config   = $irc->getBot()->getConfig();
                $password = $config->getConfigValue('admin', 'password');
                $nicks    = $config->getConfigValue('admin', 'nicks');
                if ($args[1] == $password && in_array($eventData->getNick(), $nicks)) {
                    switch ($args[0]) {
                        case 'join'://arg==channel, @todo will add array in later versoin
                            $irc->joinChannel($args[2]);
                            break;
                        case 'part'://arg==channel, @todo will add array in later versoin
                            $irc->partChannel($args[2]);
                        break;
                        case 'op':
                            $opargs = explode(' ', $args[2], 2);
                            $irc->op($opargs[0], $opargs[1]);
                            break;
                        case 'deop':
                            $opargs = explode(' ', $args[2], 2);
                            $irc->deOp($opargs[0], $opargs[1]);
                            break;
                        case '+m':
                            $irc->setMode($args[2], '+m');
                            break;
                        case '-m':
                            $irc->setMode($args[2], '-m');
                            break;
                        case 'kick':
                            $kickargs = explode(' ', $args[2], 3);
                            $reason   = (!empty($kickargs[2]) ? $kickargs[2] : 'treefiddy');
                            $irc->kick($kickargs[0], $kickargs[1], $reason);
                            break;
                    }
                } else {
                    $irc->sendPrivateMessage($eventData->getNick(), 'Invalid password: '.$args[1].' nick/pass');
                }
            } else {
                $irc->sendPrivateMessage($eventData->getNick(), $helpMessage);
            }
        }
    }
}
