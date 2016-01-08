<?php

namespace anyslab;

use pocketmine\block\Air;
use pocketmine\block\Fence;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\tag\String;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class main extends PluginBase implements Listener
{

    public $slabSessions = [];
    public $prefix = (TextFormat::GREEN . "[" . TextFormat::YELLOW . "AnySlab" . TextFormat::GREEN . "] ");
    public $noperm = (TextFormat::GREEN . "[" . TextFormat::YELLOW . "AnySlab" . TextFormat::GREEN . "] You don't have permission.");

    public function onEnable()
    {
        Entity::registerEntity(CustomSlab::class, true);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        switch (strtolower($command->getName())) {
            case "anyslab":
                if ($sender instanceof Player) {
                    $this->slabSessions[$sender->getName()] = true;
                    $sender->sendMessage($this->prefix . "Tap any block to transform it into a slab!");
                } else {
                    $sender->sendMessage($this->prefix . "This command only works in game.");
                }
        }
        return true;
    }

    private function makeNBT($x, $y, $z, $id)
    {
        $nbt = new Compound;
        $nbt->Pos = new Enum("Pos", [
            new Double("", $x + .5),
            new Double("", $y),
            new Double("", $z + .5)
        ]);
        $nbt->Rotation = new Enum("Rotation", [
            new Float("", 50),
            new Float("", 30)
        ]);
        $nbt->AnySlabVersion = new String("AnySlabVersion", "1.0.0");
        $nbt->BlockID = new Int("BlockID", $id);

        return $nbt;
    }

    public function onTouchBlock(PlayerInteractEvent $ev){
        $player = $ev->getPlayer();
        if(array_key_exists($player->getName(), $this->slabSessions)){
            $block = $ev->getBlock();
            $level = $block->getLevel();
            $level->setBlock($block, new Air());
            $level->setBlock(new Vector3($block->x, $block->y - 1, $block->z), new Fence());
            $slab = Entity::createEntity("CustomSlab", $level->getChunk($block->x >> 4, $block->z >> 4), $this->makeNBT($block->x, $block->y, $block->z, $block->getId()));
            $slab->spawnToAll();
            unset($this->slabSessions[$player->getName()]);
            $player->sendMessage($this->prefix . "Transformed block into a slab.");
        }
    }


}
