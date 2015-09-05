<?php

namespace Legoboy\CouponCode;

use pocketmine\plugin\PluginBase; //Whatever you use in the plugin, you need to have it here.
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use onebone\economyapi\EconomyAPI;
use _64FF00\PurePerms;

class Main extends PluginBase{ //Class name needs to be same as the file name.

        public function onEnable(){
				@mkdir($this->getDataFolder())
		        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array
				(
					"no_permission_error" => "You do not have permission to execute this command.",
					"vip_group_name" => "vip",
					"amount_of_money" => "100",
				)
				);  
			    $this->codes = new Config($this->getDataFolder() . "coupons.txt", Config::ENUM);
				$this->config->save();
			    $this->codes->save();
				$this->economy = EconomyAPI::getInstance();
		        if (!$this->economy) {
			        $this->getLogger()->info(TextFormat::RED . "Unable to find EconomyAPI.");
			        return true;
			    }
				$this->couponused = new Config($this->getDataFolder() . "couponused.txt", Config::ENUM);
		}

        public function onDisable(){
            $this->config->save();
			$this->codes->save();
        }
		
        public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
            switch($cmd->getName()){
                case "coupon":
                    if($sender->hasPermission("coupon.use")){
					    if($sender instanceof Player){
						    $name = $sender->getName();
					        if(isset($args[0])){
						        $coupon = file_get_contents($this->getDataFolder() . "coupons.txt");
							    $coupons = explode(",", $coupon);
							        if(in_array($args[0], $coupons)){
									    if(!($this->couponused->exists($name))){
									        $this->couponused->set($name);
											$this->couponused->save();
								            $sender->sendMessage("Correct and valid coupon code.");
					                        //$ids = [259, 260, 261, 262, 264, 265, 268, 271, 272, 275, 280, 282, 298, 299, 300, 301, 302, 303, 304, 305, 306, 308, 309, 314, 315, 316, 317, 319, 320, 354, 357, 363, 364, 365, 366]; //Edit the items here...
          		                            $rand = mt_rand(1, 10000);
									        if($rand === 2000){
									            $player = $sender;
										        $groupname = $this->config->get("vip_group_name");
										        $this->playerSetGroup($player, $groupname);
										        $sender->sendMessage(TextFormat::YELLOW . "You got VIP!!! The ratio is 1:10000!");
											    $this->getLogger()->warning(TextFormat::YELLOW . $name . " hit the jackpot! He got free VIP!");
											    $this->getServer()->broadcastMessage(TextFormat::YELLOW . $name . " hit the jackpot! He got free VIP!");
									        }else{
									            $pmoney = $this->economy->addmoney($name, $this->config->get("amount_of_money"));
										        $sender->sendMessage(TextFormat::GREEN . "You got 100 coins!");
									        }
									        //$ids[array_rand($ids)];
            		                        //for($i = 0; $i < 6; $i++){
             		                            //$inv->addItem(Item::get($ids[mt_rand(0, count($ids) - 1)]));
            		                        //}
										}else{
										    $sender->sendMessage(TextFormat::RED . "You already exchanged a coupon!");
											return true;
										}
								    }else{
								        $sender->sendMessage(TextFormat::RED . "Invalid coupon code...");
										return true;
								    }	
                                return true;
                            }else{
					            $sender->sendMessage(TextFormat::RED . "Please enter a coupon code.");
								return false;
					        }
					    }else{
						    $sender->sendMessage(TextFormat::RED . "Run this command in-game please!");
							return true;
						}
                    }else{
                        $sender->sendMessage(TextFormat::RED . $this->config->get("no_permission_error"));
                        return true;
                    }
					if($args[0] === "reset"){
					    if($sender->hasPermission("coupon.reset")){
							unlink($this->getDataFolder() . "couponused.txt");
							$sender->sendMessage(TextFormat::RED . "File deleted!");
							return true;
						}else{
							$sender->sendMessage(TextFormat::RED . $this->config->get("no_permission_error"));
							return true;
                        }
					}				
            }
	    }
	    public function playerSetGroup(IPlayer $player, $groupname){
	        $this->pperms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
		    $groups = $this->pperms->getGroups();
		    $group = $groups[$groupname];
		    $levelname = null;
		    $this->pperms->getUser($player)->setGroup($group, $levelname);
	    }
}