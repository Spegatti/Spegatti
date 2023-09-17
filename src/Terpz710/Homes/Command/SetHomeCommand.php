<?php

declare(strict_types=1);

namespace Terpz710\Homes\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use Terpz710\Homes\Main;

class SetHomeCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("sethome", "Set your home location");
        $this->setPermission("homes.sethome");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /sethome <home>");
                return false;
            }

            $homeName = $args[0];
            $homeLocation = [
                'x' => $sender->getX(),
                'y' => $sender->getY(),
                'z' => $sender->getZ(),
                'world' => $sender->getWorld()->getFolderName(), // Updated method to get the world's name.
            ];

            $this->saveHomeData($sender, $homeName, $homeLocation);

            $sender->sendMessage("Your home location '$homeName' has been set!");
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function saveHomeData(Player $player, string $homeName, array $homeLocation): void {
        $playerName = strtolower($player->getName());
        $config = new Config($this->dataFolder . "homes/" . $playerName . ".yml", Config::YAML);

        $playerHomes = $config->get("homes", []);
        $playerHomes[$homeName] = $homeLocation;

        $config->set("homes", $playerHomes);
        $config->save();
    }
}
