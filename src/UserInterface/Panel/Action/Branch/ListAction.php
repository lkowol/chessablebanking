<?php


namespace ChessableBanking\UserInterface\Panel\Action\Branch;

use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\Domain\Branch\BranchService;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractAction
{

    /**
     * @Route("/panel/branch/list", name="branch_list")
     */
    public function index(BranchService $branchService): Response
    {
        $this->addPanel('Panel/Action/Branch/templates/list.html.twig', [
            'branches' => $branchService->provideAll()
        ]);

        return $this->renderAction('Branch list');
    }
}
