<?php

namespace Leantime\Domain\Tickets\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Domain\Timesheets\Services\Timesheets as TimesheetService;
use Symfony\Component\HttpFoundation\Response;

class KanbanPage extends Controller
{
    private ProjectService $projectService;

    private TicketService $ticketService;

    private TimesheetService $timesheetService;

    public function init(
        ProjectService $projectService,
        TicketService $ticketService,
        TimesheetService $timesheetService
    ): void {
        $this->projectService = $projectService;
        $this->ticketService = $ticketService;
        $this->timesheetService = $timesheetService;
    }

    public function load(array $params): Response
    {
        $status = (int) ($params['status'] ?? -999);
        $limit = (int) ($params['limit'] ?? $this->ticketService->getKanbanPageSize());

        if (! array_key_exists($status, $this->ticketService->getKanbanColumns())) {
            return new Response('', 400);
        }

        $limit = min(max(1, $limit), 100);
        $tickets = $this->ticketService->getKanbanTicketsForStatus($params, $status, $limit);

        $this->tpl->assign('allKanbanColumns', $this->ticketService->getKanbanColumns());
        $this->tpl->assign('onTheClock', $this->timesheetService->isClocked(session('userdata.id')));
        $this->tpl->assign('todoTypeIcons', $this->ticketService->getTypeIcons());
        $this->tpl->assign('efforts', $this->ticketService->getEffortLabels());
        $this->tpl->assign('priorities', $this->ticketService->getPriorityLabels());
        $this->tpl->assign('users', $this->projectService->getUsersAssignedToProject(session('currentProject')));
        $this->tpl->assign('milestones', $this->ticketService->getAllMilestones([
            'sprint' => '',
            'type' => 'milestone',
            'currentProject' => session('currentProject'),
        ]));

        $tpl = $this->tpl;
        $key = $status;
        $todoTypeIcons = $this->ticketService->getTypeIcons();
        $efforts = $this->ticketService->getEffortLabels();
        $priorities = $this->ticketService->getPriorityLabels();

        ob_start();
        foreach ($tickets as $row) {
            include __DIR__.'/../Templates/partials/kanbanTicketCard.tpl.php';
        }
        $html = ob_get_clean();

        return new Response($html, 200);
    }
}
