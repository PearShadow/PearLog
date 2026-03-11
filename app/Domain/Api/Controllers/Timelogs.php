<?php

namespace Leantime\Domain\Api\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Repositories\AccessTokenRepository;
use Leantime\Domain\Timesheets\Repositories\Timesheets as TimesheetsRepository;
use Leantime\Domain\Users\Repositories\Users as UsersRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Timelogs extends Controller
{
    private TimesheetsRepository $timesheetsRepo;
    private UsersRepository $usersRepo;
    private AccessTokenRepository $tokenRepo;

    public function init(
        TimesheetsRepository $timesheetsRepo,
        UsersRepository $usersRepo,
        AccessTokenRepository $tokenRepo,
    ): void {
        $this->timesheetsRepo = $timesheetsRepo;
        $this->usersRepo = $usersRepo;
        $this->tokenRepo = $tokenRepo;
    }

    public function get(array $params): Response
    {
        $token = $this->incomingRequest->getBearerToken();
        if (empty($token)) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $tokenRecord = $this->tokenRepo->findToken($token);
        if (!$tokenRecord) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $email = trim($params['email'] ?? '');
        $date  = trim($params['date'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Missing or invalid email parameter'], 400);
        }

        if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return new JsonResponse(['error' => 'Missing or invalid date parameter (expected YYYY-MM-DD)'], 400);
        }

        $user = $this->usersRepo->getUserByEmail($email);
        if (!$user) {
            return new JsonResponse(['error' => 'No user found for the given email'], 404);
        }

        $logs = $this->timesheetsRepo->getTimesheetsByUserAndDate((int) $user['id'], $date);

        $grouped = [];
        foreach ($logs as $log) {
            $ticketName = '#'.$log['ticketId'].' - '.($log['headline'] ?? 'Unknown ticket');
            $grouped[$ticketName][] = [
                'duration_minutes' => (int) round((float) $log['hours'] * 60),
                'description'      => $log['description'] ?? '',
            ];
        }

        $data = array_map(
            fn($ticketName, $entries) => [
                'ticket_name' => $ticketName,
                'logs'        => $entries,
            ],
            array_keys($grouped),
            array_values($grouped)
        );

        $this->tokenRepo->updateLastUsedAt($tokenRecord['id']);

        return new JsonResponse([
            'email' => $email,
            'date'  => $date,
            'data'  => array_values($data),
        ]);
    }
}