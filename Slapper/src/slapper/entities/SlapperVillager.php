<?php
namespace slapper\entities;
use pocketmine\nbt\tag\Int;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\Network;
use pocketmine\Player;
use pocketmine\entity\Creature;
use pocketmine\entity\NPC;
use pocketmine\entity\Ageable;
use pocketmine\entity\Entity;


class SlapperVillager extends Creature implements NPC, Ageable{
	const PROFESSION_FARMER = 0;
	const PROFESSION_LIBRARIAN = 1;
	const PROFESSION_PRIEST = 2;
	const PROFESSION_BLACKSMITH = 3;
	const PROFESSION_BUTCHER = 4;
	const PROFESSION_GENERIC = 5;

	const NETWORK_ID = 15;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0;

	public function getName(){
		return "SlapperVillager";
	}
	public function isBaby() { return false;} // Needed for 1.5

	protected function initEntity(){
		parent::initEntity();
		if(!isset($this->namedtag->Profession)){
			$this->setProfession(mt_rand(0,5));
			//$this->setProfession(self::PROFESSION_GENERIC);
		}
	}

	public function spawnTo(Player $player){

		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
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

	/**
	 * Sets the villager profession
	 *
	 * @param $profession
	 */
	public function setProfession($profession){
		$this->namedtag->Profession = new Int("Profession", $profession);
	}

	public function getProfession(){
		return $this->namedtag["Profession"];
	}
}
