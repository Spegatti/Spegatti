<?php

declare(strict_types=1);

namespace Terpz710\Homes\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use Terpz710\Homes\Main;

class HomesCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("homes", "List your available home locations");
        $this->setPermission("homes.homes");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            $homes = $this->loadHomeData($sender);

            if (empty($homes)) {
                $sender->sendMessage("You have not set any home locations.");
            } else {
                $homesList = implode(", ", array_keys($homes));
                $sender->sendMessage("Homes: " . $homesList);
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function loadHomeData(Player $player): array {
        $playerName = strtolower($player->getName());
        $config = new Config($this->dataFolder . "homes/" . $playerName . ".yml", Config::YAML);

        return $config->get("homes", []);
    }
}
