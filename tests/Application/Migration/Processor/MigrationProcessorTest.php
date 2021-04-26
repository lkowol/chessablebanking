<?php

namespace ChessableBanking\Tests\Application\Migration\Processor;

use ChessableBanking\Application\Migration\Exception\MigrationException;
use ChessableBanking\Application\Migration\MigrationInterface;
use ChessableBanking\Application\Migration\Processor\MigrationProcessor;
use ChessableBanking\Application\Migration\Registry\MigrationRegistry;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MigrationProcessorTest extends TestCase
{

    public function testProcessor(): void
    {
        $migration1 = $this->createMock(MigrationInterface::class);
        $migration1->expects($this->once())->method('install');
        $migration2 = $this->createMock(MigrationInterface::class);
        $migration2->expects($this->once())->method('install');

        $registry = $this->createMock(MigrationRegistry::class);
        $registry->expects($this->once())->method('getNotInstalled')->willReturn([$migration1, $migration2]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $processor = new MigrationProcessor($registry, $logger);
        $processor->process();
    }

    public function testProcessorWithError(): void
    {
        $migration1 = $this->createMock(MigrationInterface::class);
        $migration1->expects($this->once())->method('install')->willThrowException(
            new MigrationException('theMigrationException')
        );

        $registry = $this->createMock(MigrationRegistry::class);
        $registry->expects($this->once())->method('getNotInstalled')->willReturn([$migration1]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')->with('theMigrationException');

        $processor = new MigrationProcessor($registry, $logger);
        $processor->process();
    }
}
