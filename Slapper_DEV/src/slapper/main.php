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

//use slapper\entities\SlapperBat;
use slapper\entities\SlapperZombie;
//use slapper\entities\SlapperSkeleton;
//use slapper\entities\SlapperCreeper;
//use slapper\entities\SlapperEnderman;
//use slapper\entities\SlapperLavaSlime;
//use slapper\entities\SlapperSilverfish;
//use slapper\entities\SlapperSpider;
use slapper\entities\SlapperVillager;
//use slapper\entities\SlapperSquid;
use slapper\entities\SlapperCaveSpider;
//use slapper\entities\SlapperGhast;
//use slapper\entities\SlapperPigZombie;
//use slapper\entities\SlapperSlime;
//use slapper\entities\SlapperMushroomCow;
use slapper\entities\SlapperChicken;
//use slapper\entities\SlapperCow;
//use slapper\entities\SlapperPig;
//use slapper\entities\SlapperWolf;
//use slapper\entities\SlapperSheep;





class main extends PluginBase implements Listener{
    public function onLoad()
    {
        $this->getLogger()->info("Slapper is loaded!");
    }
    public function onEnable()
    {

		//Entity::registerEntity(SlapperBat::class,true);
		//Entity::registerEntity(SlapperSheep::class,true);
		//Entity::registerEntity(SlapperPigZombie::class,true);
		//Entity::registerEntity(SlapperGhast::class,true);
		Entity::registerEntity(SlapperHuman::class,true);
		Entity::registerEntity(SlapperVillager::class,true);
		Entity::registerEntity(SlapperZombie::class,true);
		//Entity::registerEntity(SlapperSquid::class,true);
		//Entity::registerEntity(SlapperCow::class,true);
		//Entity::registerEntity(SlapperSpider::class,true);
		//Entity::registerEntity(SlapperPig::class,true);
		//Entity::registerEntity(SlapperMushroomCow::class,true);
		//Entity::registerEntity(SlapperWolf::class,true);
		//Entity::registerEntity(SlapperLavaSlime::class,true);
		//Entity::registerEntity(SlapperSilverFish::class,true);
		//Entity::registerEntity(SlapperSkeleton::class,true);
		//Entity::registerEntity(SlapperSlime::class,true);
		Entity::registerEntity(SlapperChicken::class,true);
		//Entity::registerEntity(SlapperEnderman::class,true);
		Entity::registerEntity(SlapperCaveSpider::class,true);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->getLogger()->info("Slapper is enabled! Time to slap!");
   }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch($command->getName()){
			case 'nothing':
            		return true;
            		break;
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
					$name = trim(implode(" ", $args));
					$number = count($args);
					$sender->sendMessage($type." And ".$number);
					if(($type === null || $type === "" || $type === " ")){ return false; }
						$defaultName = $sender->getDisplayName();
						if($name == null) $name = $defaultName;
							$senderSkin = $sender->getSkinData();
							$isSlim = $sender->isSkinSlim();
							$playerX = $sender->getX();
							$playerY = $sender->getY();
							$playerZ = $sender->getZ();
							$outX=round($playerX,1);
							$outY=round($playerY,1);
							$outZ=round($playerZ,1);
							$playerLevel = $sender->getLevel()->getName();
							$playerYaw = $sender->getYaw();
							$playerPitch = $sender->getPitch();
							$humanInv = $sender->getInventory();
							$pHealth = $sender->getHealth();
							$theOne = "Blank";
							$nameToSay = "Human";
							$didMatch = "No";
							foreach([
								"Chicken", "Pig", "Sheep","Cow", "Mooshroom", "Wolf", "Enderman", "Spider", " Skeleton", "PigZombie", "Creeper", "Slime", "Silverfish", "Villager", "Zombie", "Human", "Player", "Squid", /*"Ghast"*/"Bat", "CaveSpider", "LavaSlime"
							] as $entityType){
								if($type == $entityType){ 
									$didMatch = "Yes"; 
									$theOne = $entityType; 
								}
							}
							$typeToUse = "Nothing";
							if($theOne == "Human"){ $typeToUse = "SlapperHuman"; $subHeight = 0;}
							if($theOne == "Player"){ $typeToUse = "SlapperHuman"; $subHeight = 0;}
							if($theOne == "Pig"){ $typeToUse = "SlapperPig"; $subHeight = 1;}
							if($theOne == "Bat"){ $typeToUse = "SlapperBat"; $subHeight = 1;}
							if($theOne == "LavaSlime"){ $typeToUse = "SlapperLavaSlime"; $subHeight = 1;}
							if($theOne == "Enderman"){ $typeToUse = "SlapperEnderman"; $subHeight = 0;}
							if($theOne == "Zombie"){ $typeToUse = "SlapperZombie"; $subHeight = 0;}
							if($theOne == "Skeleton"){ $typeToUse = "SlapperSkeleton"; $subHeight = 0;}
							if($theOne == "Creeper"){ $typeToUse = "SlapperCreeper"; $subHeight = 0;}
							if($theOne == "Silverfish"){ $typeToUse = "SlapperSilverfish"; $subHeight = 1;}
							if($theOne == "Chicken"){ $typeToUse = "SlapperChicken"; $subHeight = 0.85;}
							if($theOne == "Villager"){ $typeToUse = "SlapperVillager"; $subHeight = 0;}
							if($theOne == "CaveSpider"){ $typeToUse = "SlapperCaveSpider"; $subHeight = 1;}
								$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "say ".$didMatch.", ".$entityType.", ".$typeToUse.", ".$theOne.", ".$type);
							if(!($typeToUse == "Nothing")){
								$nbt = $this->getNBT($subHeight,$senderSkin,$isSlim,$name,$pHealth,$humanInv,$playerYaw,$playerPitch,$playerX,$playerY,$playerZ);
								$clonedHuman = Entity::createEntity($typeToUse, $sender->getLevel()->getChunk($playerX>>4, $playerZ>>4),$nbt);
							}
							$sender->sendMessage(TextFormat::GREEN."[". TextFormat::YELLOW . "Slapper" . TextFormat::GREEN . "] ".$theOne." entity spawned with name ".TextFormat::WHITE."\"".TextFormat::BLUE.$name.TextFormat::WHITE."\"");
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
							$clonedHuman->spawnToAll();
							if($typeToUse == "Nothing"){ $sender->sendMessage("Invalid entity."); }
							return true;
				}else{
					$sender->sendMessage("This command only works in game.");
					return true;
				}
		}
	}

	public function onEntityInteract(EntityDamageEvent $ev) {
		if ($ev->isCancelled()) return;
		if($event instanceof EntityDamageByEntityEvent){
			$taker = $event->getEntity();
			$hiter = $event->getDamager();
			if($hiter instanceof Player){
				if($taker instanceof SlapperHuman || $taker instanceof SlapperVillager || $taker instanceof SlapperCaveSpider || $taker instanceof SlapperZombie || $taker instanceof SlapperChicken || $taker instanceof SlapperSpider || $taker instanceof SlapperSilverfish || $taker instanceof SlapperPig || $taker instanceof SlapperCow || $taker instanceof SlapperSlime || $taker instanceof SlapperLavaSlime || $taker instanceof SlapperEnderman){
					$configPart = $this->getConfig()->get($takerName);
					if($configPart == null){ 
						$configPart = $this->getConfig()->get("FallbackCommand"); 
					}
					foreach($configPart as $commandNew){
						$this->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{player}", $giverName, $commandNew));
					}
				}
			}
		}
	}

	public function onEntitySpawn(EntitySpawnEvent $ev) {
		$entity = $ev->getEntity();
		if($entity instanceof SlapperCaveSpider /*or $entity instanceof SlapperSpider or $entity instanceof SlapperZombie or $entity instanceof SlapperVillager or $entity instanceof SlapperHuman or $entity instanceof SlapperCreeper or $entity instanceof SlapperBat or $entity instanceof SlapperSkeleton or $entity instanceof SlapperEnderman*/){
		$this->getServer()->broadcastMessage("Test");
		}
	}

  private function getNBT($subHeight,$senderSkin,$isSlim,$name,$pHealth,$humanInv,$playerYaw,$playerPitch,$playerX,$playerY,$playerZ){
  $nbt = new Compound;
        $motion = new Vector3(0,-0.5,0);
        $playerY -= $subHeight;
        $nbt->Pos = new Enum("Pos", [
           new Double("", $playerX),
           new Double("", $playerY),
           new Double("", $playerZ)
        ]);
        $nbt->Motion = new Enum("Motion", [
           new Double("", $motion->x),
           new Double("", $motion->y),
           new Double("", $motion->z)
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
        $nbt->CustomTestTag = new Byte("CustomTestTag", 1);
        $nbt->Skin = new Compound("Skin", [
          "Data" => new String("Data", $senderSkin),
          "Slim" => new Byte("Slim", $isSlim)
        ]);
    return $nbt;
    }

}
