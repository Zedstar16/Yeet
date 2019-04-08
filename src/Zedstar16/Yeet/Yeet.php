<?php

declare(strict_types=1);

namespace Zedstar16\Yeet;


use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\Stick;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\nbt\tag\StringTag;
use pocketmine\math\Vector3;
class Yeet extends PluginBase implements Listener {

	public function onEnable() : void{
		$this->getLogger()->info("Yeet");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "yeet":
				if($sender->hasPermission("yeet.cmd")){
				    if(count($args) == 4){
				        if($sender->getServer()->getPlayer($args[0]) !== null){
				            if(is_numeric($args[1]) && is_numeric($args[2]) && is_numeric($args[3])){
				                $p = $sender->getServer()->getPlayer($args[0]);
                                $p->setMotion(new Vector3($args[1], $args[2], $args[3]));
                                $p->sendTip("§l§bYeet");
                            }else  $sender->sendMessage("§cX Y Z values must be §bnumeric");
				        }else $sender->sendMessage("§c$args[0] is not §aonline");
                    }elseif(count($args) == 3){
				        if(!$sender instanceof Player){
				            $sender->sendMessage("§cYou can only yeet yourself ingame, use §b/yeet (player) (x) (y) (z)");
                        }
                        if(is_numeric($args[0]) && is_numeric($args[1]) && is_numeric($args[2])){
                            $p = $sender->getServer()->getPlayer($sender->getName());
                            $p->setMotion(new Vector3($args[0], $args[1], $args[2]));
                        } else $sender->sendMessage("§cX Y Z values must be §bnumeric");
				    }else $sender->sendMessage("§6Usage:\n§aTo Yeet yourself: /yeet (x) (y) (z)\n§bTo yeet others: /yeet (player) (x) (y) (z)\n§fX, Y and Z is the force they will be launched at that direction");
                }
                break;
            case "yeetstick":
                if($sender->hasPermission("yeet.stick")) {
                    if (!$sender instanceof Player) {
                        $sender->sendMessage("§cYou can only use the YeetStick §aIn Game");
                        return false;
                    }
                    if (count($args) == 3 && is_numeric($args[0]) && is_numeric($args[1]) && is_numeric($args[2])) {
                        $p = $sender->getServer()->getPlayer($sender->getName());
                        $yeetstick = Item::get(280);
                        $yeetstick->setCustomName("§b§l§kII§r §l§aYeet Stick§b §kII");
                        $yeetstick->setLore(["§6The legendary §dYeeter"]);
                        $nbt = $yeetstick->getNamedTag();
                        $nbt->setIntArray("yeet", $args);
                        $yeetstick->setCompoundTag($nbt);
                        $p->getInventory()->setItemInHand($yeetstick);
                    } else $sender->sendMessage("§aUsage: §b/yeetstick (x) (y) (z)");
                }
				return true;
			default:
				return false;
		}
	return true;
	}

	public function getYeetPower(Item $item) : Vector3{
	    $nbt = $item->getNamedTag()->getIntArray("yeet");
	    $x = $nbt[0];
	    $y = $nbt[1];
	    $z = $nbt[2];
	    return new Vector3($x, $y, $z);
    }

    /**
     * @param EntityDamageByEntityEvent $event
     * @ignoreCancelled false
     */
   public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
	    $damager = $event->getDamager();
	    if($damager instanceof Player && $event->getEntity() instanceof Living){
	        $p = $damager->getPlayer();
	        $item = $p->getInventory()->getItemInHand();
	        if($item->getNamedTag()->hasTag("yeet", IntArrayTag::class)){
	            if($p->hasPermission("yeetstick.use")){
	                $entity = $event->getEntity();
	                $entity->setMotion($this->getYeetPower($item));
	                if($entity instanceof Player){
	                   $entity->getPlayer()->sendTip("§l§bYeet");
                    }
                }else {
                    $p->getInventory()->remove($item);
                    $p->sendMessage("§cYou do not have permission to use the Yeeter");
                }
            }

        }
   }

}
