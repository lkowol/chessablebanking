<?php

namespace ChessableBanking\Tests\Domain\Branch;

use ChessableBanking\Domain\Branch\BranchService;
use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use ChessableBanking\Domain\Branch\Validator\BranchValidator;
use ChessableBanking\Domain\Location\Entity\Location;
use PHPUnit\Framework\TestCase;

class BranchServiceTest extends TestCase
{

    public function testService(): void
    {
        $location = $this->createMock(Location::class);
        $branch = $this->createMock(Branch::class);

        $branchRepository = $this->createMock(BranchRepositoryInterface::class);
        $branchRepository->expects($this->once())->method('findAll')->willReturn([$branch]);
        $branchRepository->expects($this->once())->method('create')
            ->with($this->callback(function (Branch $branch) {
                return $branch->getId() === 'theId' && $branch->getName() === 'theBranchName';
            }));
        $branchValidator = $this->createMock(BranchValidator::class);
        $branchValidator->expects($this->once())->method('validate')
            ->with($this->callback(function (Branch $branch) {
                return $branch->getId() === 'theId' && $branch->getName() === 'theBranchName';
            }), true);

        $service = new BranchService($branchRepository, $branchValidator);
        $service->create('theId', 'theBranchName', $location);

        $this->assertEquals([$branch], $service->provideAll());
    }
}
