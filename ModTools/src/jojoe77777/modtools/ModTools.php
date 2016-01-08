<?php

namespace jojoe77777\modtools;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class MuteTicker extends PluginTask {
    public function onRun($currentTick){
        if ($this->owner->isDisabled()) return;
        $this->owner->tickMutes();
    }
}

class BanTicker extends PluginTask {
    public function onRun($currentTick){
        if ($this->owner->isDisabled()) return;
        $this->owner->tickBans();
    }
}

class ModTools extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->mutes = [];
        $this->bans = [];
        $server = $this->getServer();
        $manager = $server->getPluginManager();
        $manager->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->cfg = $this->getConfig();
        $server->getScheduler()->scheduleRepeatingTask(new MuteTicker($this), $this->cfg->get("general")["muteTickDelayInSeconds"] * 20);
        $server->getScheduler()->scheduleRepeatingTask(new BanTicker($this), $this->cfg->get("general")["banTickDelayInSeconds"] * 20);
        $this->sqlData = $this->cfg->get("sql");
        $this->banInfo = $this->cfg->get("bans");
        $this->muteInfo = $this->cfg->get("mutes");
        $this->general = $this->cfg->get("general");
        $this->dbStatus = true;
        try {
            $this->db = new \mysqli($this->sqlData["host"], $this->sqlData["user"], $this->sqlData["pass"], $this->sqlData["database"], $this->sqlData["port"]);
        } catch (\Exception $e){
            $this->getLogger()->critical("ModTools error: Could not connect to database!");
            $this->dbStatus = false;
            $manager->disablePlugin($this);
        }

        if($this->dbStatus) {
            $this->db->query("
            CREATE TABLE IF NOT EXISTS " . $this->sqlData["muteTable"] . " (
			name VARCHAR(16) NOT NULL,
			mutedBy VARCHAR(16) NOT NULL,
			startTime VARCHAR(16) NOT NULL,
			endTime VARCHAR(16) NOT NULL
		    );");
            $this->db->query("
            CREATE TABLE IF NOT EXISTS " . $this->sqlData["banTable"] . " (
			name VARCHAR(16) NOT NULL,
			bannedBy VARCHAR(16) NOT NULL,
			startTime VARCHAR(16) NOT NULL,
			endTime VARCHAR(16) NOT NULL
		    );");
        }
    }

    protected function prepare($string)
    {
        return "'".$this->db->real_escape_string($string)."'";
    }

    public function onPlayerJoin(PlayerPreLoginEvent $ev){
        $playerName = $ev->getPlayer()->getName();
        if($this->isBanned($playerName)){
            $ev->setKickMessage($this->banInfo["bannedMessage"]);
            $ev->setCancelled(true);
        }
    }

    public function preChat(PlayerCommandPreprocessEvent $ev){
        $command = $ev->getMessage();
        $blockedCommandsWhileMuted = $this->muteInfo["blockedCommandsWhileMuted"];
        if($this->isMuted($ev->getPlayer()->getName())){
            foreach($blockedCommandsWhileMuted as $cmd){
                if(strtolower($command) === strtolower("/" . $cmd)) {
                    $ev->getPlayer()->sendMessage($this->muteInfo["youAreMuted"]);
                    $ev->setCancelled(true);
                    return true;
                }
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch($command->getName())
        {
            case "kick":
                if(isset($args[0])){
                    $p = $this->getServer()->getPlayer(array_shift($args));
                    if($p != null){
                        $p->kick(implode(" ", $args), false);
                    } else {
                        $sender->sendMessage($this->general["playerNotOnline"]);
                    }
                }
                return true;
                break;
            case "ban":
                if(isset($args[0])){
                    $this->addBan($args[0], 1, $sender->getName());
                    $sender->sendMessage(str_ireplace("{player}", $args[0], $this->banInfo["hasBeenBanned"]));
                    $p = $this->getServer()->getPlayer($args[0]);
                    if($p != null){
                        $p->kick($this->banInfo["banMessage"], false);
                    }
                } else {
                    $sender->sendMessage($this->general["noPlayerSpecified"]);
                }
                return true;
                break;
            case "unban":
                if(isset($args[0])){
                    $this->removeBan($args[0]);
                    $sender->sendMessage(str_ireplace("{player}", $args[0], $this->banInfo["hasBeenUnBanned"]));
                } else {
                    $sender->sendMessage($this->general["noPlayerSpecified"]);
                }
                return true;
                break;
            case "tempban":
                if(isset($args[0])){
                    $p = $this->getServer()->getPlayer($args[0]);
                    $time = 0;
                    if(isset($args[1])){
                        $arg = $args[1];
                        if(stripos($arg, "s") !== false){
                            $etime = str_ireplace("s", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + $etime;
                            }
                        }
                        if(stripos($arg, "m") !== false or stripos($arg, "min") !== false or stripos($arg, "mins") !== false){
                            $etime = str_ireplace("m", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + ($etime * 60);
                            }
                        }

                        if(stripos($arg, "h") !== false){
                            $etime = str_ireplace("h", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + ($etime * 60 * 60);
                            }
                        }

                        if(stripos($arg, "d") !== false){
                            $etime = str_ireplace("d", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + ($etime * 86400);
                                // 86,400
                            }
                        }

                        if(stripos($arg, "forever") !== false){
                            $time = 1;
                        }

                        if($time === 0){
                            $sender->sendMessage($this->general["invalidTimeValue"]);
                            return true;
                        }

                    } else {
                        // no time entered, banning forever
                        $time = 1;
                    }
                    if($this->isBanned($args[0])){
                        $sender->sendMessage($this->banInfo["alreadyBanned"]);
                        return true;
                    }
                    // adds player name and future timestamp to database
                    $this->addBan($args[0], $time, $sender->getName());
                    // if player is online then
                    if($p != null){
                        // kick them
                        $p->kick(TextFormat::RED . $this->banInfo["tempBanMessage"], false);
                    }
                    $sender->sendMessage(str_ireplace("{player}", $args[0], $this->banInfo["hasBeenTempBanned"]));

                } else {
                    $sender->sendMessage($this->general["noPlayerSpecified"]);
                }
                return true;
                break;
            case "listmutes":
                $mutes = $this->mutes;
                if($mutes === []){
                    $sender->sendMessage($this->muteInfo["noPlayersMuted"]);
                }
                foreach($mutes as $mute){
                    $sender->sendMessage(str_ireplace("{player}", $mute["name"], intval($mute["endTime"]) === 1 ? $this->muteInfo["permMutedListFormat"] : str_ireplace("{end_format}", $this->muteInfo["muteEndTimeFormat"], $this->muteInfo["tempMutedListFormat"])));
                }
                return true;
                break;
            case "listbans":
                $bans = $this->bans;
                if($bans === []){
                    $sender->sendMessage($this->banInfo["noPlayersBanned"]);
                }
                foreach($bans as $ban){
                    $sender->sendMessage(str_ireplace("{player}", $ban["name"], intval($ban["endTime"]) === 1 ? $this->banInfo["permBannedListFormat"] : str_ireplace("{end_format}", date($this->banInfo["banEndTimeFormat"], $ban["endTime"]), $this->banInfo["tempBannedListFormat"])));
                }
                return true;
                break;
            case "ismuted":
                if(isset($args[0])){
                    $name = $args[0];
                    $result = $this->isMuted($name);
                    if($result){
                        $info = $this->getMuteInfo($name);
                        $sender->sendMessage(intval($info["endTime"]) === 1 ? str_ireplace("{muter}", $info["mutedBy"], $this->muteInfo["mutedForeverMessageFormat"]) : str_ireplace("{muter}", $info["mutedBy"], $this->muteInfo["isMutedMessageFormat"]));
                    } else {
                        $sender->sendMessage(str_ireplace("{player}", $name, $this->cfg->get("notMuted")));
                    }
                } else {
                    $sender->sendMessage($this->general["noPlayerSpecified"]);
                }
                return true;
                break;
            case "isbanned":
                if(isset($args[0])){
                    $name = $args[0];
                    $result = $this->isBanned($name);
                    if($result){
                        $info = $this->getBanInfo($name);
                        var_dump("Value:" . intval($info["endTime"]));
                        $sender->sendMessage(intval($info["endTime"]) === 1 ? str_ireplace("{banner}", $info["bannedBy"], $this->banInfo["bannedForeverMessageFormat"]) : str_ireplace("{banner}", $info["bannedBy"], $this->banInfo["isBannedMessageFormat"]));
                    } else {
                        $sender->sendMessage(str_ireplace("{player}", $name, $this->banInfo["notBanned"]));
                    }
                } else {
                    $sender->sendMessage($this->general["noPlayerSpecified"]);
                }
                return true;
                break;
            case "unmute":
                if(isset($args[0])){
                    $name = $args[0];
                    $wasMuted = $this->isMuted($name);
                    if($wasMuted){
                        $this->removeMute($name);
                        $p = $this->getServer()->getPlayerExact($name);
                        if($p != null){
                            $p->sendMessage($this->muteInfo["unMuted"]);
                        }
                        $sender->sendMessage(str_ireplace("{player}", $name, $this->muteInfo["hasBeenUnMuted"]));
                    } else {
                        $sender->sendMessage(str_ireplace("{player}", $name, $this->muteInfo["notMuted"]));
                    }
                } else {
                    $sender->sendMessage($this->general["noPlayerSpecified"]);
                }
                return true;
                break;
            case "mute":
                if(isset($args[0])){
                    $p = $this->getServer()->getPlayer($args[0]);
                    $time = 0;
                    if(isset($args[1])){
                        $arg = $args[1];
                        if(stripos($arg, "s") !== false){
                            $etime = str_ireplace("s", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + $etime;
                            }
                        }
                        if(stripos($arg, "m") !== false or stripos($arg, "min") !== false or stripos($arg, "mins") !== false){
                            $etime = str_ireplace("m", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + ($etime * 60);
                            }
                        }

                        if(stripos($arg, "h") !== false){
                            $etime = str_ireplace("h", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + ($etime * 60 * 60);
                            }
                        }

                        if(stripos($arg, "d") !== false){
                            $etime = str_ireplace("d", "", $arg);
                            if(!(is_int(intval($etime)))){
                                $sender->sendMessage($this->general["invalidTimeValue"]);
                                return true;
                            } else {
                                $time = time() + ($etime * 86400);
                                // 86,400
                            }
                        }

                        if(stripos($arg, "forever") !== false){
                            $time = 1;
                        }

                        if($time === 0){
                            $sender->sendMessage($this->general["invalidTimeValue"]);
                            return true;
                        }

                    } else {
                        // no time entered, muting forever
                        $time = 1;
                    }
                    if($this->isMuted($args[0])){
                        $sender->sendMessage($this->muteInfo["alreadyMuted"]);
                        return true;
                    }
                    // adds player name and future timestamp to database
                    $this->addMute($args[0], $time, $sender->getName());
                    // if player is online then
                    if($p != null){
                        // tell them they have been muted
                        $p->sendMessage($this->muteInfo["muteMessage"]);
                    }
                    $sender->sendMessage(str_ireplace("{player}", $args[0], $this->muteInfo["hasBeenMuted"]));

                } else {
                    $sender->sendMessage($this->general["noPlayerSpecified"]);
                    return true;
                }
        }
    }

    public function tickMutes()
    {
        $SQLmutes = $this->db->query("SELECT * FROM " . $this->sqlData["muteTable"] . ";");
        unset($this->mutes);
        $this->mutes = [];
        if(is_bool($SQLmutes)) return;
        while (($row = $SQLmutes->fetch_assoc()) != null) {
            $this->mutes[] = $row;
        }
        foreach($this->mutes as $mute) {
            if(time() > intval($mute["endTime"])) {
                if (!(intval($mute["endTime"]) === 1)) {
                    $this->removeMute($mute["name"]);
                    $p = $this->getServer()->getPlayer($mute["name"]);
                    if ($p != null) {
                        $p->sendMessage($this->muteInfo["unMuted"]);
                    }
                }
            }
        }

    }

    public function tickBans()
    {
        $SQLbans = $this->db->query("SELECT * FROM " . $this->sqlData["banTable"] . ";");
        unset($this->bans);
        $this->bans = [];
        if(is_bool($SQLbans)) return;
        while (($row = $SQLbans->fetch_assoc()) != null) {
            $this->bans[] = $row;
        }
        foreach($this->bans as $ban) {
            if(time() > intval($ban["endTime"])) {
                if (!(intval($ban["endTime"]) === 1)) {
                    $this->removeBan($ban["name"]);
                }
            }
        }

    }

    public function addBan($name, $endTime, $bannedBy){
        $this->db->query("INSERT INTO " . $this->sqlData["banTable"] . " (name,startTime,endTime,bannedBy) VALUES (" .
            $this->prepare($name) . "," . time() . "," . $endTime . "," . $this->prepare($bannedBy) . ");");
    }

    public function removeBan($name){
        return $this->db->query("DELETE FROM " . $this->sqlData["banTable"] . " WHERE name=" . $this->prepare($name) . ";");
    }

    public function isBanned($name){
        $value = false;
        foreach($this->bans as $ban){
            if($name === $ban["name"])
            {
                $value = true;
            } else
            {
                $value = false;
            }
        }
        return $value;
    }

    public function addMute($name, $endTime, $mutedBy){
        return $this->db->query("INSERT INTO " . $this->sqlData["muteTable"] . " (name,startTime,endTime,mutedBy) VALUES (" .
            $this->prepare($name) . "," . time() . "," . $endTime . "," . $this->prepare($mutedBy) . ");");
    }

    public function removeMute($name){
        return $this->db->query("DELETE FROM " . $this->sqlData["muteTable"] . " WHERE name=" . $this->prepare($name) . ";");
    }

    public function onChat(PlayerChatEvent $ev){
        $p = $ev->getPlayer();
        if($this->isMuted($p->getName())){
            $ev->setCancelled(true);
            $p->sendMessage($this->muteInfo["youAreMuted"]);
        }
    }

    public function isMuted($name){
        $value = false;
        foreach($this->mutes as $mute){
            if($name === $mute["name"])
            {
                $value = true;
            } else
            {
                $value = false;
            }
        }
        return $value;
    }

    public function getMuteInfo($name){
        foreach($this->mutes as $mute){
            if(strtolower($mute["name"]) === strtolower($name)){
                return $mute;
            }
        }
    }

    public function getBanInfo($name){
        foreach($this->bans as $ban){
            if(strtolower($ban["name"]) === strtolower($name)){
                return $ban;
            }
        }
    }
}