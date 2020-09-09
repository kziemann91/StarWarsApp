<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\StarWarsCharacter;
use App\Form\StarWarsCharacterType;
use App\Repository\StarWarsCharacterRepository;
use App\Service\ParsedResultsProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

/**
 * Class StarWarsCharacterController
 */
class StarWarsCharacterController extends Controller
{
    /**
     * @Rest\Get("/")
     * @SWG\Parameter(
     *     name="X-Request-Max-Results",
     *     in="header",
     *     type="integer"
     * ),
     * @SWG\Parameter(
     *     name="X-Request-Current-Page",
     *     in="header",
     *     type="integer"
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Success"
     * )
     */
    public function filterAction(Request $request, ParsedResultsProviderInterface $resultsProvider, StarWarsCharacterRepository $repository): Response
    {
        return new Response($resultsProvider->getPaginatedResults($repository));
    }

    /**
     * @Rest\Get("{character_id}")
     * @ParamConverter("character", options={"id" = "character_id"})
     * @SWG\Response(
     *     response="200",
     *     description="Success"
     * )
     */
    public function findCharacterAction(StarWarsCharacter $character, ParsedResultsProviderInterface $resultsProvider): Response
    {
        return new Response($resultsProvider->getResults($character));
    }

    /**
     * @Rest\Post()
     * @SWG\Parameter(
     *     in="body",
     *     name="character",
     *     @SWG\Schema(type="object", readOnly="true", ref=@Model(type=StarWarsCharacterType::class)
     *     )
     * ),
     * @SWG\Response(
     *     description="Character added",
     *     response="201"
     * )
     */
    public function addCharacterAction(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, ParsedResultsProviderInterface $resultsProvider): Response
    {
        $character = new StarWarsCharacter();

        $form = $formFactory->create(StarWarsCharacterType::class, $character);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new Response($form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($character);
        $entityManager->flush();

        return new JsonResponse(['id' => $character->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("{character_id}")
     * @ParamConverter("character", options={"id" = "character_id"})
     * @SWG\Response(
     *     response="200",
     *     description="Character deleted"
     * )
     */
    public function deleteCharacterAction(StarWarsCharacter $character, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($character);
        $entityManager->flush();

        return new Response(null, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("{character_id}")
     * @ParamConverter("character", options={"id" = "character_id"})
     * @SWG\Parameter(
     *     in="body",
     *     name="character",
     *     @SWG\Schema(type="object", readOnly="true", ref=@Model(type=StarWarsCharacterType::class)
     *     )
     * ),
     * @SWG\Response(
     *     description="Update success",
     *     response="204"
     * )
     */
    public function editCharacterAction(StarWarsCharacter $character, Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $form = $formFactory->create(StarWarsCharacterType::class, $character);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new Response($form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Post("{character_id}/add_friend/{friend_id}")
     * @ParamConverter("character", options={"id" = "character_id"})
     * @ParamConverter("friend", options={"id" = "friend_id"})
     * @SWG\Response(
     *     response="204",
     *     description="Friend added"
     * )
     */
    public function addFriend(StarWarsCharacter $character, StarWarsCharacter $friend, EntityManagerInterface $entityManager): Response
    {
        $character->addFriend($friend);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Delete("{character_id}/remove_friend/{friend_id}")
     * @ParamConverter("character", options={"id" = "character_id"})
     * @ParamConverter("friend", options={"id" = "friend_id"})
     * @SWG\Response(
     *     response="204",
     *     description="Friend added"
     * )
     */
    public function removeFriend(StarWarsCharacter $character, StarWarsCharacter $friend, EntityManagerInterface $entityManager): Response
    {
        if (!$character->getFriendsObjects()->contains($friend)) {
            return new JsonResponse(['error' => $friend->getName() . ' is not friend of ' . $character->getName()], Response::HTTP_NOT_FOUND);
        }

        $character->removeFriend($friend);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}