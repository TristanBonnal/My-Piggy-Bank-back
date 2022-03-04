<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Models\JsonError;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/signup", name="api_signup", methods = {"POST"})
     */
    public function signUp(EntityManagerInterface $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordHasherInterface $hasher ): Response
    {
        $data = $request->getContent();
        try {
            $newUser = $serializer->deserialize($data, User::class, "json");
            $hashedPassword = $hasher->hashPassword($newUser, $newUser->getPassword());
            $newUser->setPassword($hashedPassword);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $validator->validate($newUser);

        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, "Des erreurs de validation ont été trouvées");
            $myJsonError->setValidationErrors($errors);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $doctrine->persist($newUser);
        $doctrine->flush();

        return $this->json(
            $newUser, Response::HTTP_CREATED,
            [],
            ['groups' => ['show_user']]
        );
    }

    /**
     * @Route("/api/users", name="api_show_user", methods = {"GET"})
     */
    public function showUser(): Response
    {
        return $this->json(
            $this->getUser(), 
            Response::HTTP_OK,
            [],
            ['groups' => ['show_user']]
        );
    }

    /**
     * @Route ("/api/users", name="api_update_user", methods = {"PATCH"})
     */
    public function updateUser(User $user = null, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $doctrine, UserPasswordHasherInterface $hasher): Response 
    {
        $data = $request->getContent();
        $user = $this->getUser();
        try {
            $updatedUser = $serializer->deserialize($data, User::class, "json");
            $hashedPassword = $hasher->hashPassword($updatedUser, $updatedUser->getPassword());
            $updatedUser->setPassword($hashedPassword);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user
            ->setPassword($updatedUser->getPassword())
            ->setFirstname($updatedUser->getFirstName())
            ->setLastname($updatedUser->getLastName())
            ->setBirthDate($updatedUser->getBirthDate())
            ->setPhone($updatedUser->getPhone())
            ->setIban($updatedUser->getIban())
            ->setBic($updatedUser->getBic())
            ->setUpdatedAt(new \DateTime)
        ;

        $errors = $validator->validate($user);
        
        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, "Des erreurs de validation ont été trouvées");
            $myJsonError->setValidationErrors($errors);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $doctrine->flush();

        return $this->json(
            $user, Response::HTTP_OK,
            [],
            ['groups' => ['show_user']]
        );
    }
}