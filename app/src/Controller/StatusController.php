<?php

namespace App\Controller;

// Импортируем необходимые Symfony-классы для контроллера, ответов и маршрутизации
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// Контроллер для проверки статуса и пинга API
class StatusController extends AbstractController
{
    // ===============================
    // Проверить статус сервера
    // GET /api
    // ===============================
    #[Route('/api', name: 'api_status', methods: ['GET'])]
    public function status(Request $request): JsonResponse
    {
        // Возвращаем информацию о статусе сервера, хосте и протоколе в JSON-формате
        return $this->json([
            'status' => 'server is running',            // Сообщение о том, что сервер запущен
            'host' => $request->getHost(),              // Текущий хост (например, localhost:8080)
            'protocol' => $request->getScheme(),        // Протокол (http или https)
        ]);
    }

    // ===============================
    // Проверить "пинг" сервера
    // GET /api/ping
    // ===============================
    #[Route('/api/ping', name: 'api_ping', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        // Возвращаем простой ответ "pong" для проверки живости сервера
        return $this->json([
            'status' => 'pong',
        ]);
    }
}
