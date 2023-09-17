<?php

declare(strict_types=1);

namespace Terpz710\Homes\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use Terpz710\Homes\Main;

class DelHomeCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("delhome", "Delete your home location");
        $this->setPermission("homes.delhome");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /delhome <home>");
                return false;
            }

            $homeName = $args[0];
            $homeData = $this->loadHomeData($sender);

            if (isset($homeData[$homeName])) {
                unset($homeData[$homeName]);
                $this->saveHomeData($sender, $homeData);

                $sender->sendMessage("Your home location '$homeName' has been deleted.");
            } else {
                $sender->sendMessage("You do not have a home location named '$homeName'.");
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

    private function saveHomeData(Player $player, array $homeData): void {
        $playerName = strtolower($player->getName());
        $config = new Config($this->dataFolder . "homes/" . $playerName . ".yml", Config::YAML);

        $config->set("homes", $homeData);
        $config->save();
    }
}
