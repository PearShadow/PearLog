<?php

namespace Leantime\Domain\Tickets\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Tickets\Services\CustomTemplatesService;
use Leantime\Core\Controller\Frontcontroller;

class CustomTemplates extends Controller
{
    private CustomTemplatesService $templateService;

    public function init(CustomTemplatesService $templateService)
    {
        $this->templateService = $templateService;
    }

    private function getUserId(): ?int
    {
        return session('userdata.id');
    }

    private function jsonAuth(): void
    {
        $userId = $this->getUserId();
        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
    }

    public function get($params)
    {
        $this->jsonAuth();
        $userId = $this->getUserId();
        $templates = $this->templateService->getAllTemplates($userId);

        $formatted = array_map(function($tpl) {
            return [
                'title' => $tpl['title'],
                'content' => $tpl['content'],
            ];
        }, $templates);

        header('Content-Type: application/json');
        echo json_encode($formatted);
        exit;
    }

    public function save($params)
    {
        $this->jsonAuth();
        $userId = $this->getUserId();

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        $result = $this->templateService->createTemplate($title, $content, $userId);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function showAll($params)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return Frontcontroller::redirect(BASE_URL . '/login');
        }

        $templates = $this->templateService->getAllTemplates($userId);
        $this->tpl->assign('templates', $templates);
        return $this->tpl->display('tickets.customTemplates');
    }

    public function create($params)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return Frontcontroller::redirect(BASE_URL . '/login');
        }

        return $this->tpl->display('tickets.createTemplate');
    }

    public function delete($params)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $templateId = $params['id'] ?? null;
        if (!$templateId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid template ID']);
            exit;
        }

        $result = $this->templateService->deleteTemplate($templateId, $userId);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}