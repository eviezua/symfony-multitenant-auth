<?php

namespace App\EventSubscriber;

use App\Security\Voter\AccountVoter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AccountContextSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $accountId = $request->headers->get('Account-ID');

        if (!$accountId) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        $targetAccountId = (int)$accountId;

        if ($this->authorizationChecker->isGranted(AccountVoter::CAN_ACCESS, $targetAccountId)) {
            $token?->setAttribute('current_account_id', $targetAccountId);
        }
    }
}
