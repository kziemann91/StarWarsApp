<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(
 *     repositoryClass="App\Repository\EpisodeRepository"
 * )
 * @ORM\Table(
 *     name="episode"
 * )
 */
class Episode implements EntityInterface
{
    const MOVIE_NAMES = [
        'A New Hope',
        'The Empire Strikes Back',
        'Return of the Jedi',
        'The Phantom Menace',
        'Attack of the Clones',
        'Revenge of the Sith',
        'The Force Awakens',
        'The Last Jedi',
        'The Rise of Skywalker'
    ];

    use NameTrait;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\StarWarsCharacter", inversedBy="episodes")
     * @ORM\JoinTable(name="character_in_episodes")
     * @Serializer\Exclude()
     */
    private $starWarsCharacter;

    public function __construct()
    {
        $this->starWarsCharacter = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addStarWarsCharacter(StarWarsCharacter $character): void
    {
        $this->starWarsCharacter->add($character);
    }
}