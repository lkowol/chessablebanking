<?php


namespace ChessableBanking\UserInterface\Panel\Action\Dashboard;

use ChessableBanking\Application\Branch\CurrentBranchService;
use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\Domain\Branch\BranchService;
use Symfony\Component\HttpFoundation\Response;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;

class IndexAction extends AbstractAction
{

    /**
     * All routes are configured in the simplest possible way, using annotations
     * TODO: In application further development advanced router shall be considered
     *
     * @Route("/panel", name="index")
     */
    public function index(CurrentBranchService $currentBranchService, BranchService $branchService): Response
    {
        $this->addPanel('Panel/Action/Dashboard/templates/dashboardBranches.html.twig', [
            'currentBranch' => $currentBranchService->get(),
            'branches' => $branchService->provideAll()
        ]);

        return $this->renderAction('Dashboard');
    }
}
