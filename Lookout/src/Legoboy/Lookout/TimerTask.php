<?php
namespace Legoboy\Lookout;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use Legoboy\Lookout\Loader;

class TimerTask extends PluginTask{
	
    public function __construct(Loader $plugin, $totaltime){
        $this->plugin = $plugin;
		$this->totaltime = $totaltime;
	    parent::__construct($plugin);
    }
	
    public function onRun($currentTick){
	    $this->endingtime = $this->plugin->starttime + $this->totaltime;
	    $this->timeleft = $this->endingtime - time();
		if($this->timeleft % 10 === 0){
			$this->plugin->sendGameMessage($this->timeleft . " seconds left to end!");
		}
	    if($this->timeleft <= (int) $this->plugin->setting->get("time_to_last_countdown")){ 
            $this->plugin->sendGameMessage($this->timeleft . " seconds left to end!");
        }
		if($this->timeleft <= 0){
			$this->plugin->endGame();
		}
    }
}