<?php
namespace slapper\entities;

use pocketmine\item\Item;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\Network;
use pocketmine\Player;
use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\String;

class SlapperCow extends Animal{
	const NETWORK_ID = 11;

	public $width = 1;
	public $length = 1;
	public $height = 0;


	public function getName(){
		return $this->getDataProperty(2);
	}

	public function setAge($age){
		$this->namedtag->Age($age);
	}

	public function addCommand($command){
		$this->namedtag->Commands[$command] = new String($command, $command);
		$this->saveNBT();
	}

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
				Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $this->getDataProperty(2)],
				Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE, 1],
				Entity::DATA_NO_AI => [Entity::DATA_TYPE_BYTE, 1]
        ];

		$player->dataPacket($pk->setChannel(Network::CHANNEL_ENTITY_SPAWNING));
		parent::spawnTo($player);
	}




}
