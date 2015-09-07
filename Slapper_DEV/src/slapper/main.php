<?php

namespace slapper;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\format\FullChunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Short;
use pocketmine\nbt\tag\String;
use pocketmine\nbt\tag\Byte;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\protocol\AddPlayerPacket;

use slapper\entities\SlapperHuman;
use slapper\entities\HumanNPC;
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
//use slapper\entities\SlapperGhast;
//use slapper\entities\SlapperIronGolem;
//use slapper\entities\SlapperSnowman;
//use slapper\entities\SlapperOcelot;
use slapper\entities\SlapperPigZombie;
use slapper\entities\SlapperSlime;
use slapper\entities\SlapperMushroomCow;
use slapper\entities\SlapperChicken;
use slapper\entities\SlapperCow;
use slapper\entities\SlapperPig;
use slapper\entities\SlapperWolf;
use slapper\entities\SlapperSheep;





class main extends PluginBase implements Listener{

    public function onEnable()
    {


		Entity::registerEntity(SlapperCreeper::class,true);
		Entity::registerEntity(SlapperBat::class,true);
		Entity::registerEntity(SlapperSheep::class,true);
		Entity::registerEntity(SlapperPigZombie::class,true);
		//Entity::registerEntity(SlapperGhast::class,true);
		//Entity::registerEntity(SlapperIronGolem::class,true);
		//Entity::registerEntity(SlapperSnowman::class,true);
		//Entity::registerEntity(SlapperOcelot::class,true);
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
		switch($command->getName()){
			case 'nothing':
            		return true;
            		break;
            		/*case 'save':
            			$this->saveDefaultConfig();
            		        $this->getLogger()->debug("Config has been saved!!!");
            		        $sender->sendMessage("Files saved.");
            		return true;
            		break;
            		*/
			case 'rca':
            	if (count($args) < 2){
					$sender->sendMessage("Please enter a player and a command.");
					return false;
            	}
				$player = $this->getServer()->getPlayer($tuv = array_shift($args));
				if(!($player == null)){
					$commandForSudo = trim(implode(" ", $args));
					$this->getServer()->dispatchCommand($player, $commandForSudo);
					return true;
					break;
				}
            $sender->sendMessage(TextFormat::RED."Player not found.");
            return true;
            break;
			case "slapper":
          		if($sender instanceof Player){
					$type = array_shift($args);
					$name = str_replace("{color}","§",str_replace("{line}", "\n", trim(implode(" ", $args))));
					$number = count($args);
/* DEBUG CODE */
					$sender->sendMessage($type." And ".$number);
/* DEBUG CODE */
					if(($type === null || $type === "" || $type === " ")){ return false; }
						$defaultName = $sender->getDisplayName();
						if($name == null) $name = $defaultName;
							$senderSkin = $sender->getSkinData();
							$isSlim = $sender->isSkinSlim();
							$playerX = $sender->getX();
							$playerY = $sender->getY();
							$playerZ = $sender->getZ();
							$playerLevel = $sender->getLevel()->getName();
							$playerYaw = $sender->getYaw();
							$playerPitch = $sender->getPitch();
							$humanInv = $sender->getInventory();
							$pHealth = $sender->getHealth();
							$theOne = "Blank";
							$nameToSay = "Human";
							$didMatch = "No";
							foreach([
								"Chicken", "Pig", "Sheep","Cow", "Mooshroom", "MushroomCow", "Wolf", "Enderman", "Spider", "Skeleton", "PigZombie", "Creeper", "Slime", "Silverfish", "Villager", "Zombie", "Human", "Player", "Squid", /*"Ghast"*/"Bat", "CaveSpider", "LavaSlime"
							] as $entityType){
								if(strtolower($type) == strtolower($entityType)){
									$didMatch = "Yes";
									$theOne = $entityType;
								}
							}
							$typeToUse = "Nothing";
							if($theOne == "Human"){ $typeToUse = "SlapperHuman"; $subHeight = 0;}
							if($theOne == "Player"){ $typeToUse = "SlapperHuman"; $subHeight = 0;}
							if($theOne == "Pig"){ $typeToUse = "SlapperPig"; $subHeight = 0.05;}
							if($theOne == "Bat"){ $typeToUse = "SlapperBat"; $subHeight = 0;}
							if($theOne == "Cow"){ $typeToUse = "SlapperCow"; $subHeight = 0;}
							if($theOne == "Sheep"){ $typeToUse = "SlapperSheep"; $subHeight = 0;}
							if($theOne == "MushroomCow"){ $typeToUse = "SlapperMushroomCow"; $subHeight = 0;}
							if($theOne == "Mooshroom"){ $typeToUse = "SlapperMushroomCow"; $subHeight = 0;}
							if($theOne == "LavaSlime"){ $typeToUse = "SlapperLavaSlime"; $subHeight = 0;}
							if($theOne == "Enderman"){ $typeToUse = "SlapperEnderman"; $subHeight = 0;}
							if($theOne == "Zombie"){ $typeToUse = "SlapperZombie"; $subHeight = 0;}
							if($theOne == "Creeper"){ $typeToUse = "SlapperCreeper"; $subHeight = 0;}
							if($theOne == "Skeleton"){ $typeToUse = "SlapperSkeleton"; $subHeight = 0;}
							if($theOne == "Silverfish"){ $typeToUse = "SlapperSilverfish"; $subHeight = 0;}
							if($theOne == "Chicken"){ $typeToUse = "SlapperChicken"; $subHeight = 0;}
							if($theOne == "Villager"){ $typeToUse = "SlapperVillager"; $subHeight = 0;}
							if($theOne == "CaveSpider"){ $typeToUse = "SlapperCaveSpider"; $subHeight = 0;}
							if($theOne == "Spider"){ $typeToUse = "SlapperSpider"; $subHeight = 0;}
							if($theOne == "Squid"){ $typeToUse = "SlapperSquid"; $subHeight = 0;}
							if($theOne == "Wolf"){ $typeToUse = "SlapperWolf"; $subHeight = 0;}
							if($theOne == "Slime"){ $typeToUse = "SlapperSlime"; $subHeight = 0;}
							if($theOne == "PigZombie"){ $typeToUse = "SlapperPigZombie"; $subHeight = 0;}
							if($theOne == "ZombiePigman"){ $typeToUse = "SlapperPigZombie"; $subHeight = 0;}
								$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "say ".$didMatch.", ".$entityType.", ".$typeToUse.", ".$theOne.", ".$type);
							if(!($typeToUse == "Nothing") && !($theOne == "Blank")){
								$nbt = $this->makeNBT($subHeight,$senderSkin,$isSlim,$name,$pHealth,$humanInv,$playerYaw,$playerPitch,$playerX,$playerY,$playerZ,$type);
								$clonedHuman = Entity::createEntity($typeToUse, $sender->getLevel()->getChunk($playerX>>4, $playerZ>>4),$nbt);

							$sender->sendMessage(TextFormat::GREEN."[". TextFormat::YELLOW . "Slapper" . TextFormat::GREEN . "] ".$theOne." entity spawned with name ".TextFormat::WHITE."\"".TextFormat::BLUE.$name.TextFormat::WHITE."\"");
							}
								if($typeToUse == "Human" || $typeToUse == "Player"){
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
							if(!($theOne == "Blank"))
							$clonedHuman->spawnToAll();
							if($typeToUse == "Nothing" || $theOne == "Blank"){ $sender->sendMessage("Invalid entity."); }
							return true;
				}else{
					$sender->sendMessage("This command only works in game.");
					return true;
				}
		}
	}

	public function onEntityInteract(EntityDamageEvent $event) {
		$perm = "yeah";
		if ($event->isCancelled()) return;
		$taker = $event->getEntity();
		if($taker instanceof SlapperHuman || $taker instanceof SlapperSheep || $taker instanceof SlapperPigZombie || $taker instanceof SlapperVillager || $taker instanceof SlapperCaveSpider || $taker instanceof SlapperZombie || $taker instanceof SlapperChicken || $taker instanceof SlapperSpider || $taker instanceof SlapperSilverfish || $taker instanceof SlapperPig || $taker instanceof SlapperCow || $taker instanceof SlapperSlime || $taker instanceof SlapperLavaSlime || $taker instanceof SlapperEnderman || $taker instanceof SlapperMushroomCow || $taker instanceof SlapperBat || $taker instanceof SlapperCreeper || $taker instanceof SlapperSkeleton || $taker instanceof SlapperSquid || $taker instanceof SlapperWolf){
		if(!($event instanceof EntityDamageByEntityEvent)){ $event->setCancelled(); }
		if($event instanceof EntityDamageByEntityEvent){
			$hitter = $event->getDamager();
			$takerName = str_replace("\n", "", $taker->getName());
			$giverName = $hitter->getName();
			if($hitter instanceof Player){
					$configPart = $this->getConfig()->get($takerName);
					if(!($hitter->hasPermission("slapper.hit"))){ $event->setCancelled(true); $perm = "nah";}
					if($configPart == null && $perm == "nah"){
						$configPart = $this->getConfig()->get("FallbackCommand");
					}
					foreach($configPart as $commandNew){
					if($perm == "nah")
						$this->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{player}", $giverName, $commandNew));
					}
				}

			}
		}
	}


  private function makeNBT($subHeight, $senderSkin, $isSlim, $name, $pHealth, $humanInv, $playerYaw, $playerPitch, $playerX, $playerY, $playerZ, $type){
  $nbt = new Compound;
        $playerY -= $subHeight;
        $nbt->Pos = new Enum("Pos", [
           new Double("", $playerX),
           new Double("", $playerY),
           new Double("", $playerZ)
        ]);
        $nbt->Rotation = new Enum("Rotation", [
            new Float("", $playerYaw),
            new Float("", $playerPitch)
     ]);
        $nbt->Rotation = new Enum("Rotation", [
            new Float("", $playerYaw),
            new Float("", $playerPitch)
        ]);
        $nbt->Health = new Short("Health", $pHealth);
        $nbt->Inventory = new Enum("Inventory", $humanInv);
        $nbt->NameTag = new String("name",$name);
        $nbt->CustomName = new String("CustomName",$name);
        $nbt->CustomNameVisible = new Byte("CustomNameVisible", 1);
        $nbt->Invulnerable = new Byte("Invulnerable", 1);
        $nbt->IsSlapper = new Byte("IsSlapper", 1);
        $nbt->CustomTestTag = new Byte("CustomTestTag", 1);
        $nbt->BatFlags = new Byte("BatFlags", 0);
        $nbt->Skin = new Compound("Skin", [
          "Data" => new String("Data", $senderSkin),
          "Slim" => new Byte("Slim", $isSlim)
        ]);
    return $nbt;
    }

}
