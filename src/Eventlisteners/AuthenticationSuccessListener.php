<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AuthenticationSuccessListener
{

    public function __construct (SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
/**
 * @param AuthenticationSuccessEvent $event
 */
public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
{
    $data = $event->getData();
    $user = $event->getUser();

    if (!$user instanceof UserInterface) {
        return;
    }

    $data['user'] = [
        'id' => $user->getId(),
        'email' => $user->getEmail(),
        'firstname' => $user->getFirstname(),
        'lastname' => $user->getLastname(),

    ];

    $event->setData($data);
}
}