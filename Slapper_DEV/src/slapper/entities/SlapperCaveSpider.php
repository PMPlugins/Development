<?php
namespace slapper\entities;

use pocketmine\entity\Entity;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class SlapperCaveSpider extends Entity
{

    const NETWORK_ID = 40;

    public function getName()
    {
        return $this->getDataProperty(2);
    }

    public function spawnTo(Player $player)
    {

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


}
