<?php

namespace ChessableBanking\Application\Migration;

use ChessableBanking\Application\Migration\Exception\MigrationException;

interface MigrationInterface
{

    /**
     * Simple migration method, for storage tables creation, initial data, etc.
     * TODO: There should be methods for installation and uninstallation of migration
     * @throws MigrationException
     */
    public function install(): void;

    public function isInstalled(): bool;
}
