<?php

namespace slapper;

use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Short;
use pocketmine\nbt\tag\String;
use pocketmine\nbt\tag\Byte;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use slapper\entities\SlapperHuman;
use slapper\entities\SlapperBat;
use slapper\entities\SlapperZombie;
use slapper\entities\SlapperSkeleton;
use slapper\entities\SlapperCreeper;
use slapper\entities\SlapperEnderman;
use slapper\entities\SlapperLavaSlime;
use slapper\entities\SlapperSilverfish;
use slapper\entities\SlapperSpider;
use slapper\entities\SlapperVillager;
use slapper\entities\SlapperSquid;
use slapper\entities\SlapperCaveSpider;
/*
use slapper\entities\SlapperGhast;
use slapper\entities\SlapperIronGolem;
use slapper\entities\SlapperSnowman;
use slapper\entities\SlapperOcelot;
*/
use slapper\entities\SlapperPigZombie;
use slapper\entities\SlapperSlime;
use slapper\entities\SlapperMushroomCow;
use slapper\entities\SlapperChicken;
use slapper\entities\SlapperCow;
use slapper\entities\SlapperPig;
use slapper\entities\SlapperWolf;
use slapper\entities\SlapperSheep;


class main extends PluginBase implements Listener{

    public $hitSessions;
    public $idSessions;
    public $prefix = (TextFormat::GREEN."[".TextFormat::YELLOW."Slapper".TextFormat::GREEN."] ");
    public $helpHeader =
        (
        TextFormat::YELLOW."---------- ".
        TextFormat::GREEN."[".TextFormat::YELLOW."Slapper Help".TextFormat::GREEN."] ".
        TextFormat::YELLOW."----------"
        );
    public $mainArgs = ["help: /slapper help", "spawn: /slapper spawn <type> [name]", "id: /slapper id", "remove: /slapper remove [id]"];

    public function onEnable(){
		$this->hitSessions = [];
		$this->idSessions = [];
		Entity::registerEntity(SlapperCreeper::class,true);
		Entity::registerEntity(SlapperBat::class,true);
		Entity::registerEntity(SlapperSheep::class,true);
		Entity::registerEntity(SlapperPigZombie::class,true);
		/*
		Entity::registerEntity(SlapperGhast::class,true);
		Entity::registerEntity(SlapperIronGolem::class,true);
		Entity::registerEntity(SlapperSnowman::class,true);
		Entity::registerEntity(SlapperOcelot::class,true);
		*/
		Entity::registerEntity(SlapperHuman::class,true);
		Entity::registerEntity(SlapperVillager::class,true);
		Entity::registerEntity(SlapperZombie::class,true);
		Entity::registerEntity(SlapperSquid::class,true);
		Entity::registerEntity(SlapperCow::class,true);
		Entity::registerEntity(SlapperSpider::class,true);
		Entity::registerEntity(SlapperPig::class,true);
		Entity::registerEntity(SlapperMushroomCow::class,true);
		Entity::registerEntity(SlapperWolf::class,true);
		Entity::registerEntity(SlapperLavaSlime::class,true);
		Entity::registerEntity(SlapperSilverfish::class,true);
		Entity::registerEntity(SlapperSkeleton::class,true);
		Entity::registerEntity(SlapperSlime::class,true);
		Entity::registerEntity(SlapperChicken::class,true);
		Entity::registerEntity(SlapperEnderman::class,true);
		Entity::registerEntity(SlapperCaveSpider::class,true);
	    $this->getLogger()->debug("Entities have been registered!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->debug("Events have been registered!");
        $this->saveDefaultConfig();
        $this->getLogger()->debug("Config has been saved!!!");
        $this->getLogger()->info("Slapper is enabled! Time to slap!");
   }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch(strtolower($command->getName())){
			case 'nothing':
            		return true;
            		break;
			case 'rca':
            	if (count($args) < 2){
					$sender->sendMessage("Please enter a player and a command.");
					return true;
            	}
				$player = $this->getServer()->getPlayer(array_shift($args));
				if(!($player === null)){
					$this->getServer()->dispatchCommand($player, trim(implode(" ", $args)));
					return true;
					break;
				}
                $sender->sendMessage(TextFormat::RED."Player not found.");
                return true;
                break;
			case "slapper":
          		if($sender instanceof Player){
          			if(!(isset($args[0]))){
			    		if($sender->hasPermission("slapper.noargs")){
                            $sender->sendMessage($this->prefix."Please type '/slapper help' for a list of commands.");
			            } else {
			                $sender->sendMessage($this->prefix."You don't have permission.");
			            }
			        }
					$arg = array_shift($args);
					switch($arg){
                        case "id":
                            if($sender->hasPermission("slapper.id")){
                                $this->idSessions[$sender->getName()] = true;
                                $sender->sendMessage($this->prefix."Hit an entity to get its ID!");
                                return true;
                            }
                        break;
                        case "remove":
                            if($sender->hasPermission("slapper.remove")){
                                if(isset($args[0])){
                                    $entity = $sender->getLevel()->getEntity($args[0]);
                                    if(!($entity == null)){
                                        if(
                                            $entity instanceof SlapperHuman ||
                                            $entity instanceof SlapperSheep ||
                                            $entity instanceof SlapperPigZombie ||
                                            $entity instanceof SlapperVillager ||
                                            $entity instanceof SlapperCaveSpider ||
                                            $entity instanceof SlapperZombie ||
                                            $entity instanceof SlapperChicken ||
                                            $entity instanceof SlapperSpider ||
                                            $entity instanceof SlapperSilverfish ||
                                            $entity instanceof SlapperPig ||
                                            $entity instanceof SlapperCow ||
                                            $entity instanceof SlapperSlime ||
                                            $entity instanceof SlapperLavaSlime ||
                                            $entity instanceof SlapperEnderman ||
                                            $entity instanceof SlapperMushroomCow ||
                                            $entity instanceof SlapperBat ||
                                            $entity instanceof SlapperCreeper ||
                                            $entity instanceof SlapperSkeleton ||
                                            $entity instanceof SlapperSquid ||
                                            $entity instanceof SlapperWolf
                                        ){
                                            if($entity instanceof SlapperHuman) $entity->getInventory()->clearAll();
                                            $entity->kill();
                                            $sender->sendMessage($this->prefix."Entity removed.");
                                        } else {
                                            $sender->sendMessage($this->prefix."That entity is not handled by Slapper.");
                                        }
                                    } else {
                                        $sender->sendMessage($this->prefix."Entity does not exist.");
                                    }
                                return true;
                                }
                            $this->hitSessions[$sender->getName()] = true;
                            $sender->sendMessage($this->prefix."Hit an entity to remove it.");
                            } else {
                                $sender->sendMessage($this->prefix."You don't have permission.");
                            }
                            break;
                        case "edit":
                            if($sender->hasPermission("slapper.edit")){
                                if(isset($args[0])){
                                    $entity = $sender->getLevel()->getEntity($args[0]);
                                    if(!($entity == null)){
                                        if(
                                            $entity instanceof SlapperHuman ||
                                            $entity instanceof SlapperSheep ||
                                            $entity instanceof SlapperPigZombie ||
                                            $entity instanceof SlapperVillager ||
                                            $entity instanceof SlapperCaveSpider ||
                                            $entity instanceof SlapperZombie ||
                                            $entity instanceof SlapperChicken ||
                                            $entity instanceof SlapperSpider ||
                                            $entity instanceof SlapperSilverfish ||
                                            $entity instanceof SlapperPig ||
                                            $entity instanceof SlapperCow ||
                                            $entity instanceof SlapperSlime ||
                                            $entity instanceof SlapperLavaSlime ||
                                            $entity instanceof SlapperEnderman ||
                                            $entity instanceof SlapperMushroomCow ||
                                            $entity instanceof SlapperBat ||
                                            $entity instanceof SlapperCreeper ||
                                            $entity instanceof SlapperSkeleton ||
                                            $entity instanceof SlapperSquid ||
                                            $entity instanceof SlapperWolf
                                        ){
                                            if(isset($args[1])){
                                                switch($args[1]){
                                                    case "skin":
                                                        if($entity instanceof SlapperHuman){
                                                            $entity->setSkin($sender->getSkinData(), $sender->isSkinSlim());
                                                            $sender->sendMessage($this->prefix."Skin updated.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix."That entity can't have a skin.");
                                                        }
                                                        return true;
                                                    case "name":
                                                        if(isset($args[2])){
                                                            array_shift($args);
                                                            array_shift($args);
                                                            $entity->setDataProperty(2, Entity::DATA_TYPE_STRING, trim(implode(" ", $args)));
                                                            $sender->sendMessage($this->prefix."Name updated.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix."Please enter a name.");
                                                        }
                                                        return true;
                                                    case "addc":
                                                    case "addcmd":
                                                    case "addcommand":
                                                        if(isset($args[2])){
                                                            array_shift($args);
                                                            array_shift($args);
                                                            $input = trim(implode(" ", $args));
                                                            $entity->addCommand($input);
                                                            $sender->sendMessage($this->prefix."Command added.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix."Please enter a command.");
                                                        }
                                                        return true;
                                                    case "delc":
                                                    case "delcmd":
                                                    case "removecommand":
                                                        if(isset($args[2])){
                                                            array_shift($args);
                                                            array_shift($args);
                                                            $input = trim(implode(" ", $args));
                                                            unset($entity->namedtag->Commands[$input]);
                                                            $sender->sendMessage($this->prefix."Command removed.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix."Please enter a command.");
                                                        }
                                                        return true;
                                                    case "listcommands":
                                                    case "listcmds":
                                                    case "listcs":
                                                        if(isset($entity->namedtag->Commands)){
                                                            foreach($entity->namedtag->Commands as $cmd){
                                                                $sender->sendMessage(TextFormat::GREEN."[".TextFormat::YELLOW."S".TextFormat::GREEN."] "."$cmd\n");
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix."That entity does not have any commands.");
                                                        }
                                                        return true;
                                                    case "update":
                                                    case "fix":
                                                    case "migrate":
                                                        if($this->getConfig()->get($entity->getName()) !== null){
                                                            foreach($this->getConfig()->get($entity->getName()) as $cmd){
                                                                $entity->addCommand($cmd);
                                                            }
                                                            $sender->sendMessage($this->prefix."Commands migrated.");
                                                        }
                                                        return true;
                                                }
                                            }
                                        } else {
                                            $sender->sendMessage($this->prefix."That entity is not handled by Slapper.");
                                        }
                                    } else {
                                        $sender->sendMessage($this->prefix."Entity does not exist.");
                                    }
                                    return true;
                                }
                                $this->hitSessions[$sender->getName()] = true;
                                $sender->sendMessage($this->prefix."Hit an entity to remove it.");
                            } else {
                                $sender->sendMessage($this->prefix."You don't have permission.");
                            }
                            return true;
                            break;
                        case "help":
                        case "?":
                            $sender->sendMessage($this->helpHeader);
                            foreach ($this->mainArgs as $msgArg){
                                $sender->sendMessage(TextFormat::GREEN . " - " . $msgArg . "\n");
                            }
                            break;
                        case "add":
                        case "make":
                        case "create":
					    case "spawn":
					        $type = array_shift($args);
                            $spawn = true;
					        $name = str_replace("{color}", "§",str_replace("{line}", "\n", trim(implode(" ", $args))));
					        if($type === null || $type === "" || $type === " "){ $sender->sendMessage($this->prefix."Please enter an entity type."); return true; }
						    $defaultName = $sender->getDisplayName();
                            if($name == null) $name = $defaultName;
							$senderSkin = $sender->getSkinData();
							$isSlim = $sender->isSkinSlim();
							$playerX = $sender->getX();
							$playerY = $sender->getY();
							$playerZ = $sender->getZ();
						    $playerYaw = $sender->getYaw();
							$playerPitch = $sender->getPitch();
							$humanInv = $sender->getInventory();
						    $theOne = "Blank";
                            foreach([
								"Chicken", "ZombiePigman", "Pig", "Sheep","Cow", "Mooshroom", "MushroomCow", "Wolf", "Enderman", "Spider", "Skeleton", "PigZombie", "Creeper", "Slime", "Silverfish", "Villager", "Zombie", "Human", "Player", "Squid", /*"Ghast"*/"Bat", "CaveSpider", "LavaSlime"
							] as $entityType){
								if(strtolower($type) === strtolower($entityType)){
                                    $theOne = $entityType;
								}
							}
							$typeToUse = "Nothing";
	                        if($theOne === "Human") $typeToUse = "SlapperHuman";
							if($theOne === "Player") $typeToUse = "SlapperHuman";
							if($theOne === "Pig") $typeToUse = "SlapperPig";
							if($theOne === "Bat") $typeToUse = "SlapperBat";
							if($theOne === "Cow") $typeToUse = "SlapperCow";
							if($theOne === "Sheep") $typeToUse = "SlapperSheep";
							if($theOne === "MushroomCow") $typeToUse = "SlapperMushroomCow";
							if($theOne === "Mooshroom") $typeToUse = "SlapperMushroomCow";
							if($theOne === "LavaSlime") $typeToUse = "SlapperLavaSlime";
							if($theOne === "Enderman") $typeToUse = "SlapperEnderman";
							if($theOne === "Zombie") $typeToUse = "SlapperZombie";
							if($theOne === "Creeper") $typeToUse = "SlapperCreeper";
							if($theOne === "Skeleton") $typeToUse = "SlapperSkeleton";
							if($theOne === "Silverfish") $typeToUse = "SlapperSilverfish";
							if($theOne === "Chicken") $typeToUse = "SlapperChicken";
							if($theOne === "Villager") $typeToUse = "SlapperVillager";
							if($theOne === "CaveSpider") $typeToUse = "SlapperCaveSpider";
							if($theOne === "Spider") $typeToUse = "SlapperSpider";
							if($theOne === "Squid") $typeToUse = "SlapperSquid";
							if($theOne === "Wolf") $typeToUse = "SlapperWolf";
							if($theOne === "Slime") $typeToUse = "SlapperSlime";
							if($theOne === "PigZombie") $typeToUse = "SlapperPigZombie";
							if($theOne === "MagmaCube") $typeToUse = "SlapperLavaSlime";
							if($theOne === "ZombiePigman") $typeToUse = "SlapperPigZombie";
							if($theOne === "PigZombie") $typeToUse = "SlapperPigZombie";
							if(!($typeToUse === "Nothing") && !($theOne === "Blank")){
								$nbt = $this->makeNBT($senderSkin,$isSlim,$name,$humanInv,$playerYaw,$playerPitch,$playerX,$playerY,$playerZ);
								$clonedHuman = Entity::createEntity($typeToUse, $sender->getLevel()->getChunk($playerX>>4, $playerZ>>4),$nbt);

							$sender->sendMessage($this->prefix.$theOne." entity spawned with name ".TextFormat::WHITE."\"".TextFormat::BLUE.$name.TextFormat::WHITE."\"");
							}
								if($typeToUse === "SlapperHuman"){
									$Inv = $clonedHuman->getInventory();
									$pHelm = $humanInv->getHelmet();
									$pChes = $humanInv->getChestplate();
									$pLegg = $humanInv->getLeggings();
									$pBoot = $humanInv->getBoots();

									$Inv->setHelmet($pHelm);
									$Inv->setChestplate($pChes);
									$Inv->setLeggings($pLegg);
									$Inv->setBoots($pBoot);
									$clonedHuman->getInventory()->setHeldItemSlot($sender->getInventory()->getHeldItemSlot());
									$clonedHuman->getInventory()->setItemInHand($sender->getInventory()->getItemInHand());
								}
							if(!($theOne == "Blank")) $clonedHuman->spawnToAll();
							if($typeToUse === "Nothing" || $theOne === "Blank"){
							    if($spawn) $sender->sendMessage($this->prefix."Invalid entity.");
							}
							return true;
						}
                }else{
					$sender->sendMessage($this->prefix."This command only works in game.");
					return true;
				}
		}
	}

	public function onEntityDamage(EntityDamageEvent $event) {
		$perm = true;
		if ($event->isCancelled()) return; //IDK why but I have a feeling this is wrong...
		$taker = $event->getEntity();
		if(
		    $taker instanceof SlapperHuman ||
		    $taker instanceof SlapperSheep ||
		    $taker instanceof SlapperPigZombie ||
		    $taker instanceof SlapperVillager ||
		    $taker instanceof SlapperCaveSpider ||
		    $taker instanceof SlapperZombie ||
		    $taker instanceof SlapperChicken ||
		    $taker instanceof SlapperSpider ||
		    $taker instanceof SlapperSilverfish ||
		    $taker instanceof SlapperPig ||
		    $taker instanceof SlapperCow ||
		    $taker instanceof SlapperSlime ||
		    $taker instanceof SlapperLavaSlime ||
		    $taker instanceof SlapperEnderman ||
		    $taker instanceof SlapperMushroomCow ||
		    $taker instanceof SlapperBat ||
		    $taker instanceof SlapperCreeper ||
		    $taker instanceof SlapperSkeleton ||
		    $taker instanceof SlapperSquid ||
		    $taker instanceof SlapperWolf
		){
		if(!($event instanceof EntityDamageByEntityEvent)) $event->setCancelled(true);
		if($event instanceof EntityDamageByEntityEvent){
			$hitter = $event->getDamager();
			if(!$hitter instanceof Player){
                $event->setCancelled(true);
			}
			if($hitter instanceof Player){
                $giverName = $hitter->getName();
			    if($hitter instanceof Player){
				    if(isset($this->hitSessions[$giverName])){
                            if($taker instanceof SlapperHuman) $taker->getInventory()->clearAll();
							$taker->kill();
                            unset($this->hitSessions[$giverName]);
                            $hitter->sendMessage($this->prefix."Entity removed.");
                            return;
                    }
                    if(isset($this->idSessions[$giverName])){
							$hitter->sendMessage($this->prefix."Entity ID: ".$taker->getId());
                            unset($this->idSessions[$giverName]);
                            $event->setCancelled();
                            return;
                    }
					if(!($hitter->hasPermission("slapper.hit"))){
					    $event->setCancelled(true);
					    $perm = false;
					}
					if($perm == false){
					    if(isset($taker->namedtag->Commands)){
					        foreach($taker->namedtag->Commands as $cmd){
						        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{player}", $giverName, $cmd));
					        }
					    }
					}
				}

			}
			}
		}

	}


	private function makeNBT($skin, $slim, $name, $inv, $yaw, $pitch, $x, $y, $z){
	    $nbt = new Compound;
        $nbt->Pos = new Enum("Pos", [
           new Double("", $x),
           new Double("", $y),
           new Double("", $z)
        ]);
        $nbt->Rotation = new Enum("Rotation", [
            new Float("", $yaw),
            new Float("", $pitch)
		]);
        $nbt->Health = new Short("Health", 1);
        $nbt->Inventory = new Enum("Inventory", $inv);
        $nbt->CustomName = new String("CustomName",$name);
        $nbt->CustomNameVisible = new Byte("CustomNameVisible", 1);
        $nbt->Invulnerable = new Byte("Invulnerable", 1);
        $nbt->Skin = new Compound("Skin", [
          "Data" => new String("Data", $skin),
          "Slim" => new Byte("Slim", $slim)
        ]);
        /* Slapper NBT info */
        $nbt->Commands = new Compound("Commands", []);
        $nbt->SlapperVersion = new String("SlapperVersion", "1.2.7");

		return $nbt;
    }
}
