<?php

namespace Legoboy\WorldStats\command;

use Legoboy\WorldStats\WorldStats;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;

class WorldStatsCommand extends Command implements PluginIdentifiableCommand{
  
    public function __construct(WorldStats $plugin){
        parent::__construct(
            "worldstats", 
            "Shows all the sub-commands for /worldstats", 
            "/worldstats <sub-command> [parameters]", 
            array("ws")
        );
        $this->setPermission("worldstats.cmd");
        $this->plugin = $plugin;
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
    
    private function sendCommandHelp(CommandSender $sender){
        $sender->sendMessage("§bWorldStats Commands:");
        $sender->sendMessage("§a/worldstats <update|info>");
    }
    
    private function updateSigns(){
        foreach($this->plugin->getServer()->getLevels() as $levels){
            foreach($levels->getTiles() as $tiles){
                if($tiles instanceof Sign){
                    $text = $tiles->getText();
                    if(TextFormat::clean(strtolower(trim($text[0]))) === strtolower(trim($this->plugin->sign->get("sign_final_change")))){
						$world = $text[1];
						$level = $this->plugin->getServer()->getLevelByName($world);
						$players = count($level->getPlayers());
						$tiles->setText($text[0] = TextFormat::GREEN . $this->plugin->sign->get("sign_final_change"), $text[2] = TextFormat::YELLOW . $players . " players");
					}
                }
            }
        }
    }
    
    public function execute(CommandSender $sender, $label, array $args){
        if(isset($args[0])){
            switch(strtolower($args[0])){
                case "info":
                    $sender->sendMessage(TextFormat::GREEN . "A plugin by Legoboy0215...");
                    break;
                case "update":
                    $sender->sendMessage("All signs are updated!");
                    $this->updateSigns();
                    break;                                               
            }
        }else{
            $this->sendCommandHelp($sender);
            return true;
        }
    }
}
