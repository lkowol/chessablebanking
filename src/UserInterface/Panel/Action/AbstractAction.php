<?php

namespace ChessableBanking\UserInterface\Panel\Action;

use ChessableBanking\Application\Branch\CurrentBranchService;
use ChessableBanking\UserInterface\Panel\Model\MenuPosition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAction extends AbstractController
{

    public const REDIRECT_FLASH_MESSAGE = 'redirect.flash.message';
    /** @var array<mixed> */
    private array $parameters = [];
    /** @var array<string> */
    private array $panels = [];
    private CurrentBranchService $currentBranchService;

    public function __construct(CurrentBranchService $currentBranchService)
    {
        $this->currentBranchService = $currentBranchService;
    }

    protected function renderAction(?string $title = null): Response
    {
        return $this->render('Panel/templates/index.html.twig', array_merge([
            '_menuPositions' => $this->prepareMenuPositions(),
            '_currentBranch' => $this->currentBranchService->get(),
            '_panels' => $this->panels,
            '_title' => $title
        ], $this->parameters));
    }

    protected function addPanel(string $path, array $parameters = []): void
    {
        $this->panels[] = $path;
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    protected function redirectToRouteWithMessage(
        string $route,
        array $parameters,
        ?string $message,
        int $status = 302
    ): Response {
        $this->addFlash(self::REDIRECT_FLASH_MESSAGE, $message);
        return $this->redirectToRoute($route, $parameters, $status);
    }

    /**
     * TODO: Menu positions should be automatically injected or configurable outside this class
     */
    private function prepareMenuPositions(): array
    {
        $positions = [];

        // TODO: Labels should be translatable
        $positions[] = new MenuPosition('fa-tachometer-alt', 'Dashboard', '/panel');
        $positions[] = new MenuPosition('fa-plus', 'Create branch', '/panel/branch/create');
        $positions[] = new MenuPosition('fa-list', 'Branches list', '/panel/branch/list');

        if ($this->currentBranchService->get() !== null) {
            $positions[] = new MenuPosition('fa-plus', 'Create customer', '/panel/customer/create');
            $positions[] = new MenuPosition('fa-list', 'Customers list', '/panel/customer/list');
        }

        $positions[] = new MenuPosition('fa-tasks', 'Report A', '/panel/report/branchesWithHighestBalance');
        $positions[] = new MenuPosition(
            'fa-tasks',
            'Report B',
            '/panel/report/branchesWithCustomersWithSpecifiedBalance'
        );

        return $positions;
    }
}
