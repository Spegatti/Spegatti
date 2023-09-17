<?php

declare(strict_types=1);

namespace Terpz710\Homes;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Terpz710\Homes\Command\SetHomeCommand;
use Terpz710\Homes\Command\HomeCommand;
use Terpz710\Homes\Command\HomesCommand;
use Terpz710\Homes\Command\DelHomeCommand;

class Main extends PluginBase {

    public function onEnable(): void {

        $this->createHomesDirectory();

        $this->getServer()->getCommandMap()->register("sethome", new SetHomeCommand($this->getDataFolder()));
        $this->getServer()->getCommandMap()->register("home", new HomeCommand($this->getDataFolder()));
        $this->getServer()->getCommandMap()->register("delhome", new DelHomeCommand($this->getDataFolder()));
        $this->getServer()->getCommandMap()->register("homes", new HomesCommand($this->getDataFolder()));
    }

    private function createHomesDirectory(): void {
        $dataFolder = $this->getDataFolder();
        if (!is_dir($dataFolder . "homes")) {
            mkdir($dataFolder . "homes", 0755, true);
        }
    }
}
