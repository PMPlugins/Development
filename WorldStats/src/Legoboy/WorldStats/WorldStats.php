<?php

namespace Legoboy\WorldStats;

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

use Legoboy\WorldStats\task\SignUpdateTask;
use Legoboy\WorldStats\commands\WorldStatsCommand;

class WorldStats extends PluginBase implements Listener{

		public function onEnable(){
		    $this->getServer()->getPluginManager()->registerEvents($this, $this);
				if(!file_exists($this->getDataFolder())){
					@mkdir($this->getDataFolder());
				}
				$this->sign = new Config($this->getDataFolder() . "settings.yml", Config::YAML, array
				(
					"sign_trigger" => "worldstats",
					"sign_final_change" => "[WorldStatus]",
				)
			);
			$this->getLogger()->info(TextFormat::BLUE . "WorldStats has been enabled!");
			$this->getServer()->getScheduler()->scheduleRepeatingTask($task = new SignUpdateTask($this), 40);
			$this->taskid = $task->getTaskId();
		}
		
		private function registerAll(){
			$commandmap = $this->getServer()->getCommandMap();
    			$commandmap->register("ws", new WorldStatsCommand($this));
    		}
		
		
		// Line one: The sign trigger secret name...
		// Line two: The world name...
		// Line three: The final change...
		public function onSignChange(SignChangeEvent $event){
			$player = $event->getPlayer();
			if($player->hasPermission("worldstats.place")){
				if(TextFormat::clean(strtolower(trim($event->getLine(0)))) === strtolower(trim($this->sign->get("sign_trigger"))) || TextFormat::clean(strtolower(trim($event->getLine(0)))) === strtolower(trim($this->sign->get("sign_final_change")))){
					$world = $event->getLine(1);
					$level = $this->getServer()->getLevelByName($world);
					if($level instanceof Level){
						$players = count($level->getPlayers());
						$event->setLine(0, TextFormat::GREEN . $this->sign->get("sign_final_change"));
						$event->setLine(2, TextFormat::YELLOW . $players . TextFormat::RED . " players");
					}else{
						$event->setLine(0, TextFormat::DARK_RED . "()()()()()()");
						$event->setLine(1, TextFormat::DARK_RED . "*****");
						$event->setLine(2, TextFormat::RED . "World Name Error");
						$event->setLine(3, TextFormat::DARK_RED . "******");
					}
				}
			}else{
				$player->sendMessage(TextFormat::RED . "You do not have permission to create a World Status sign.");
				$event->setCancelled(true);
			}
		}
		
		public function onSignBreak(BlockBreakEvent $event){
			$player = $event->getPlayer();
			$block = $event->getBlock();
			if($block instanceof Sign){
				$sign = $block->getLevel()->getTile($block);
				$text = $sign->getText();
				if(TextFormat::clean(strtolower(trim($text[0]))) === strtolower(trim($this->sign->get("sign_final_change")))){
					if(!$player->hasPermission("worldstats.break")){
						$event->setCancelled(true);
						$player->sendMessage(TextFormat::RED . "You do not have permission to break a World Status sign.");
					}else{
						$player->sendMessage(TextFormat::RED . "A World Status sign has been broken!");
					}
				}
			}
		}
		
		
		/*************************
		*========================*
		*====[ Plugin APIs ]=====*
		*========================*
		*************************/
		
		public function updateSigns(){
			foreach($this->getServer()->getLevels() as $levels){
				foreach($levels->getTiles() as $tiles){
					if($tiles instanceof Sign){
						$text = $tiles->getText();
						if(TextFormat::clean(strtolower(trim($text[0]))) === strtolower(trim($this->sign->get("sign_final_change")))){
							$world = $text[1];
							$level = $this->getServer()->getLevelByName($world);
							$players = count($level->getPlayers());
							$tiles->setText($text[0] = TextFormat::GREEN . $this->sign->get("sign_final_change"), $text[2] = TextFormat::YELLOW . $players . " players");
						}
					}
				}
			}
		}
		
		public function killUpdateTask(){
			$result = $this->getServer()->getScheduler()->cancelTask($this->taskid);
			return $result;
		}
		
		public function resetSign(Sign $sign){
			$text = $sign->getText();
			if(TextFormat::clean(strtolower(trim($text[0]))) === strtolower(trim($this->sign->get("sign_final_change")))){
				$sign->setText($text[0] = "Sign Has been", $text[1] = "deleted from", $text[2] = "SignStatus", $text[3] = "updates.");
				return true;
			}else{
				return false;
			}
		}
}
