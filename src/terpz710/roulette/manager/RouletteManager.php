<?php

declare(strict_types=1);

namespace terpz710\roulette\manager;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\roulette\Roulette;

use terpz710\roulette\task\RouletteTask;

final class RouletteManager {

    public static function spin(Player $player, int $amount, string $choice) : void{
        $result = mt_rand(0, 1) === 0 ? "red" : "black";

        Roulette::getInstance()->getScheduler()->scheduleDelayedTask(new RouletteTask($player, $amount, $choice, $result), 20 * 5);
    }
}