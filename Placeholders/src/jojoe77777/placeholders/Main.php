<?php

namespace jojoe77777\placeholders;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{


    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "Placeholders enabled!");
    }

    public function onDisable(){
        $this->getLogger()->info(TextFormat::GREEN . "Placeholders disabled!");
    }

    public function Placeholders($message, $player){
        $msg = $this->singlePlaceholders($message, $player);
        $msg = $this->globalPlaceholders($msg);
        return $msg;
    }

    /* "singlePlaceholders" means placeholders that are player specific, like {name} */
    public function singlePlaceholders($message, $player){
        $manager = $this->getServer()->getPluginManager();
		$message = str_ireplace("{x}", $player->getX(), $message);
		$message = str_ireplace("{y}", $player->getY(), $message);
		$message = str_ireplace("{z}", $player->getZ(), $message);
		$message = str_ireplace("{name}", $player->getName(), $message);
		$message = str_ireplace("{displayname}", $player->getDisplayName(), $message);
		$message = str_ireplace("{gamemode}", $player->getGamemode(), $message);
		$message = str_ireplace("{health}", $player->getHealth(), $message);
		$message = str_ireplace("{ip}", $player->getAddress(), $message);
		$message = str_ireplace("{port}", $player->getPort(), $message);
		$message = str_ireplace("{nametag}", $player->getNameTag(), $message);
		$message = str_ireplace("{yaw}", $player->getYaw(), $message);
		$message = str_ireplace("{pitch}", $player->getPitch(), $message);
		$message = str_ireplace("{world}", $player->getZ(), $message);
        /* PurePerms, money, and KillRate placeholders by aliuly */
		if (($pmoney = $manager->getPlugin("PocketMoney")) !== null) {
		    $message = str_ireplace("{money}", $manager->getMoney($player->getName()), $message);
		} elseif (($mecon = $manager->getPlugin("MassiveEconomy")) !== null) {
		    $message = str_ireplace("{money}", $mecon->getMoney($player->getName()), $message);
		} elseif (($econapi = $manager->getPlugin("EconomyAPI")) !== null) {
		    $message = str_ireplace("{money}", $econapi->mymoney($player->getName()), $message);
		} elseif (($goldstd = $manager->getPlugin("GoldStd")) !== null) {
		    $message = str_ireplace("{money}", $goldstd->getMoney($player), $message);
		}

		if(($pperms = $manager->getPlugin("PurePerms")) !== null){
		    $message = str_ireplace("{group}", $pperms->getUser($player)->getGroup()->getName(), $message);
		}
		if(($kr = $manager->getPlugin("KillRate")) !== null){
            if(version_compare($kr->getDescription()->getVersion(),"1.1") >= 0){
                $message = str_ireplace("{score}", $kr->getScore($player), $message);
            }
         }

        return $message;

    }
    /* "globalPlaceholders" can be used anywhere */
    public function globalPlaceholders($message){
        $manager = $this->getServer()->getPluginManager();
        $message = str_ireplace("{difficulty}", $this->getServer()->getDifficulty(), $message);
		$message = str_ireplace("{motd}", $this->getServer()->getMotd(), $message);
		$message = str_ireplace("{tps}", $this->getServer()->getTicksPerSecond(), $message);
		$message = str_ireplace("{maxplayers}", $this->getServer()->getMaxPlayers(), $message);
		$message = str_ireplace("{serverip}", $this->getServer()->getIp(), $message);
		$message = str_ireplace("{playercount}", count($this->getServer()->getOnlinePlayers()), $message);
		$message = str_ireplace("{serverport}", $this->getServer()->getPort(), $message);
		$message = str_ireplace("{pmversion}", $this->getServer()->getPocketMineVersion(), $message);
		$message = str_ireplace("{version}", $this->getServer()->getVersion(), $message);
		$message = str_ireplace("{viewdistance}", $this->getServer()->getViewDistance(), $message);
		$message = str_ireplace("{servername}", $this->getServer()->getServerName(), $message);
		$message = str_ireplace("{defaultgamemode}", $this->getServer()->getDefaultGamemode(), $message);
		$message = str_ireplace("{defaultlevel}", $this->getServer()->getDefaultLevel()->getName(), $message);
		$message = str_ireplace("{serverflight}", $this->getServer()->getAllowFlight(), $message);
		$message = str_ireplace("{codename}", $this->getServer()->getCodename(), $message);
		$message = str_ireplace("{apiversion}", $this->getServer()->getApiVersion(), $message);
		$message = str_ireplace("{line}", "\n", $message);
        $message = str_ireplace("{BLACK}", TextFormat::BLACK, $message);
        $message = str_ireplace("{DARK_BLUE}", TextFormat::DARK_BLUE, $message);
        $message = str_ireplace("{DARK_GREEN}", TextFormat::DARK_GREEN, $message);
        $message = str_ireplace("{DARK_AQUA}", TextFormat::DARK_AQUA, $message);
        $message = str_ireplace("{DARK_RED}", TextFormat::DARK_RED, $message);
        $message = str_ireplace("{DARK_PURPLE}", TextFormat::DARK_PURPLE, $message);
        $message = str_ireplace("{GOLD}", TextFormat::GOLD, $message);
        $message = str_ireplace("{GRAY}", TextFormat::GRAY, $message);
        $message = str_ireplace("{DARK_GRAY}", TextFormat::DARK_GRAY, $message);
        $message = str_ireplace("{BLUE}", TextFormat::BLUE, $message);
        $message = str_ireplace("{GREEN}", TextFormat::GREEN, $message);
        $message = str_ireplace("{AQUA}", TextFormat::AQUA, $message);
        $message = str_ireplace("{RED}", TextFormat::RED, $message);
        $message = str_ireplace("{LIGHT_PURPLE}", TextFormat::LIGHT_PURPLE, $message);
        $message = str_ireplace("{YELLOW}", TextFormat::YELLOW, $message);
        $message = str_ireplace("{WHITE}", TextFormat::WHITE, $message);
        $message = str_ireplace("{OBFUSCATED}", TextFormat::OBFUSCATED, $message);
        $message = str_ireplace("{BOLD}", TextFormat::BOLD, $message);
        $message = str_ireplace("{STRIKETHROUGH}", TextFormat::STRIKETHROUGH, $message);
        $message = str_ireplace("{UNDERLINE}", TextFormat::UNDERLINE, $message);
        $message = str_ireplace("{ITALIC}", TextFormat::ITALIC, $message);
        $message = str_ireplace("{RESET}", TextFormat::RESET, $message);
        if(($kr = $manager->getPlugin("KillRate")) !== null){
            if(version_compare($kr->getDescription()->getVersion(),"1.1") >= 0){
                $ranks = $kr->getRankings(3);
                    if ($ranks == null){
                        $message = str_ireplace("{tops}", "N/A", $message);
                    } else {
                        $message = str_ireplace("{tops}", "", $message);
                        $i = 1; $q = "";
                        foreach ($ranks as $r){
                            $message = str_ireplace("{tops}", $q.($i++).". ".substr($r["player"],0,8)." ".$r["count"], $message);
                            $q = "   ";
                        }
                    }
            }
        }
        return $message;
    }

}
