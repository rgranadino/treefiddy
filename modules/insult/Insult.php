<?php
/**
 * Insult.php 
 * treefiddy insult module
 * @author Rolando Granadino <beeplogic@gmail.com>
 */

use Geekpunks\Common\Model;
use Geekpunks\Treefiddy\Irc_ChannelEvent;
use Geekpunks\Treefiddy\Irc;
use Geekpunks\Treefiddy\Bot;
use Geekpunks\Treefiddy\ModuleInterface;
/**
 * Insult Module
 * @author Rolando Granadino <beeplogic@gmail.com>
 */
class insult implements ModuleInterface {
    /**
     * insult module constructor
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $bot->registerEvent(Irc::EVENT_CHAN_MSG, array($this, 'chanHandler'));
    }
    /**
     * sends insult to victim on channel
     * @todo kick or insult nick if target is bot nick
     * @param Model $eventData
     */
    public function chanHandler(Model $eventData) 
    {
        if ($eventData instanceof Irc_ChannelEvent) {
            $message = $eventData->getMessage();
            if (preg_match('/^insult .*/', $message) != 1) {
                return;
            }
            $msg    = explode(' ', $message, 2);
            $insult = $this->_getInsult();
            $target = $msg[1] == 'me' ? $eventData->getNick() : $msg[1];
            $channel = $eventData->getChannel();
            $eventData->getIrc()->sendChannelMessage($channel,$target.$insult);
        }
    }
    /**
     * get insult string
     */
    protected function _getInsult()
    {
        $words = array(
                'adj' => array(
                        'acidic','antique','contemptible','culturally-unsound','despicable','evil','fermented','festering',
                        'foul','fulminating','humid','impure','inept','inferior','industrial','left-over','low-quality',
                        'malodorous','off-color','penguin-molesting','petrified','pointy-nosed','salty','sausage-snorfling',
                        'tastless','tempestuous','tepid','tofu-nibbling','unintelligent','unoriginal','uninspiring',
                        'weasel-smelling','wretched','spam-sucking','egg-sucking','decayed','halfbaked','infected','squishy',
                        'porous','pickled','coughed-up','thick','vapid','hacked-up','unmuzzled','bawdy','vain','lumpish',
                        'churlish','fobbing','rank','craven','puking','jarring','fly-bitten','pox-marked','fen-sucked','spongy',
                        'droning','gleeking','warped','currish','milk-livered','surly','mammering','ill-borne','beef-witted',
                        'tickle-brained','half-faced','headless','wayward','rump-fed','onion-eyed','beslubbering','villainous',
                        'lewd-minded','cockered','full-gorged','rude-snouted','crook-pated','pribbling','dread-bolted',
                        'fool-born','puny','fawning','sheep-biting','dankish','goatish','weather-bitten','knotty-pated',
                        'malt-wormy','saucyspleened','motley-mind','it-fowling','vassal-willed','loggerheaded','clapper-clawed',
                        'frothy','ruttish','clouted','common-kissing','pignutted','folly-fallen','plume-plucked','flap-mouthed',
                        'swag-bellied','dizzy-eyed','gorbellied','weedy','reeky','measled','spur-galled','mangled','impertinent',
                        'bootless','toad-spotted','hasty-witted','horn-beat','yeasty','imp-bladdereddle-headed','boil-brained',
                        'tottering','hedge-born','hugger-muggered','elf-skinned'
                ),
                'amt' => array(
                        'accumulation','bucket','coagulation','enema-bucketful','gob','half-mouthful',
                        'heap','mass','mound','petrification','pile','puddle','stack','thimbleful','tongueful',
                        'ooze','quart','bag','plate','ass-full'
                ),
                'noun' => array(
                        'bat toenails','bug spit','cat hair','chicken piss','dog vomit','dung',
                        "fat-woman's stomach-bile",'fish heads','guano','gunk','pond scum','rat retch',
                        'red dye number-9','Sun IPC manuals','waffle-house grits','yoo-hoo',
                        'dog balls','seagull puke','cat bladders','pus','urine samples',
                        'squirrel guts','snake assholes','snake bait','buzzard gizzards',
                        'cat-hair-balls','rat-farts','pods','armadillo snouts','entrails',
                        'snake snot','eel ooze','slurpee-backwash','toxic waste','Stimpy-drool',
                        'poopy','poop','craptacular carpet droppings','jizzum','cold sores','anal warts'
                )
        );
        $adj1 = $words['adj'][rand(0,(count($words['adj']) - 1))];
        $amt  = $words['amt'][rand(0,(count($words['amt']) - 1))];
        $adj2 = $words['adj'][rand(0,(count($words['adj']) - 1))];
        $noun = $words['noun'][rand(0,(count($words['noun']) - 1))];
        return ' is nothing but a '.$adj1.' '.$amt.' of '.$adj2.' '.$noun;
    }
}
