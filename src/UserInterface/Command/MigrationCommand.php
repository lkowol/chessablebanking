<?php

namespace ChessableBanking\UserInterface\Command;

use ChessableBanking\Application\Migration\Processor\MigrationProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command
{

    const NAME = 'chessable-banking:migration:migrate';

    private MigrationProcessor $migrationProcessor;

    public function __construct(MigrationProcessor $migrationProcessor)
    {
        parent::__construct(self::NAME);
        $this->migrationProcessor = $migrationProcessor;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Processing migrations');
        $this->migrationProcessor->process();

        return 0;
    }
}
