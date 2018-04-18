<?php
/**
 *  ____  _            _______ _          _____
 * |  _ \| |          |__   __| |        |  __ \
 * | |_) | | __ _ _______| |  | |__   ___| |  | | _____   __
 * |  _ <| |/ _` |_  / _ \ |  | '_ \ / _ \ |  | |/ _ \ \ / /
 * | |_) | | (_| |/ /  __/ |  | | | |  __/ |__| |  __/\ V /
 * |____/|_|\__,_/___\___|_|  |_| |_|\___|_____/ \___| \_/
 *
 * Copyright (C) 2018 iiFlamiinBlaze
 *
 * iiFlamiinBlaze's plugins are licensed under MIT license!
 * Made by iiFlamiinBlaze for the PocketMine-MP Community!
 *
 * @author iiFlamiinBlaze
 * Twitter: https://twitter.com/iiFlamiinBlaze
 * GitHub: https://github.com/iiFlamiinBlaze
 * Discord: https://discord.gg/znEsFsG
 */
declare(strict_types=1);

namespace iiFlamiinBlaze\AdvancedFly;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat;

class AdvancedFly extends PluginBase implements Listener{

    const PREFIX = TextFormat::GREEN . "AdvancedFly" . TextFormat::AQUA . " > " . TextFormat::WHITE;

    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getLogger()->info(AdvancedFly::PREFIX . "AdvancedFly by iiFlamiinBlaze enabled");
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $config = $this->getConfig();
        if($config->getNested("onJoin_FlyReset") === true){
            if($player->isCreative()) return;
            $player->setAllowFlight(false);
            $player->setFlying(false);
            $player->setGamemode(0); //Could potentially be needed. We'll see how this plans out.
        if(!$player->hasPermission("fly.command")){ //The user has to have this permission for the message below to popup.
            $player->sendMessage(AdvancedFly::PREFIX . TextFormat::RED . "Flight has been disabled"); //The message that's been sent to the user. (If they have the permission fly.command.
        }
    }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{ //The command API method.
        if($command->getName() === "fly"){
            if(!$sender instanceof Player){ //The command executing as.
                $sender->sendMessage(TextFormat::RED . "Use this command in-game"); //If executes on console.
                return false;
            }
            if(!$sender->hasPermission("fly.command")){
                $sender->sendMessage(AdvancedFly::PREFIX . TextFormat::RED . "You do not have permission to use this command"); //No permission message.
                return false;
            }
            if(!$sender->isCreative()){
                if(!$sender->getAllowFlight()){
                    $sender->setAllowFlight(true);
                    $sender->setFlying(true);
                    $sender->sendMessage(AdvancedFly::PREFIX . TextFormat::GREEN . "§dFlight mode has been enabled.");
                    $sender->getPlayer()->addTitle("§dFlight Mode has been", "§aEnabled!", 40, 20, 40);
                }else{
                    $sender->setAllowFlight(false);
                    $sender->setFlying(false);
                    $sender->sendMessage(AdvancedFly::PREFIX . TextFormat::RED . "§5Flight mode has been disabled.");
                    $sender->getPlayer()->addTitle("§5Flight mode has been", "§bDisabled!", 40, 20, 40);
                }
            }else{
                $sender->sendMessage(AdvancedFly::PREFIX . TextFormat::RED . "You can only use this command in survival mode");
                return false;
            }
        }
        return true;
    }

    public function onDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        $config = $this->getConfig();
        if($config->getNested("onDamage_FlyReset") === true){
            if($event instanceof EntityDamageByEntityEvent){
                if($entity instanceof Player){
                    $damager = $event->getDamager();
                    if(!$damager instanceof Player) return;
                    if($damager->isCreative()) return;
                    if($damager->getAllowFlight() === true){
                        $damager->sendMessage(AdvancedFly::PREFIX . TextFormat::DARK_RED . "§6Flight mode disabled due to combat.");
                        $damager->setAllowFlight(false);
                        $damager->setFlying(false);
                        $damager->setGamemode(0); //This could be another method of fixing the Creative mode bug based on if you hit someone and you're in creative mode, it would take them out of creative fly (But still be in creative mode.)
                    }
                }
            }
        }
    }
}
