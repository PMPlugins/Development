<?php

namespace Legoboy\Lookout;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use pocketmine\level\Level;
use pocketmine\level\Position;

use pocketmine\math\Vector3;

use pocketmine\item\Item;

use pocketmine\block\Block;

use Legoboy\Lookout\TimerTask;

class Loader extends PluginBase implements Listener{

		/** @var string AUTHOR Plugin author(s) */
		const AUTHOR = "Legoboy0215";
	
		/** @var string VERSION Plugin version */
		const VERSION = "1.0.0";
	
		/** @var string PREFIX Plugin message prefix */
		const PREFIX = "[Lookout]";
	
		public function onEnable(){
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
			if(!file_exists($this->getDataFolder())){
				@mkdir($this->getDataFolder());
			}
			$this->saveDefaultConfig();
			$this->setting = $this->getConfig();
			$this->players = array();
			$this->gamestatus = 0; // 0 for not running, 1 for running.
			if(!$this->getServer()->isLevelLoaded($this->setting->get("match_world_name"))){
				$this->getServer()->loadLevel($this->setting->get("match_world_name"));
				$this->getLogger()->info(TextFormat::YELLOW . "Level was loaded automaticly.");
			}
			$this->getLogger()->info(TextFormat::GREEN . self::PREFIX . " Lookout version " . self::VERSION . " by " . self::AUTHOR . " has started!");
			if(!$this->getServer()->isLevelGenerated($this->setting->get("match_world_name"))){
				$this->getLogger()->critical("Level " . $this->setting->get("match_world_name") . " does NOT exist!");
				$this->getServer()->getPluginManager()->disablePlugin($this);
			}else{
				$this->startGame();
			}
		}
    
		public function onDisable(){
			$this->setting->save();
		}
		
		public function startGame(){
			if($this->gamestatus === 0){
				$this->starttime = time();
				$gametime = (int) $this->setting->get("game_seconds");
				$waittime = (int) $this->setting->get("waiting_seconds");
				$this->timer = new TimerTask($this, $gametime, $waittime);
				$handler = $this->getServer()->getScheduler()->scheduleRepeatingTask($this->timer, 20);
				$this->timer->setHandler($handler);
				$itemid = $this->setting->get("item_id");
				$this->generateItem($itemid);
			}
		}
		
		public function endGame(){
			$this->getServer()->getScheduler()->cancelTask($this->timer->getTaskId());
			$this->gamestatus = 0;
		}
		
		public function restartGame(){
			$this->getServer()->getScheduler()->cancelTask($this->timer->getTaskId());
			$this->gamestatus = 0;
			$this->starttime = time();
			$gametime = (int) $this->setting->get("game_seconds");
			$waittime = (int) $this->setting->get("waiting_seconds");
			$this->timer = new TimerTask($this, $gametime, $waittime);
			$handler = $this->getServer()->getScheduler()->scheduleRepeatingTask($this->timer, 20);
			$this->timer->setHandler($handler);
		}
		
		public function forceEndGame(){
			if($this->gamestatus === 1){
				$this->getServer()->getScheduler()->cancelTask($this->timer->getTaskId());
				return true;
			}else{
				return false;
			}
		}
		
		public function generateItem($itemid){
			$item = Item::get($itemid, 0, 1);
			$x = $this->setting->get("x_axis_for_generation");
			$y = $this->setting->get("y_axis_for_generation");
			$z = $this->setting->get("z_axis_for_generation");
			$level = $this->getServer()->getLevelByName($this->setting->get("match_world_name"));
			$pos = new Vector3($x, $y, $z);
			$level->dropItem($pos, $item);
		}
		
		public function sendGameMessage($message){
			foreach($this->players as $p){
				$player = $this->getServer()->getPlayer($p);
				$player->sendMessage(self::PREFIX . $message);
			}
			$this->getLogger()->info($message);
		}
		
		public function onTapBlock(PlayerInteractEvent $event){
			$block = $event->getBlock();
			$player = $event->getPlayer();
			$level = strtolower($player->getLevel()->getName());
			$gamelevel = strtolower($this->setting->get("match_world_name"));
			$item = $event->getItem();
			if($this->gamestatus === 1 && $level === $gamelevel && $block instanceof Block && in_array($player->getName(), $this->players) && $item->getId() === $this->setting->get("item_id") && $block->getId() === $this->setting->get("tapped_block_id")){
				$this->sendGameMessage($player->getName() . " won the game!");
				$this->endGame();
			}
		}
}
