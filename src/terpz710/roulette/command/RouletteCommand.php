<?php

declare(strict_types=1);

namespace terpz710\roulette\command;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use terpz710\roulette\Roulette;
use terpz710\roulette\economy\EconomyManager;
use terpz710\roulette\manager\RouletteManager;

use terpz710\messages\Messages;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;

class RouletteCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("roulette.cmd");

        $this->registerArgument(0, new IntegerArgument("amount"));
        $this->registerArgument(1, new RawStringArgument("color"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $config = new Config(Roulette::getInstance()->getDataFolder() . "messages.yml");

        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return;
        }

        $amount = (int)($args["amount"]);
        $color = strtolower($args["color"]);

        if ($amount <= 0) {
            $sender->sendMessage((string) new Messages($config, "invalid-amount"));
            return;
        }

        if (!in_array($color, ["red", "black"])) {
            $sender->sendMessage((string) new Messages($config, "invalid-color"));
            return;
        }

        EconomyManager::getInstance()->getMoney($sender, function(float $balance) use ($sender, $amount, $color) {
            if ($balance < $amount) {
                $config = new Config(Roulette::getInstance()->getDataFolder() . "messages.yml");
                $sender->sendMessage((string) new Messages($config, "not-enough-money", ["{balance}"], [$balance]));
                return;
            }

            EconomyManager::getInstance()->reduceMoney($sender, $amount, function(bool $success) use ($sender, $amount, $color) {
                if ($success) {
                    $config = new Config(Roulette::getInstance()->getDataFolder() . "messages.yml");
                    $sender->sendMessage((string) new Messages($config, "spin-wheel", ["{amount}", "{color}"], [$amount, $color]));
                    RouletteManager::spin($sender, $amount, $color);
                } else {
                    $sender->sendMessage("§cFailed to deduct money from your account!");
                }
            });
        });
    }
}
