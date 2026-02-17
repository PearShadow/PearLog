<?php

namespace Leantime\Domain\Tickets\Services;

use Leantime\Domain\Tickets\Repositories\CustomTemplatesRepository;

class CustomTemplatesService
{
    private CustomTemplatesRepository $customTemplatesRepo;

    public function __construct(CustomTemplatesRepository $customTemplatesRepo)
    {
        $this->customTemplatesRepo = $customTemplatesRepo;
    }

    public function getAllTemplates($userId)
    {
        return $this->customTemplatesRepo->getAll($userId);
    }

    public function createTemplate($title, $content, $userId)
    {
        if (empty($title) || empty($content)) {
            return ['success' => false, 'message' => 'Title and content required'];
        }

        $result = $this->customTemplatesRepo->create($title, $content, $userId);

        return [
            'success' => $result,
            'message' => $result ? 'Template saved' : 'Failed to save'
        ];
    }

    public function deleteTemplate($id, $userId)
    {
        $result = $this->customTemplatesRepo->delete($id, $userId);

        return [
            'success' => $result,
            'message' => $result ? 'Template deleted' : 'Not found or not authorized'
        ];
    }

    public function getTemplate($id, $userId)
    {
        return $this->customTemplatesRepo->getOne($id, $userId);
    }
}
