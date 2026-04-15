<?php

namespace Quellwerke\QuellwerkeBugtrackBundle\Installer;

class Installer extends \Pimcore\Extension\Bundle\Installer\AbstractInstaller
{
    public function canBeInstalled(): bool
    {
        return !$this->isInstalled();
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function install(): void
    {
        echo "Installer->install() OK\n";
    }
}