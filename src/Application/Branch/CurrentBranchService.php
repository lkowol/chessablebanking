<?php

namespace ChessableBanking\Application\Branch;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrentBranchService
{

    private const CURRENT_BRANCH_KEY = 'chessableBanking.currentBranch';
    private SessionInterface $session;
    private BranchRepositoryInterface $branchRepository;

    public function __construct(SessionInterface $session, BranchRepositoryInterface $branchRepository)
    {
        $this->session = $session;
        $this->branchRepository = $branchRepository;
    }

    public function get(): ?Branch
    {
        $branchId = $this->session->get(self::CURRENT_BRANCH_KEY);

        if (null === $branchId) {
            return null;
        }

        return $this->branchRepository->find($branchId);
    }

    public function select(?Branch $branch): void
    {
        if (null !== $branch) {
            $this->session->set(self::CURRENT_BRANCH_KEY, $branch->getId());
        } else {
            $this->session->set(self::CURRENT_BRANCH_KEY, null);
        }
    }
}
