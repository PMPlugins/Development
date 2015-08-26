<?php

namespace Legoboy\WorldStats\task;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;

use Legoboy\WorldStats\WorldStats;

class SignUpdateTask extends PluginTask{
	
    public function __construct(WorldStats $plugin){
        $this->plugin = $plugin;
	    parent::__construct($plugin);
    }
    
    public function onRun($currentTick){
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
}
