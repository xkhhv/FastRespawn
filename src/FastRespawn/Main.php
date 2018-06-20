<?php

namespace FastRespawn;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as Color;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Position;
use pocketmine\utils\Config;
use pocketmine\item\Item;
use pocketmine\inventory\InventoryBase;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerMoveEvent;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {

    private $players = [];

	public function onEnable()
	{
		  $this->getLogger()->info(Color::AQUA . "FastRespawn Enabled By @Khavmc");
    $this->getServer()->getPluginManager()->registerEvents($this ,$this);
    $this->saveDefaultConfig();
    $this->reloadConfig();
        }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $playerN = $player->getName();
        if ($event->getPlayer()->getY() < -5) {
            $event->getPlayer()->teleport($event->getPlayer()->getLevel()->getSafeSpawn());
            $player->setHealth(20);
            $player->setFood(20);
            $player->removeAllEffects();
            if($this->getConfig()->get("clear_inventory")) {
                $player->getInventory()->clearAll();
                return true;
            }
            foreach ($this->getServer()->getOnlinePlayers() as $players) {
                $search = array(
                    '{player}'
                );
                $replace = array(
                    $playerN
                );
                $void_msg = str_replace($search, $replace, $this->getConfig()->getAll(){"void_message"});
                $players->sendMessage(FMT::colorMessage($void_msg));
            }
        }
    }

// Thx For CraftYourBukkit To Give Me The Code!

        public
        function onDamage(EntityDamageEvent $event)
        {
            if ($event instanceof EntityDamageByEntityEvent) {
                if ($event->getEntity() instanceof Player && $event->getDamager() instanceof Player) {
                    $player = $event->getEntity();
                    $killer = $event->getDamager();
                    $playerN = $player->getName();
                    $killerN = $killer->getName();
                    if ($event->getBaseDamage() >= $event->getEntity()->getHealth()) {
                        $event->setCancelled();
                        $player->setHealth(20);
                        $player->setFood(20);
                        $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                        $player->removeAllEffects();
                        if ($this->getConfig()->get("clear_inventory")) {
                            $player->getInventory()->clearAll();
                            return true;
                        }
                        if ($this->getConfig()->get("heal_for_killer")){
                            $killer->setHealth(20);
                            $killer->setFood(20);
                        }
                        if ($this->getConfig()->get("money_when_kill")){
                            $economy = EconomyAPI::getInstance();
                                $economy->addMoney($killerN, $this->getConfig()->get("money_amount"));
                            $search = array(
                                '{player}',
                                '{killer}'
                            );
                            $replace = array(
                                $playerN,
                                $killerN
                            );
                            $money_msg = str_replace($search, $replace, $this->getConfig()->getAll(){"money_message"});
                            $killer->sendMessage(FMT::colorMessage($money_msg));
                              $this->getServer()->getPluginManager()->callEvent(new PlayerDeathEvent($player, [], ""));
			}
                        foreach ($this->getServer()->getOnlinePlayers() as $players) {
                            $search = array(
                                '{killer}',
                                '{player}'
                            );
                            $replace = array(
                                $killerN,
                                $playerN
                            );
                            $killer_msg = str_replace($search, $replace, $this->getConfig()->getAll(){"kill_message"});
                            $players->sendMessage(FMT::colorMessage($killer_msg));

                        }
                    }
                }
            }
            if ($event->getEntity() instanceof Player && $event->getEntity()->getY() < 0) {
                $player = $event->getEntity();
                $playerN = $player->getName();
                $event->setCancelled();
                $player->setHealth(20);
                $player->setFood(20);
                $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                $player->removeAllEffects();
                if ($this->getConfig()->get("clear_inventory")) {
                    $player->getInventory()->clearAll();
                    return true;
                }
                foreach ($this->getServer()->getOnlinePlayers() as $players) {
                    $search = array(
                        '{player}'
                    );
                    $replace = array(
                        $playerN
                    );
                    $void_msg = str_replace($search, $replace, $this->getConfig()->getAll(){"void_message"});
                    $players->sendMessage(FMT::colorMessage($void_msg));
                }
            }
            }
 }
