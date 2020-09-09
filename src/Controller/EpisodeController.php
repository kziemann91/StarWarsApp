<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\StarWarsCharacter;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
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

class EpisodeController extends Controller
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
     *     description="Success",
     *     response="200"
     * )
     */
    public function filterAction(EpisodeRepository $episodeRepository, ParsedResultsProviderInterface $resultsProvider): Response
    {
        return new Response($resultsProvider->getPaginatedResults($episodeRepository));
    }

    /**
     * @Rest\Get("{episode_id}")
     * @ParamConverter("episode", options={"id" = "episode_id"})
     * @SWG\Response(
     *     description="Success",
     *     response="200"
     * )
     */
    public function findEpisodeAction(Episode $episode, ParsedResultsProviderInterface $resultsProvider): Response
    {
        return new Response($resultsProvider->getResults($episode));
    }

    /**
     * @Rest\Post()
     * @SWG\Parameter(
     *     in="body",
     *     name="episode",
     *     @SWG\Schema(type="object", readOnly="true", ref=@Model(type=EpisodeType::class)
     *     )
     * ),
     * @SWG\Response(
     *     description="Episode added",
     *     response="201"
     * )
     */
    public function addEpisodeAction(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $episode = new Episode();

        $form = $formFactory->create(EpisodeType::class, $episode);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new Response($form->getErrors(true,false), Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($episode);
        $entityManager->flush();

        return new JsonResponse(['id' => $episode->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("{episode_id}")
     * @ParamConverter("episode", options={"id" = "episode_id"})
     * @SWG\Response(
     *     description="Episode deleted",
     *     response="200"
     * )
     */
    public function deleteEpisodeAction(Episode $episode, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($episode);
        $entityManager->flush();

        return new Response(null, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("{episode_id}")
     * @ParamConverter("episode", options={"id" = "episode_id"})
     * @SWG\Parameter(
     *     in="body",
     *     name="episode",
     *     @SWG\Schema(type="object", readOnly="true", ref=@Model(type=EpisodeType::class)
     *     )
     * ),
     * @SWG\Response(
     *     description="Update success",
     *     response="204"
     * )
     */
    public function editEpisodeAction(Episode $episode, Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $form = $formFactory->create(EpisodeType::class, $episode);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new Response($form->getErrors(true,false), Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Post("{episode_id}/add_character/{character_id}")
     * @ParamConverter("episode", options={"id" = "episode_id"})
     * @ParamConverter("character", options={"id" = "character_id"})
     * @SWG\Response(
     *     description="Character added",
     *     response="204"
     * )
     */
    public function addCharacterToEpisodeAction(Episode $episode, StarWarsCharacter $character, EntityManagerInterface $entityManager): Response
    {
        $episode->addStarWarsCharacter($character);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}