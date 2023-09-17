<?php

declare(strict_types=1);

namespace Terpz710\Homes\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\world\WorldManager;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use Terpz710\Homes\Main;

class HomeCommand extends Command {

    private $dataFolder;

    public function __construct(string $dataFolder) {
        parent::__construct("home", "Teleport to your home location");
        $this->setPermission("homes.home");
        $this->dataFolder = $dataFolder;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /home <home>");
                return false;
            }

            $homeName = $args[0];
            $homeLocation = $this->loadHomeData($sender, $homeName);

            if ($homeLocation !== null) {
                $x = $homeLocation['x'];
                $y = $homeLocation['y'];
                $z = $homeLocation['z'];
                $worldName = $homeLocation['world'];

                $world = $sender->getServer()->getWorld()->getFolderName($worldName);

                if ($world !== null) {
                    $homeVector = new Vector3($x, $y, $z);
                    $sender->teleport($homeVector, $world);
                    $sender->sendMessage("Teleported to your home location '$homeName'.");
                } else {
                    $sender->sendMessage("The world of your home location no longer exists.");
                }
            } else {
                $sender->sendMessage("You do not have a home location named '$homeName'.");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function loadHomeData(Player $player, string $homeName): ?array {
        $playerName = strtolower($player->getName());
        $config = new Config($this->dataFolder . "homes/" . $playerName . ".yml", Config::YAML);

        $playerHomes = $config->get("homes", []);
        return $playerHomes[$homeName] ?? null;
    }
}
