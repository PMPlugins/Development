<?php

namespace SurPlugins\WorldStats;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\BlockBreakEvent;

class WorldStats extends PluginBase implements Listener{

      public function onEnable(){
		    $this->getServer()->getPluginManager()->registerEvents($this, $this);
			  if(!file_exists($this->getDataFolder())){
				  @mkdir($this->getDataFolder());
			  }
			  $this->sign = new Config($this->getDataFolder() . "settings.yml", Config::YAML, array
            (
			    "sign_trigger" => "worldstats",
				"sign_final_change" => "[World Status]",
            )
        );
        $this->getLogger()->info(TextFormat::BLUE . "WorldStats has been enabled!");
			  $this->getServer()->getScheduler()->scheduleRepeatingTask($task = new SignUpdateTask($this), 40);
			  $this->taskid = $task->getTaskId();
      }
		
		public function onSignChange(SignChangeEvent $event){
			$player = $event->getPlayer();
				if($player->isOp()){
					if(TextFormat::clean(strtolower(trim($event->getLine(0)))) === strtolower(trim($this->sign->get("sign_trigger"))) || TextFormat::clean(strtolower(trim($event->getLine(0)))) === strtolower(trim($this->sign->get("sign_final_change")))){
						$world = $event->getLine(1);
						$level = $this->getServer()->getLevelByName($world);
						if($level instanceof Level){
						  $players = count($level->getPlayers());
						  $event->setLine(0, TextFormat::GREEN . $this->sign->get("sign_final_change"));
						  $event->setLine(1, TextFormat::YELLOW . $players . TextFormat::RED . " players");
					  }else{
					    $event->setLine(0, TextFormat::DARK_RED . "ERROR: WORLDNAME");
					  }
					}
				}else{
					$player->sendMessage(TextFormat::RED . "You do not have permission to create a World Status sign.");
					$event->setCancelled(true);
				}
		}
		
		public function onSignBreak(BlockBreakEvent $event){
		
		}
}
