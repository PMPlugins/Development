<?php

namespace Legoboy\Lookout;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use Legoboy\Lookout\Loader;

class TimerTask extends PluginTask{
	
    public function __construct(Loader $plugin, $gametime, $waittime){
        $this->plugin = $plugin;
		$this->gametime = $gametime;
		$this->waittime = $waittime;
	    parent::__construct($plugin);
    }
	
    public function onRun($currentTick){
		$this->totaltime = $this->waittime + $this->gametime;
	    $this->endingtime = $this->plugin->starttime + $this->totaltime;
	    $this->timeleft = $this->endingtime - time();
		$this->timepassed = $this->totaltime - $this->timeleft;
		$this->plugin->getLogger()->info($this->timepassed . " seconds past, " . $this->timeleft . " seconds left.");
		if($this->timepassed >= $this->waittime){
			if(count($this->plugin->players) < (int) $this->plugin->setting->get("min_players")){
				$this->plugin->gamestatus = 0;
				$this->plugin->sendGameMessage("Not enough players!");
				$this->plugin->restartGame();
			}else{
				$this->plugin->gamestatus = 1;
				$this->plugin->sendGameMessage("Game starts!");
			}
		}
		if($this->timeleft % 10 === 0 && $this->plugin->gamestatus === 1){
			$this->plugin->sendGameMessage($this->timeleft . " seconds left to end!");
		}
	    if($this->timeleft <= (int) $this->plugin->setting->get("time_to_last_countdown") && $this->plugin->gamestatus === 1){ 
            $this->plugin->sendGameMessage($this->timeleft . " seconds left to end!");
        }
		if($this->timeleft <= 0){
			$this->plugin->sendGameMessage("Game ended without winners!");
			$this->plugin->endGame();
		}
    }
}