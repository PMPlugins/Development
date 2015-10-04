<?php

namespace jojoe77777\tellraw;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as Colours;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase implements Listener{


	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(Colours::GREEN . "TellRaw enabled!");
	}

	public function onDisable(){
		$this->getLogger()->info(Colours::GREEN . "TellRaw disabled!");
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch($command->getName()){
			case 'tellraw':
				if (count($args) < 2){
					$sender->sendMessage(Colours::RED."Please enter a player and a message.");
					return true;
				}
				$playerInput = array_shift($args);
				$player = $this->getServer()->getPlayer($playerInput);
				$toAll = false;
				if($playerInput == "*"){
					$toAll = true;
				}
				if(!($player == null)){
					$playerName = $player->getName();
					$msg = str_replace("{player}", $playerName, trim(implode(" ", $args)));
					$msg = str_replace("{line}", "\n", $msg);
					$msg = $this->genColours("&", $msg);
					$msg = $this->genColours("§", $msg);
					$manager = $this->getServer()->getPluginManager();
					if (($placeholders = $manager->getPlugin("Placeholders")) !== null) {
						$msg = $placeholders->allPlaceholders($msg, $player);
					}
					$player->sendMessage($msg);
					$sender->sendMessage(Colours::GREEN."Message was sent to ".$playerName);
					return true;
				}
				if($toAll == false){
					$sender->sendMessage(Colours::RED."Player not found.");
					return true;
				}

				if($toAll){
					$players = $this->getServer()->getOnlinePlayers();
					if(!(empty($players))){
						foreach($players as $player){
							$playerName = $player->getName();
							$msg = str_replace("{player}", $playerName, trim(implode(" ", $args)));
							$msg = str_replace("{line}", "\n", $msg);
							$msg = $this->genColours("&", $msg);
							$msg = $this->genColours("§", $msg);
							$manager = $this->getServer()->getPluginManager();
							if (($placeholders = $manager->getPlugin("Placeholders")) !== null) {
								$msg = $placeholders->allPlaceholders($msg, $player);
							}
							$player->sendMessage($msg);
						}
						$sender->sendMessage(Colours::GREEN."Message sent to everyone");
					} else {
						$sender->sendMessage(Colours::RED."No players online.");
					}
				}


				return true;
			case 'silenttellraw':
				if (count($args) < 2){
					return true;
				}
				$playerInput = array_shift($args);
				$player = $this->getServer()->getPlayer($playerInput);
				$toAll = false;
				if($playerInput == "*"){ $toAll = true; }
				if(!($player == null)){
					$playerName = $player->getName();
					$msg = str_replace("{player}", $playerName, trim(implode(" ", $args)));
					$msg = str_replace("{line}", "\n", $msg);
					$msg = $this->genColours("&", $msg);
					$msg = $this->genColours("§", $msg);
					$manager = $this->getServer()->getPluginManager();
					if (($placeholders = $manager->getPlugin("Placeholders")) !== null) {
						$msg = $placeholders->allPlaceholders($msg, $player);
					}
					$player->sendMessage($msg);
					return true;
				}
				if($toAll){
					$players = $this->getServer()->getOnlinePlayers();
					if(!(empty($players))){
						foreach($players as $player){
							$playerName = $player->getName();
							$msg = str_replace("{player}", $playerName, trim(implode(" ", $args)));
							$msg = str_replace("{line}", "\n", $msg);
							$msg = $this->genColours("&", $msg);
							$msg = $this->genColours("§", $msg);
							$manager = $this->getServer()->getPluginManager();
							if (($placeholders = $manager->getPlugin("Placeholders")) !== null) {
								$msg = $placeholders->allPlaceholders($msg, $player);
							}
							$player->sendMessage($msg);
						}
						return true;

					}
				}
		}
	}
	public function genColours($colourSign, $msg){

		$msg = str_replace($colourSign."0", Colours::BLACK, $msg);
		$msg = str_replace($colourSign."1", Colours::DARK_BLUE, $msg);
		$msg = str_replace($colourSign."2", Colours::DARK_GREEN, $msg);
		$msg = str_replace($colourSign."3", Colours::DARK_AQUA, $msg);
		$msg = str_replace($colourSign."4", Colours::DARK_RED, $msg);
		$msg = str_replace($colourSign."5", Colours::DARK_PURPLE, $msg);
		$msg = str_replace($colourSign."6", Colours::GOLD, $msg);
		$msg = str_replace($colourSign."7", Colours::GRAY, $msg);
		$msg = str_replace($colourSign."8", Colours::DARK_GRAY, $msg);
		$msg = str_replace($colourSign."9", Colours::BLUE, $msg);
		$msg = str_replace($colourSign."a", Colours::GREEN, $msg);
		$msg = str_replace($colourSign."b", Colours::AQUA, $msg);
		$msg = str_replace($colourSign."c", Colours::RED, $msg);
		$msg = str_replace($colourSign."d", Colours::LIGHT_PURPLE, $msg);
		$msg = str_replace($colourSign."e", Colours::YELLOW, $msg);
		$msg = str_replace($colourSign."f", Colours::WHITE, $msg);
		$msg = str_replace($colourSign."k", Colours::OBFUSCATED, $msg);
		$msg = str_replace($colourSign."l", Colours::BOLD, $msg);
		$msg = str_replace($colourSign."m", Colours::STRIKETHROUGH, $msg);
		$msg = str_replace($colourSign."n", Colours::UNDERLINE, $msg);
		$msg = str_replace($colourSign."o", Colours::ITALIC, $msg);
		$msg = str_replace($colourSign."r", Colours::RESET, $msg);
		return $msg;
	}

}
