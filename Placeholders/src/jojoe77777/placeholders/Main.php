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
