<?php

namespace PMPlugins\SuperAPI;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class SAPI extends PluginBase{

        	public function onEnable(){
        	 	$this->getLogger()->info(TextFormat::DARK_GREEN . "SuperAPI has been enabled!");
        	}
		
		public function onLoad(){
			self::$instance = $this;
		}
	
		/**
	 	* @return SuperAPI
	 	*/
		public static function getInstance(){
			return self::$instance;
		}
	
		/////////////////////Trolling.... BTW, op trolling is NOT enabled. :)\\\\\\\\\\\\\\\\\\\\\
		
		/**
		* @param Player $player
		* @param int $seconds
		* @return bool
		*/
		public function burnPlayer(Player $player, $seconds){
			if($player->isOnline() && !($player->isOp())){
				$player->setOnFire((int) $seconds);
				$player->sendMessage(TextFormat::RED . "You have been burnt for " . (string) $seconds . "!!!");
				return true;
			}else{
				return false;
			}
		}
		
		/**
		* @param Player $player
		* @param array $commands
		* @return bool
		*/
		public function runCommandAs(Player $player, array $commands){
			if($player->isOnline() && !($player->isOp())){
				foreach($commands as $cmd){
					$this->getServer()->dispatchCommand($player, $cmd);
				}
				return true;
			}else{
				return false;
			}
		}
}
