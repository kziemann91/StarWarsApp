<?php
declare(strict_types=1);

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BasicEntityRepository extends EntityRepository
{
    public function findAllPaginated(): QueryBuilder
    {
        return $this->createQueryBuilder('star_wars_characters');
    }
}