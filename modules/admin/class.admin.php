<?php
/**
 * class.admin.php
 * @author Rolando Granadino <beeplogic@gmail.com>
 *
 */
/**
 * admin module
 * @author Rolando Granadino <beeplogic@gmail.com>
 * @todo write help messages
 */
//when the bot has ops on a chan it'll allow it perform certain op functions
//functionality will be added over time...at least thats the plan
//functions should be in the following form /msg bot command arg password

class admin implements module {
    /**
     * Contains config object
     *
     * @var config
     */
    private $config = null;
    /**
     * constructor sets up action handlers
     *
     * @param Net_SmartIRC $irc
     * @param modules $modules
     * @param config $config
     */
	public function __construct(Net_SmartIRC &$irc, modules &$modules, config &$config)
	{
	    $this->config = &$config;
        $irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^join .*', $modules->modules['admin'], 'admin_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^op .*', $modules->modules['admin'], 'admin_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^deop .*', $modules->modules['admin'], 'admin_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^\+m .*', $modules->modules['admin'], 'admin_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^-m .*', $modules->modules['admin'], 'admin_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^kick .*', $modules->modules['admin'], 'admin_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '^part .*', $modules->modules['admin'], 'admin_func');
        $irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^!list .*',$modules->modules['admin'], 'kicknOOb');
        $irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '^!list',$modules->modules['admin'], 'kicknOOb');
	}
	/**
	 * Handles Admin/op functionality
	 *
	 * @param Net_SmartIRC $irc
	 * @param Net_SmartIRC_data $data
	 */
	function admin_func(Net_SmartIRC &$irc, Net_SmartIRC_data &$data){
		//cmd pass args
		//$args[0] $args[1] $args[2]
		$args = explode(' ',$data->message,3);
		if($args[1]==$this->config->password && in_array($data->nick,$this->config->master)) {
			switch ($args[0]) {
				case 'join':
				//arg==channel, will add array in later versoin
					$irc->join(array($args[2]));
				break;

				case 'part':
				//arg==channel, will add array in later versoin
					$irc->part(array($args[2]));
				break;

				case 'op':
				//args== channel nick
					$opargs=explode(' ',$args[2],2);
					$irc->op($opargs[0],$opargs[1]);
				break;

				case 'deop':
				//args== channel nick
					$opargs=explode(' ',$args[2],2);
					$irc->deop($opargs[0],$opargs[1]);
				break;

				case '+m':
					$irc->mode($args[2],'+m');
				break;

				case '-m':
					$irc->mode($args[2],'-m');
				break;

				case 'kick':
				//chan nick reason
					$kickargs=explode(' ',$args[2],3);
					$irc->kick($kickargs[0],$kickargs[1],($kickargs[2]!=''?$kickargs[2]:'treefiddy'));
				break;

				default:
					$irc->message(SMARTIRC_TYPE_QUERY,$data->nick,': Please type a valid command');
				break;
			}
		}else{
			$irc->message(SMARTIRC_TYPE_QUERY,$data->nick,'Invalid password: '.$args[1].' nick/pass');
		}
	}
	/**
	 * Hack used to kick n00bs
	 *
	 * @param Net_SmartIRC $irc
	 * @param Net_SmartIRC_data $data
	 */
	function kicknOOb(Net_SmartIRC &$irc, Net_SmartIRC_data &$data)
	{
		$irc->kick($data->channel,$data->nick,'http://disturbedyouth.org/geekpunks');
	}
}
?>
