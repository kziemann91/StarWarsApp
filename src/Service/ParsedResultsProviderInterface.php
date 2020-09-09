<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\EntityInterface;
use App\Form\BasicEntityRepository;

interface ParsedResultsProviderInterface
{
    public function getPaginatedResults(BasicEntityRepository $repository): string;
    public function getResults(EntityInterface $entity): string;
}