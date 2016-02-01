<?php

namespace jojoe77777\arrowtp;

use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{

	public $arrowSessions = [];
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "ArrowTP by jojoe77777 has been enabled!");
        $this->saveDefaultConfig();

    }

    public function onDisable()
    {
        $this->getLogger()->info(TextFormat::GREEN . "ArrowTP by jojoe77777 has been disabled.");
    }

    public function onArrowShoot(EntityShootBowEvent $ev)
    {
        $e = $ev->getProjectile();
        $en = $ev->getEntity();
        $lvlname = $e->getLevel()->getName();
        if (in_array(strtolower($lvlname), $this->getConfig()->get("worlds"))){
		    if ($en instanceof Player and $en->hasPermission("arrowtp.tp")) {
                $this->arrowSessions[$e->getId()] = [$e->getId(), $en->getName()];
            }
        }
	}

    public function onArrowHit(ProjectileHitEvent $ev){
        $e = $ev->getEntity();
        $id = $e->getId();
        if(isset($this->arrowSessions[$id])){
            $p = $this->getServer()->getPlayer($this->arrowSessions[$id][1]);
            if($p !== null){
                $p->teleport($e, $p->getYaw(), $p->getPitch());
            }

        }
    }

    public function onArrowPickup(InventoryPickupArrowEvent $ev)
    {
        if ($this->getConfig()->get("stopCrashes")) {
            $ev->setCancelled(true);
            $ev->getArrow()->kill();
        }
    }

}
