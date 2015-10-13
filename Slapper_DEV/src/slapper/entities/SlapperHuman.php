<?php
namespace slapper\entities;

use pocketmine\Player;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\Network;

class SlapperHuman extends HumanNPC{

	public function spawnTo(Player $player){
		if($player !== $this and !isset($this->hasSpawned[$player->getLoaderId()])){
			$this->hasSpawned[$player->getLoaderId()] = $player;

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
            if($player->hasPermission("slapper.seeownskin")){
                $pk->skin = $player->getSkinData();
			    $pk->slim = $player->isSkinSlim();
			} else {
			    $pk->skin = $this->skin;
                $pk->slim = $this->isSlim;
			}
            $pk->metadata = $this->dataProperties;
			$player->dataPacket($pk->setChannel(Network::CHANNEL_ENTITY_SPAWNING));

			$this->inventory->sendArmorContents($player);
		}
	}



}