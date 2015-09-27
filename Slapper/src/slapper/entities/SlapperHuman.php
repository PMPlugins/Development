<?php
namespace slapper\entities;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\Network;
use pocketmine\entity\Entity;
use slapper\main;
use pocketmine\Server;
use pocketmine\utils\Config;
class SlapperHuman extends HumanNPC{

	public function spawnTo(Player $player){
		if($player !== $this and !isset($this->hasSpawned[$player->getLoaderId()])){
			$this->hasSpawned[$player->getLoaderId()] = $player;

			if(strlen($this->skin) < 64 * 32 * 4){
				throw new \InvalidStateException((new \ReflectionClass($this))->getShortName() . " must have a valid skin set");
			}

			$pk = new AddPlayerPacket();
			$pk->uuid = $this->getUniqueId();
			$pk->username = $this->getName();
			$pk->eid = $this->getId();
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->speedX = 0;
			$pk->speedY = 0;
			$pk->speedZ = 0;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$item = $this->getInventory()->getItemInHand();
			$pk->item = $item;
			//$pk->skin = $this->skin;
			//$pk->slim = $this->isSlim;
			// Help wanted!!! (To make this work) if($this->getConfig()->get("PlayerSkin")){
			    $pk->skin = $player->getSkinData();
			    $pk->slim = $player->isSkinSlim();
			//}
			$pk->metadata = $this->dataProperties;
			$player->dataPacket($pk->setChannel(Network::CHANNEL_ENTITY_SPAWNING));

			$this->inventory->sendArmorContents($player);
		}
	}



}