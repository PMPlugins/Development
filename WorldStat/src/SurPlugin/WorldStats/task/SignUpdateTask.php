<?php

namespace SurPlugin/WorldStats;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;

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
					if(TextFormat::clean($text[0]) === "[HG Players]"){
						$tiles->setText($text[0] = TextFormat::GREEN . "[HG Players]", $text[1] = TextFormat::YELLOW . "TPS: " . $this->plugin->getServer()->getTicksPerSecond());
					}
				}
			}
		}
	}
}
