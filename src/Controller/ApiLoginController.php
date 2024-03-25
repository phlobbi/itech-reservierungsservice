<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user, SessionService $sessionService): JsonResponse
    {
        try {
            $token = $sessionService->getSessionToken($user);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }

    #[Route('/api/logout', name: 'app_api_logout', methods: ['POST'])]
    public function logout(Request $request, SessionService $sessionService): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $token = $jsonData['token'];

        try {
            $sessionService->deleteSession($token);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return $this->json([
            'message' => 'Logout successful',
        ]);
    }
}
