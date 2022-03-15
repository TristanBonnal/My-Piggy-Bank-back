<?php

namespace App\Controller\Api;

use App\Entity\Operation;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pot;
use App\Models\JsonError;
use App\Service\HandlePotType;
use App\Service\TotalCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OperationController extends AbstractController
{
    /**
     * Récupère les opérations (retrait ou dépots) liées à l'utilisateur authentifié
     *
     * @return Response
     * 
     * @Route("/api/operations", name="api_show_operations", methods = {"GET"})
     */
    public function showOperations(): Response
    {
        return $this->json(
            $this->getUser()->getOperations(), 
            Response::HTTP_OK,
            [],
            ['groups' => ['show_operation']]
        );
    }

    /**
     * Ajoute une opération (type=true pour dépot, sinon retrait)
     * 
     * @param TotalCalculator $calculator Calcule le montant d'une cagnotte
     * @return Response
     * 
     * @Route("/api/operations", name="api_add_operation", methods = {"POST"})
     */
    public function addOperation(EntityManagerInterface $doctrine, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, TotalCalculator $calculator, HandlePotType $typeHandler): Response
    {
        // Deserialisation du contenu du formulaire 
        $data = $request->getContent();
        try {
            $newOperation = $serializer->deserialize($data, Operation::class, "json");
            $newOperation->setUser($this->getUser());
        } catch (NotNormalizableValueException $e) {
            return new JsonResponse("Erreur de type pour le champ '". $e->getPath() . "': " . $e->getCurrentType() . " au lieu de : " . implode('|', $e->getExpectedTypes()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $errors = $validator->validate($newOperation);

        //Vérification de la cagnotte associée à l'opération
        $pot = $newOperation->getPot();
        try {
            if (!$pot) {
                throw new Exception("Cette cagnotte n'existe pas (identifiant erroné)", RESPONSE::HTTP_NOT_FOUND);
            }

            $this->denyAccessUnlessGranted('USER', $newOperation->getPot()->getUser(), "Vous n'avez pas accès à cette cagnotte");

            //Vérification du mode de déblocage en cas de demande de retrait (voir Service/HandleTypePot)
            $typeHandler->checkType($newOperation);

            //Vérification du solde de la cagnotte en cas de retrait
            if (!$newOperation->getType() && ($newOperation->getAmount() > $pot->getAmount())) {
                throw new Exception('Retrait supérieur au montant de la cagnotte :(', Response::HTTP_BAD_REQUEST);
            }

        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }

        //Vérification des données du formulaire
        $errors = $validator->validate($newOperation);
        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, "Des erreurs de validation ont été trouvées");
            $myJsonError->setValidationErrors($errors);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $doctrine->persist($newOperation);
        $calculator->calculateAmount($pot, $newOperation);     //Recalcule le montant de la cagnotte après opération
        $doctrine->flush();

        return $this->json(
            $newOperation, Response::HTTP_CREATED,
            [],
            ['groups' => ['show_operation']]
        );
    }

    /**
     * Récupère les opérations liée à une cagnotte (tout en vérifiant l'utilisateur)
     * 
     * @param Pot $pot Cagnotte sur laquelle les opérations sont récupérées
     * @return Response
     * 
     * @Route("/api/pots/{id}/operations", name="api_show_operations_by_pot", methods = {"GET"})
     */
    public function showPotOperations(Pot $pot = null): Response
    {
        // Vérification de la cagnotte et de l'utilisateur
        try {
            if (!$pot) {
                throw new Exception("Cette cagnotte n'existe pas (identifiant erroné)", RESPONSE::HTTP_NOT_FOUND);
            }
            $this->denyAccessUnlessGranted('USER', $pot->getUser(), "Vous n'avez pas accès à cette cagnotte");
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