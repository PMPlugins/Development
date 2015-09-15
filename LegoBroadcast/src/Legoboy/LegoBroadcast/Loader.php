<?php

namespace Legoboy\LegoBroadcast;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{

    public function onEnable(){
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		}
        $this->setting = new Config($this->getDataFolder() . "setting.yml", Config::YAML, array(
			"message-select-mode" => "in-order",
			"message-send-method" => "chat",
            "messages" => array(
                "message1",
                "message2",
                "message3",
                "message4",
                "message5",
                "message6"
            ),
            "seconds" => "30",
            "prefix" => "[LegoBroadcast]",
        )
		);
        $time = intval($this->setting->get("seconds")) * 20;
		$this->mode = strtolower($this->setting->get("message-select-mode"));
		$this->sendmethod = strtolower($this->setting->get("message-send-method"));
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MessageTask($this), $time);
		$this->setting->save();
        $this->getLogger()->info("LegoBroadcast has been enabled!");
    }

    public function onDisable(){
        $this->setting->save();
    }
}