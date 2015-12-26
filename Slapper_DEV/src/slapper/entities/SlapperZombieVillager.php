<?php

namespace slapper\entities;

use pocketmine\nbt\tag\Int;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\Network;
use pocketmine\Player;
use pocketmine\entity\Entity;

class SlapperZombieVillager extends Entity{

	const PROFESSION_FARMER = 0;
	const PROFESSION_LIBRARIAN = 1;
	const PROFESSION_PRIEST = 2;
	const PROFESSION_BLACKSMITH = 3;
	const PROFESSION_BUTCHER = 4;
	const PROFESSION_GENERIC = 5;

	const NETWORK_ID = 44;

	public function getName(){
		return $this->getDataProperty(2);
	}

	public function isBaby(){ return false; }

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = 0;
		$pk->speedY = 0;
		$pk->speedZ = 0;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = [
			2 => [4, $this->getDataProperty(2)],
			3 => [0, $this->getDataProperty(3)],
			15 => [0, 1]
		];
		$player->dataPacket($pk);
		parent::spawnTo($player);
	}

	public function setProfession($profession){
		$this->namedtag->Profession = new Int("Profession", $profession);
	}

	public function getProfession(){
		return $this->namedtag["Profession"];
	}
}
