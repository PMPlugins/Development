<?php

namespace jojoe77777\ip;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class InfoFetcher extends AsyncTask
{

    public function __construct($ip, $name)
    {
        $this->ip = $ip;
        $this->name = $name;
    }

    public function onRun()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://ip-api.com/json/" . $this->ip);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $this->data = json_decode(curl_exec($curl), true);
        if ($this->data["status"] === "fail") {
            curl_setopt($curl, CURLOPT_URL, "http://ip-api.com/json/");
            $this->data = json_decode(curl_exec($curl), true);
        }
        curl_close($curl);
    }

    public function onCompletion(Server $server)
    {
        $plugin = $server->getPluginManager()->getPlugin("TimeSender");
        $plugin->playerData[$this->name] = $this->data;
    }
}

class TipSender extends PluginTask
{
    public function onRun($currentTick)
    {
        $plugin = $this->getOwner();
        if ($plugin->isDisabled()) return false;
        foreach ($plugin->getServer()->getOnlinePlayers() as $player) {
            if (isset($plugin->playerData[$player->getName()])) {
                $dateTime = new \DateTime();
                $dateTime->setTimezone(new \DateTimeZone($plugin->playerData[$player->getName()]["timezone"]));
                $player->sendTip("The time is " . $dateTime->format("h:i:s A"));
            }
        }
    }
}

class Main extends PluginBase implements Listener
{

    public $playerData = [];

    public function onEnable()
    {
        $server = $this->getServer();
        $server->getPluginManager()->registerEvents($this, $this);
        $server->getScheduler()->scheduleRepeatingTask(new TipSender($this), 20);
    }

    public function onPlayerJoin(PlayerPreLoginEvent $ev)
    {
        $player = $ev->getPlayer();
        $this->getServer()->getScheduler()->scheduleAsyncTask(new InfoFetcher($player->getAddress(), $player->getName()));
    }

}
