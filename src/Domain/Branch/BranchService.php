<?php

namespace ChessableBanking\Domain\Branch;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Exception\BranchValidationException;
use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use ChessableBanking\Domain\Branch\Validator\BranchValidator;
use ChessableBanking\Domain\Location\Entity\Location;

class BranchService
{

    private BranchRepositoryInterface $branchRepository;
    private BranchValidator $branchValidator;

    public function __construct(BranchRepositoryInterface $branchRepository, BranchValidator $branchValidator)
    {
        $this->branchRepository = $branchRepository;
        $this->branchValidator = $branchValidator;
    }

    /**
     * @param string $id
     * @param string $branchName
     * @param Location $location
     * @return Branch
     * @throws BranchValidationException
     */
    public function create(string $id, string $branchName, Location $location): Branch
    {
        $branch = new Branch($id, $branchName, $location);
        $this->branchValidator->validate($branch, true);
        return $this->branchRepository->create($branch);
    }

    /**
     * @return Branch[]
     */
    public function provideAll(): array
    {
        return $this->branchRepository->findAll();
    }
}
