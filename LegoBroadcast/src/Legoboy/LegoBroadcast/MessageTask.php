<?php

namespace Legoboy\LegoBroadcast;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MessageTask extends PluginTask{

    public function __construct(Loader $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
		$this->messagekey = 0;
    }

    public function onRun($currentTick){
		switch($this->plugin->mode){
			case "in-order":
				$messages = $this->plugin->setting->get("messages");
				$number = count($messages);
				if($this->messagekey > $number - 1){
					$this->messagekey = 0;
				}
				$message = $messages[$this->messagekey];
				switch($this->plugin->sendmethod){
					case "chat":
						$this->plugin->getServer()->broadcastMessage($this->plugin->setting->get("prefix") . " " . $message);
						break;
					case "popup":
						foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
							$p->sendPopup($this->plugin->setting->get("prefix") . " " . $message);
						}
						break;
					case "tip":
						foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
							$p->sendTip($this->plugin->setting->get("prefix") . " " . $message);
						}
						break;
				}
				$this->messagekey++;
				break;
			case "random":
				$messages = $this->plugin->setting->get("messages");
				$messagekey = array_rand($messages, 1);
				$message = $messages[$messagekey];
				switch($this->plugin->sendmethod){
					case "chat":
						$this->plugin->getServer()->broadcastMessage($this->plugin->setting->get("prefix") . " " . $message);
						break;
					case "popup":
						foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
							$p->sendPopup($this->plugin->setting->get("prefix") . " " . $message);
						}
						break;
					case "tip":
						foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
							$p->sendTip($this->plugin->setting->get("prefix") . " " . $message);
						}
						break;
				}
				break;
		}
    }
}
