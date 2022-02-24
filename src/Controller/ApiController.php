<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/signup", name="api", methods = {"POST"})
     */
    public function index(EntityManagerInterface $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validator ): Response
    {
        $data = $request->getContent();
        // dd($newUser);
        try {
            $newUser = $serializer->deserialize($data, User::class, "json");
        } catch (Exception $e) {
            return new JsonResponse("Données formulaires invalides", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $validator->validate($newUser);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString);
        }

        $doctrine->persist($newUser);
        $doctrine->flush();

        return $this->json(
            $newUser, Response::HTTP_CREATED,
            [],
            ['groups' => ['add_user']]
        );
    }
}
