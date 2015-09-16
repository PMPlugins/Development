<?php
namespace slapper\entities;

use pocketmine\item\Item;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\Network;
use pocketmine\Player;
use pocketmine\entity\Monster;
use pocketmine\entity\Entity;

class SlapperZombie extends Monster{
	const NETWORK_ID = 32;

	public function getName(){
		$name = $this->getDataProperty(2);
		return $name;
	}


	public $width = 0;
	public $length = 0;
	public $height = 0;
	public $motionY = 1.5;


	public function spawnTo(Player $player){

		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = 1.5;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = [
				Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $this->getDataProperty(2)],
				Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE, 1],
				Entity::DATA_NO_AI => [Entity::DATA_TYPE_BYTE, 1],
        ];

		$player->dataPacket($pk->setChannel(Network::CHANNEL_ENTITY_SPAWNING));
		parent::spawnTo($player);
	}




}