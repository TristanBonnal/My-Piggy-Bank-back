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
        'roles' => $user->getRoles(),
        'lastname' => $user->getLastname(),
        'password' => $user->getPassword(),
        'birthDate' => $user->getBirthDate(),
        'status' => $user->getStatus(),
        'phone' => $user->getPhone(),
        'iban' => $user->getIban(),
        'bic' => $user->getBic(),
        'createdAt' => $user->getCreatedAt(),
        'updatedAt' => $user->getUpdatedAt()

    ];

    $event->setData($data);
}
}