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
        $this->saveDefaultConfig();
    }

    public function onDisable(){
        $this->getLogger()->info(TextFormat::GREEN . "Placeholders disabled!");
    }

    public function allPlaceholders($message, $player){
        $msg = $this->singlePlaceholders($message, $player);
        $msg = $this->globalPlaceholders($msg);
        return $msg;
    }

    /* "singlePlaceholders" means placeholders that are player specific, like {name} */
    public function singlePlaceholders($message, $player){
        $manager = $this->getServer()->getPluginManager();
		$svars = [
        "{x}" => $player->getX(),
		"{y}" => $player->getY(),
		"{z}" => $player->getZ(),
		"{name}" => $player->getName(),
		"{displayname}" => $player->getDisplayName(),
		"{gamemode}" => $player->getGamemode(),
		"{health}" => $player->getHealth(),
		"{ip}" => $player->getAddress(),
		"{port}" => $player->getPort(),
		"{nametag}" => $player->getNameTag(),
		"{yaw}" => $player->getYaw(),
		"{pitch}" => $player->getPitch(),
		"{world}" => $player->getZ()];
        /* PurePerms, money, and KillRate placeholders by aliuly */
		if (($pmoney = $manager->getPlugin("PocketMoney")) !== null) {
		    $svars["{money}"] = $manager->getMoney($player->getName();
		} elseif (($mecon = $manager->getPlugin("MassiveEconomy")) !== null) {
            $svars["{money}"] = $mecon->getMoney($player->getName();
		} elseif (($econapi = $manager->getPlugin("EconomyAPI")) !== null) {
		    $svars["{money}"] = $econapi->mymoney($player->getName();
		} elseif (($goldstd = $manager->getPlugin("GoldStd")) !== null) {
		    $svars["{money}"] = $goldstd->getMoney($player);
		}

		if(($pperms = $manager->getPlugin("PurePerms")) !== null){
		    $svars["{group}"] = $pperms->getUser($player)->getGroup()->getName();
		}
		if(($kr = $manager->getPlugin("KillRate")) !== null){
            if(version_compare($kr->getDescription()->getVersion(),"1.1") >= 0){
                $svars["{score}"] = $kr->getScore($player);
            }
         }
        foreach($gvars as $key => $value){
            str_ireplace($key, $value, $message);
        }
        return $message;

    }
    /* "globalPlaceholders" can be used anywhere */
    public function globalPlaceholders($message){
        $manager = $this->getServer()->getPluginManager();
        $gvars = ["{difficulty}" => $this->getServer()->getDifficulty(),
		"{motd}" => $this->getServer()->getMotd(),
		"{tps}" => $this->getServer()->getTicksPerSecond(),
		"{maxplayers}" => $this->getServer()->getMaxPlayers(),
		"{serverip}" => $this->getServer()->getIp(),
		"{playercount}" => count($this->getServer()->getOnlinePlayers()),
		"{serverport}" => $this->getServer()->getPort(),
		"{pmversion}" => $this->getServer()->getPocketMineVersion(),
		"{version}" => $this->getServer()->getVersion(),
		"{viewdistance}" => $this->getServer()->getViewDistance(),
		"{servername}" => $this->getServer()->getServerName(),
		"{defaultgamemode}" => $this->getServer()->getDefaultGamemode(),
		"{defaultlevel}" => $this->getServer()->getDefaultLevel()->getName(),
		"{serverflight}" => $this->getServer()->getAllowFlight(),
		"{codename}" => $this->getServer()->getCodename(),
		"{apiversion}" => $this->getServer()->getApiVersion(),
		"{line}" => "\n",
        "{BLACK}" => TextFormat::BLACK,
        "{DARK_BLUE}" => TextFormat::DARK_BLUE,
        "{DARK_GREEN}" => TextFormat::DARK_GREEN,
        "{DARK_AQUA}" => TextFormat::DARK_AQUA,
        "{DARK_RED}" => TextFormat::DARK_RED,
        "{DARK_PURPLE}" => TextFormat::DARK_PURPLE,
        "{GOLD}" => TextFormat::GOLD,
        "{GRAY}" => TextFormat::GRAY,
        "{DARK_GRAY}" => TextFormat::DARK_GRAY,
        "{BLUE}" => TextFormat::BLUE,
        "{GREEN}" => TextFormat::GREEN,
        "{AQUA}" => TextFormat::AQUA,
        "{RED}" => TextFormat::RED,
        "{LIGHT_PURPLE}" => TextFormat::LIGHT_PURPLE,
        "{YELLOW}" => TextFormat::YELLOW,
        "{WHITE}" => TextFormat::WHITE,
        "{OBFUSCATED}" => TextFormat::OBFUSCATED,
        "{BOLD}" => TextFormat::BOLD,
        "{STRIKETHROUGH}" => TextFormat::STRIKETHROUGH,
        "{UNDERLINE}" => TextFormat::UNDERLINE,
        "{ITALIC}" => TextFormat::ITALIC,
        "{RESET}" => TextFormat::RESET,
        "{time}" => date($this->getConfig()->get("time_format"))]
        if(($kr = $manager->getPlugin("KillRate")) !== null){
            if(version_compare($kr->getDescription()->getVersion(),"1.1") >= 0){
                $ranks = $kr->getRankings(3);
                    if ($ranks == null){
                        $gvars["{tops}"] = "N/A";
                    } else {
                        $gvars["{tops}" = "";
                        $i = 1; $q = "";
                        foreach ($ranks as $r){
                            $gvars["{tops}"] = $q.($i++).". ".substr($r["player"],0,8)." ".$r["count"];
                            $q = "   ";
                        }
                    }
            }
        }
        foreach($gvars as $key => $value){
            str_ireplace($key, $value, $message);
        }
        return $message;
    }

}
