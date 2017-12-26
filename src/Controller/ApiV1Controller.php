<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiV1Controller
{
    public function catchAllAction(): JsonResponse
    {
        return new JsonResponse([
            'errors' => [
                'message' => 'Resource not found',
            ],
        ], 404);
    }
}
