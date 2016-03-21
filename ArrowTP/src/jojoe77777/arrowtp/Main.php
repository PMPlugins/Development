<?php

namespace jojoe77777\arrowtp;

use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "ArrowTP by jojoe77777 has been enabled!");

    }

    public function onDisable()
    {
        $this->getLogger()->info(TextFormat::GREEN . "ArrowTP by jojoe77777 has been disabled.");
    }

    public function onArrowHit(ProjectileHitEvent $ev){
        $e = $ev->getEntity();
        $s = $e->shootingEntity;
        if($e instanceof Arrow && $s instanceof Player)
                $s->teleport($e->getPosition(), $s->getYaw(), $s->getPitch());
            }
}
