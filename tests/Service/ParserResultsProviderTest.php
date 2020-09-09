<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\StarWarsCharacter;
use App\Form\BasicEntityRepository;
use App\Service\ParsedResultsProvider;
use App\Service\ParsedResultsProviderInterface;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\SerializerInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\MockObject\MockObject as Mock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ParserResultsProviderTest extends TestCase
{
    /** @var ParsedResultsProviderInterface */
    private $resultsParser;

    /** @var SerializerInterface|Mock */
    private $jmsSerializerMock;

    /** @var RequestStack|Mock */
    private $requestStackMock;

    /** @var BasicEntityRepository|Mock */
    private $entityRepositoryMock;

    /** @var QueryBuilder */
    private $queryBuilderMock;

    /** @var Pagerfanta|Mock */
    private $pagerfantaMock;

    protected function setUp(): void
    {
        $this->jmsSerializerMock = $this->createMock(SerializerInterface::class);
        $this->requestStackMock = $this->createMock(RequestStack::class);
        $this->entityRepositoryMock = $this->createMock(BasicEntityRepository::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->pagerfantaMock = $this->createMock(Pagerfanta::class);

        $this->resultsParser = new ParsedResultsProvider($this->jmsSerializerMock, $this->requestStackMock);
    }

    /**
     * @test
     */
    public function shouldReturnParsedEntity(): void
    {
        $this->requestStackMock
            ->expects($this->never())
            ->method('getCurrentRequest');

        $result = $this->resultsParser->getResults($this->getStarWarsCharacter());

        $this->assertIsString($result);

        // i cant test here anything more, serializer is final class, that cannot be mocked
    }

    /**
     * @test
     */
    public function shouldReturnPagerfantaInstance(): void
    {
        $this->requestStackMock
            ->expects($this->exactly(2))
            ->method('getCurrentRequest')
            ->willReturn(new Request());

        $reflectionClass = new \ReflectionClass(ParsedResultsProvider::class);
        $method = $reflectionClass->getMethod('getPagerfanta');
        $method->setAccessible(true);

        $this->assertInstanceOf(Pagerfanta::class, $method->invoke($this->resultsParser, $this->entityRepositoryMock));

        // as i cant test getPaginatedResults because i cant mock pagerfanta that will return some values,
        // i decided to test only this method
    }

    private function getStarWarsCharacter(): StarWarsCharacter
    {
        $starWarsCharacter = new StarWarsCharacter();
        $starWarsCharacter->setName('Luke Skywalker');

        return $starWarsCharacter;
    }
}