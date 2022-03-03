<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Entity\Pot;
use App\Entity\User;
use App\Models\JsonError;
use App\Repository\OperationRepository;
use App\Service\TotalCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
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
            ['groups' => ['add_user']]
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
            ['groups' => ['update_user']]
        );
    }

    /**
     * @Route("/api/pots", name="api_add_pot", methods = {"POST"})
     */
    public function addPot(EntityManagerInterface $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();
        try {
            $newPot = $serializer->deserialize($data, Pot::class, "json");
            $newPot->setUser($this->getUser());
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $validator->validate($newPot);

        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, "Des erreurs de validation ont été trouvées");
            $myJsonError->setValidationErrors($errors);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $doctrine->persist($newPot);
        $doctrine->flush();

        return $this->json(
            $newPot, Response::HTTP_CREATED,
            [],
            ['groups' => ['show_pot']]
        );
    }

    /**
     * Retourne les cagnottes liées à un utilisateur
     * 
     * @Route("/api/pots", name="api_pots", methods = {"GET"})
     */
    public function potsByUser(): Response
    {
        return $this->json(
            $this->getUser()->getPots(), 
            Response::HTTP_OK,
            [],
            ['groups' => ['show_pot']]
        );
    }

    /**
     * @Route("/api/pots/{id}", name="api_show_pot", methods = {"GET"})
     */
    public function showPot(Pot $pot = null, TotalCalculator $calculator): Response
    {
        try {
            if (!$pot) {
                throw new Exception('Cette cagnotte n\'existe pas (identifiant erroné)', RESPONSE::HTTP_NOT_FOUND);
            }
            $this->denyAccessUnlessGranted('USER', $pot->getUser(), 'Vous n\'avez pas accès à cette cagnotte');
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        //Récupération du total des opérations d'une cagnotte
        $calculator->calculateAmount($pot);

        return $this->json(
            $pot, 
            Response::HTTP_OK,
            [],
            ['groups' => ['show_pot']]
        );
    }

    /**
     * @Route("/api/pots/{id}", name="api_update_pot", methods = {"PATCH"})
     */
    public function updatePot(Pot $pot = null,EntityManagerInterface $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();

        try {
            if (!$pot) {
                throw new Exception('Cette cagnotte n\'existe pas (identifiant erroné)', RESPONSE::HTTP_NOT_FOUND);
            }
            $this->denyAccessUnlessGranted('USER', $pot->getUser(), 'Vous n\'avez pas accès à cette cagnotte');
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        try {
            $newPot = $serializer->deserialize($data, Pot::class, "json");
            $newPot->setUser($this->getUser());
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $pot
            ->setName($newPot->getName())
            ->setDateGoal($newPot->getDateGoal())
            ->setAmountGoal($newPot->getAmountGoal())
            ->setUpdatedAt(new \DateTime)
        ;
        $errors = $validator->validate($newPot);

        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, "Des erreurs de validation ont été trouvées");
            $myJsonError->setValidationErrors($errors);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $doctrine->flush();    
        
        return $this->json(
            $pot, 
            Response::HTTP_OK,
            [],
            ['groups' => ['show_pot']]
        );
    }

    /**
     * @Route("/api/operations", name="api_show_operations", methods = {"GET"})
     */
    public function showOperation(): Response
    {
        return $this->json(
            $this->getUser()->getOperations(), 
            Response::HTTP_OK,
            [],
            ['groups' => ['show_operation']]
        );
    }

    /**
     * @Route("/api/operations", name="api_add_operation", methods = {"POST"})
     */
    public function addOperation(EntityManagerInterface $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        //Deserialisation 
        $data = $request->getContent();
        try {
            $newOperation = $serializer->deserialize($data, Operation::class, "json");
            $newOperation->setUser($this->getUser());
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Vérification de la cagnotte associée à l'opération
        try {
            if (!$newOperation->getPot()) {
                throw new Exception('Cette cagnotte n\'existe pas (identifiant erroné)', RESPONSE::HTTP_NOT_FOUND);
            }
            $this->denyAccessUnlessGranted('USER', $newOperation->getPot()->getUser(), 'Vous n\'avez pas accès à cette cagnotte');
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        //Vérification des données du formulaire
        $errors = $validator->validate($newOperation);
        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, "Des erreurs de validation ont été trouvées");
            $myJsonError->setValidationErrors($errors);
            return $this->json($myJsonError, $myJsonError->getError());
        }


        $doctrine->persist($newOperation);
        $doctrine->flush();

        return $this->json(
            $newOperation, Response::HTTP_CREATED,
            [],
            ['groups' => ['show_operation']]
        );
    }

    /**
     * @Route("/api/pots/{id}/operations", name="api_show_operations_by_pot", methods = {"GET"})
     */
    public function showOperations(Pot $pot = null): Response
    {
        try {
            if (!$pot) {
                throw new Exception('Cette cagnotte n\'existe pas (identifiant erroné)', RESPONSE::HTTP_NOT_FOUND);
            }
            $this->denyAccessUnlessGranted('USER', $pot->getUser(), 'Vous n\'avez pas accès à cette cagnotte');
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        return $this->json(
            $pot->getOperations(), 
            Response::HTTP_OK,
            [],
            ['groups' => ['show_operation']]
        );
    }

}
            