<?php

namespace ChessableBanking\Tests\Application\Branch;

use ChessableBanking\Application\Branch\CurrentBranchService;
use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrentBranchServiceTest extends TestCase
{

    public function testCurrentBranchService(): void
    {
        $branch = $this->createMock(Branch::class);
        $branch->expects($this->once())->method('getId')->willReturn('theId');

        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('set')->with('chessableBanking.currentBranch', 'theId');
        $session->expects($this->once())->method('get')->with('chessableBanking.currentBranch')->willReturn('theId');
        $branchRepository = $this->createMock(BranchRepositoryInterface::class);
        $branchRepository->expects($this->once())->method('find')->with('theId')->willReturn($branch);

        $service = new CurrentBranchService($session, $branchRepository);
        $service->select($branch);

        $resultBranch = $service->get();
        $this->assertEquals($branch, $resultBranch);
    }
}
