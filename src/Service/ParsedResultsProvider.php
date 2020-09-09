<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\EntityInterface;
use App\Form\BasicEntityRepository;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

class ParsedResultsProvider implements ParsedResultsProviderInterface
{
    const X_REQUEST_MAX_RESULTS = 'X-Request-Max-Results';
    const X_REQUST_CURRENT_PAGE = 'X-Request-Current-Page';

    /** @var Serializer */
    private $serializer;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(SerializerInterface $serializer, RequestStack $requestStack)
    {
        $this->serializer = $serializer;
        $this->requestStack = $requestStack;
    }

    public function getPaginatedResults(BasicEntityRepository $repository): string
    {
        $pagerfanta = $this->getPagerfanta($repository);

        $results = $pagerfanta->getCurrentPageResults();

        return $this->serializer->serialize($results, 'json');
    }

    public function getResults(EntityInterface $entity): string
    {
        return $this->serializer->serialize($entity, 'json');
    }

    private function getMaxResults(): int
    {
        return $this->requestStack->getCurrentRequest()->headers->has(self::X_REQUEST_MAX_RESULTS)
            ? intval($this->requestStack->getCurrentRequest()->headers->get(self::X_REQUEST_MAX_RESULTS))
            : 10;
    }

    private function getCurrentPage(): int
    {
        return $this->requestStack->getCurrentRequest()->headers->has(self::X_REQUST_CURRENT_PAGE)
            ? intval($this->requestStack->getCurrentRequest()->headers->get(self::X_REQUST_CURRENT_PAGE))
            : 1;
    }

    private function getPagerfanta(EntityRepository $repository): Pagerfanta
    {
        $adapter = new QueryAdapter($repository->findAllPaginated());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->getMaxResults());
        $pagerfanta->setAllowOutOfRangePages(true);
        $pagerfanta->setCurrentPage($this->getCurrentPage());

        return $pagerfanta;
    }
}