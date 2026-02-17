<?php

namespace Leantime\Domain\Tickets\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Tickets\Services\CustomTemplatesService;

class CustomTemplatesController extends Controller
{
    private CustomTemplatesService $templateService;

    public function init(CustomTemplatesService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function showAll($params)
    {
        $userId = $_SESSION['userdata']['id'];

        $templates = $this->templateService->getAllTemplates($userId);

        $this->tpl->assign('templates', $templates);

        return $this->tpl->display('tickets.templates');
    }

    public function create($params)
    {
        return $this->tpl->display('tickets.createTemplate');
    }

    public function save($params)
    {
        $userId = $_SESSION['userdata']['id'];

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        $result = $this->templateService->createTemplate($title, $content, $userId);

        if ($result['success']) {
            $this->tpl->setNotification($result['message'], 'success');
            return $this->tpl->redirect(BASE_URL . '/tickets/customTemplates/showAll');
        } else {
            $this->tpl->setNotification($result['message'], 'error');
            $this->tpl->assign('title', $title);
            $this->tpl->assign('content', $content);
            return $this->tpl->display('tickets.createTemplate');
        }
    }

    public function delete($params)
    {
        $userId = $_SESSION['userdata']['id'];
        $templateId = $params['id'] ?? null;

        if (!$templateId) {
            $this->tpl->setNotification('Invalid template ID', 'error');
            return $this->tpl->redirect(BASE_URL . '/tickets/customTemplates/showAll');
        }

        $result = $this->templateService->deleteTemplate($templateId, $userId);

        $this->tpl->setNotification($result['message'], $result['success'] ? 'success' : 'error');
        return $this->tpl->redirect(BASE_URL . '/tickets/customTemplates/showAll');
    }
}
