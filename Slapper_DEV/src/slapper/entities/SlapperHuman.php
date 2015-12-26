<?php
namespace slapper\entities;

use pocketmine\Player;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;

class SlapperHuman extends HumanNPC{

	public function spawnTo(Player $player){
		if($player !== $this and !isset($this->hasSpawned[$player->getLoaderId()])){
			$this->hasSpawned[$player->getLoaderId()] = $player;

            $uuid = $this->getUniqueId();
            $entityId = $this->getId();

            $pk = new AddPlayerPacket();
			$pk->uuid = $uuid;
			$pk->username = $this->getName();
			$pk->eid = $entityId;
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->speedX = 0;
			$pk->speedY = 0;
			$pk->speedZ = 0;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->item = $this->getInventory()->getItemInHand();
			$pk->metadata = [
                2 => [4, $this->getDataProperty(2)],
				3 => [0, $this->getDataProperty(3)],
				15 => [0, 1]
            ];
			$player->dataPacket($pk);

			$this->inventory->sendArmorContents($player);

			$add = new PlayerListPacket();
            $add->type = 0;
			$add->entries[] = [$uuid, $entityId, isset($this->namedtag->MenuName) ? $this->namedtag["MenuName"]: "", $this->skinName, $this->skin];
			$player->dataPacket($add);
			if($this->namedtag["MenuName"] === "") {
				$remove = new PlayerListPacket();
				$remove->type = 1;
				$remove->entries[] = [$uuid];
				$player->dataPacket($remove);
			}
		}
	}



}