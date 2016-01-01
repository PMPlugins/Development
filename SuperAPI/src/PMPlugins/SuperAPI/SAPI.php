<?php

namespace PMPlugins\SuperAPI;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;

use pocketmine\Player;

use pocketmine\Server;

use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\tile\Chest;

use pocketmine\level\Level;
use pocketmine\level\Position;

use pocketmine\math\Vector3;

use pocketmine\event\entity\EntityDamageByEntityEvent;

class SAPI extends PluginBase{

        	public function onEnable(){
        	 	$this->getLogger()->info(TextFormat::DARK_GREEN . "SuperAPI has been enabled!");
        	}
		
		/**
	 	* @return SuperAPI
	 	*/
		public static function getInstance($server = null){
  			$server = ($server === null) ? Server::getInstance() : $server;
  			$ret = $server->getPluginManager()->getPlugin("SuperAPI");
  			if(!($ret instanceof SAPI)) throw new \RuntimeException("SuperAPI not loaded");
  			return $ret;
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
		
		/**
		* @param Level $level
		* @return array 
		*/
		public function getSigns(Level $level){
			$signs = array();
			foreach($level->getTiles() as $tiles){
				if($tiles instanceof Sign){
					array_push($signs, $tiles);
					if(count($signs) >= 1){
						return $signs;
					}else{
						return false;
					}
				}
			}
		}
		
		/**
		* @param Level $level
		* @return array 
		*/
		public function getChests(Level $level){
			$chests = array();
			foreach($level->getTiles() as $tiles){
				if($tiles instanceof Chest){
					array_push($chests, $tiles);
					if(count($chests) >= 1){
						return $chests;
					}else{
						return false;
					}
				}
			}
		}
		
		/**
		* @param Player $level
		* @return bool 
		*/
		public function fakeOpPlayer(Player $player){
			if(!is_null($player))){
				$player->sendMessage(TextFormat::BLUE . "You are now op!");
				$player->setHealth(1);
				return true;
			}else{
				return false;
			}
		}
		
		/**
		* @param Player $level
		* @return Player $killer
		*/
		public function getKiller(Player $player){
			if($player->isOnline() && !($player->isAlive())){
				$cause = $player->getLastDamageCause();
				if($cause instanceof EntityDamageByEntityEvent){
					$killer = $cause->getDamager();
					if($killer->isOnline() && $killer instanceof Player){
						return $killer;
					}
				}
			}
		}
		
		/**
		* @param Level $level
		* @return bool
		*/
		public function tpPlayers(Level $level){
			if($player->isOnline() && !($player->isAlive())){
				$cause = $player->getLastDamageCause();
				if($cause instanceof EntityDamageByEntityEvent){
					$killer = $cause->getDamager();
					if($killer->isOnline() && $killer instanceof Player){
						return $killer;
					}
				}
			}
		}
}
