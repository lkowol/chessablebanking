<?php


namespace ChessableBanking\UserInterface\Panel\Action\Branch;

use ChessableBanking\Application\Branch\CurrentBranchService;
use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class SelectAction extends AbstractAction
{

    /**
     * @Route("/panel/branch/select/{branchId}", name="branch_select")
     */
    public function index(
        string $branchId,
        BranchRepositoryInterface $branchRepository,
        CurrentBranchService $currentBranchService
    ): Response {
        $branch = $branchRepository->find($branchId);
        if (null === $branch) {
            return $this->redirectToRouteWithMessage(
                'branch_list',
                [],
                sprintf('Branch with ID %s was not found', $branchId)
            );
        }

        $currentBranchService->select($branch);
        return $this->redirectToRouteWithMessage(
            'index',
            [],
            sprintf('Branch %s (%s) was selected', $branch->getName(), $branch->getId())
        );
    }

    /**
     * @Route("/panel/branch/deselect", name="branch_deselect")
     */
    public function deselect(CurrentBranchService $currentBranchService): Response
    {
        $currentBranchService->select(null);
        return $this->redirectToRouteWithMessage(
            'index',
            [],
            'Branch was deselected'
        );
    }
}
