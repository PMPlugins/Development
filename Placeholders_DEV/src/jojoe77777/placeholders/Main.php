<?php

namespace jojoe77777\placeholders;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{


    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "Placeholders enabled!");
        $this->saveDefaultConfig();
        $this->saveResource("placeholders.php");
    }

    public function onDisable()
    {
        $this->getLogger()->info(TextFormat::GREEN . "Placeholders disabled!");
    }

    public function allPlaceholders($message, $player)
    {
        $msg = $this->singlePlaceholders($message, $player);
        $msg = $this->globalPlaceholders($msg);
        return $msg;
    }

    /* "singlePlaceholders" means placeholders that are player specific, like {name} */
    public function singlePlaceholders($message, $player)
    {
        $manager = $this->getServer()->getPluginManager();
        $server = $this->getServer();
        foreach ([
                     "{x}" => $player->getX(),
                     "{y}" => $player->getY(),
                     "{z}" => $player->getZ(),
                     "{name}" => $player->getName(),
                     "{displayname}" => $player->getDisplayName(),
                     "{gamemode}" => $player->getGamemode(),
                     "{health}" => $player->getHealth(),
                     "{ip}" => $player->getAddress(),
                     "{nametag}" => $player->getNameTag(),
                     "{yaw}" => $player->getYaw(),
                     "{pitch}" => $player->getPitch(),
                     "{world}" => $player->getLevel()->getName(),
                     "{world_seed}" => $player->getLevel()->getSeed(),
                 ] as $first => $second) {
                     $svars[$first] = $second;

        }
        /* PurePerms, money, and KillRate scores placeholders by aliuly */
        if (($pmoney = $manager->getPlugin("PocketMoney")) !== null) {
            $svars["{money}"] = $pmoney->getMoney($player->getName());
        } elseif (($mecon = $manager->getPlugin("MassiveEconomy")) !== null) {
            $svars["{money}"] = $mecon->getMoney($player->getName());
        } elseif (($econapi = $manager->getPlugin("EconomyAPI")) !== null) {
            $svars["{money}"] = $econapi->mymoney($player->getName());
        } elseif (($goldstd = $manager->getPlugin("GoldStd")) !== null) {
            $svars["{money}"] = $goldstd->getMoney($player);
        }

        if (($pperms = $manager->getPlugin("PurePerms")) !== null) {
            $svars["{group}"] = $pperms->getUser($player)->getGroup()->getName();
        }
        if (($kr = $manager->getPlugin("KillRate")) !== null) {
            if (version_compare($kr->getDescription()->getVersion(), "1.1") >= 0) {
                $svars["{score}"] = $kr->getScore($player);
            }
        }

        if (file_exists($this->getDataFolder()."placeholders.php")) {
            $code = file_get_contents($this->getDataFolder()."placeholders.php");
            if(!($code == null)){
                eval($code);
            }
        }

        foreach ($svars as $key => $value) {
            $message = str_ireplace($key, $value, $message);
        }
        return $message;

    }

    /* "globalPlaceholders" can be used anywhere */
    public function globalPlaceholders($message)
    {
        $manager = $this->getServer()->getPluginManager();
        $server = $this->getServer();
        foreach ([
                     "{difficulty}" => $server->getDifficulty(),
                     "{motd}" => $server->getMotd(),
                     "{tps}" => $server->getTicksPerSecond(),
                     "{max_players}" => $server->getMaxPlayers(),
                     "{server_ip}" => $server->getIp(),
                     "{player_count}" => count($server->getOnlinePlayers()),
                     "{server_port}" => $server->getPort(),
                     "{pm_version}" => $server->getPocketMineVersion(),
                     "{version}" => $server->getVersion(),
                     "{view_distance}" => $server->getViewDistance(),
                     "{default_gamemode}" => $server->getDefaultGamemode(),
                     "{default_level}" => $server->getDefaultLevel()->getName(),
                     "{default_level_seed}" => $server->getDefaultLevel()->getSeed(),
                     "{server_flight}" => $server->getAllowFlight(),
                     "{codename}" => $server->getCodename(),
                     "{api_version}" => $server->getApiVersion(),
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
                     "{month_number}" => date("n"),
                     "{month_number_0}" => date("m"),
                     "{month_name_short}" => date("M"),
                     "{month_name}" => date("F"),
                     "{year}" => date("Y"),
                     "{year_short}" => date("y"),
                     "{minute}" => date("i"),
                     "{second}" => date("s"),
                     "{day}" => date("l"),
                     "{day_short}" => date("D"),
                     "{day_number_0}" => date("d"),
                     "{day_number}" => date("j"),
                     "{time_unix}" => time(),

                 ] as $first => $second) {
            $gvars[$first] = $second;
        }
        $crctr = $this->getConfig()->get("time_corrector");
        $gvars["{12hour_0}"] = date("g") + $crctr;
        $gvars["{24hour_0}"] = date("G") + $crctr;
        $gvars["{12hour}"] = date("h") + $crctr;
        $gvars["{24hour}"] = date("H") + $crctr;

        if (($kr = $manager->getPlugin("KillRate")) !== null) {
            $rankings = $kr->api->getRankedScores();
            $i = 0;
            while ($i < (count($rankings))) {
                $currentRanking = $rankings[$i];
                $gvars["{kr_top_name_".$i."}"] = $currentRanking["player_name"];
                $gvars["{kr_top_score_".$i."}"] = $currentRanking["points"];
                $i++;
            }
        }
        if (file_exists($this->getDataFolder()."placeholders.php")) {
            $code = file_get_contents($this->getDataFolder()."placeholders.php");
            if(!($code == null)){
                eval($code);
            }
        }

        foreach($gvars as $key => $value){
            $message = str_ireplace($key, $value, $message);
        }
        return $message;
    }

}
