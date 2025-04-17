<?php

declare(strict_types=1);

namespace terpz710\roulette\task;

use pocketmine\scheduler\Task;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use terpz710\roulette\Roulette;

use terpz710\libeconomy\libEconomy;

use terpz710\messages\Messages;

class RouletteTask extends Task {

    public function __construct(
        private Player $player,
        private int $amount,
        private string $choice,
        private string $result
    ) {}

    public function onRun() : void{
        $config = new Config(Roulette::getInstance()->getDataFolder() . "messages.yml");

        if (!$this->player->isOnline()) return;

        $this->player->sendMessage((string) new Messages($config, "result-message", ["{result}"], [$this->result]));

        if (strtolower($this->choice) === $this->result) {
            $winAmount = $this->amount * 2;
            libEconomy::getInstance()->addMoney($this->player, $winAmount, function(bool $success) use ($winAmount) {
                if ($success) {
                    $config = new Config(Roulette::getInstance()->getDataFolder() . "messages.yml");
                    $this->player->sendMessage((string) new Messages($config, "win-message", ["{win_amount}"], [$winAmount]));
                } else {
                    $this->player->sendMessage("§cFailed to credit your winnings!");
                }
            });
        } else {
            $config = new Config(Roulette::getInstance()->getDataFolder() . "messages.yml");
            $this->player->sendMessage((string) new Messages($config, "loss-message", ["{loss_amount}"], [$this->amount]));
        }
    }
}
