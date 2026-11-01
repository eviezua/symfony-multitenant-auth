<?php

namespace App;

use App\Entity\User;
use App\Repository\AccountRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class LoginSuccessHandlerDecorator extends AuthenticationSuccessHandler
{
    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        private readonly AccountRepository $accountRepository,
    ) {
        parent::__construct($jwtManager, $dispatcher);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $response = parent::onAuthenticationSuccess($request, $token);
        $user = $token->getUser();

        if ($response instanceof JsonResponse && $user instanceof User) {
            $data = json_decode($response->getContent(), true);

            $data['default_account_id'] = $this->accountRepository->findFirstAccountForUser($user)?->getId();

            $response->setData($data);
        }

        return $response;
    }
}
