<?php

declare(strict_types=1);

namespace terpz710\roulette;

use pocketmine\plugin\PluginBase;

use terpz710\roulette\command\RouletteCommand;

use CortexPE\Commando\PacketHooker;

class Roulette extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->register("Roulette", new RouletteCommand($this, "roulette", "Take a chance to double your money"));
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}