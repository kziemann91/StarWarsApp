<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(
 *     repositoryClass="App\Repository\StarWarsCharacterRepository"
 * )
 * @ORM\Table(
 *     name="star_wars_character",
 *     indexes={
 *          @ORM\Index(name="NAME_IDX", columns="name")
 *     }
 * )
 */
class StarWarsCharacter implements EntityInterface
{
    use NameTrait;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Episode", mappedBy="starWarsCharacter")
     * @Serializer\SkipWhenEmpty()
     */
    private $episodes;

    /**
     * @Serializer\Exclude()
     * @ORM\ManyToMany(targetEntity=StarWarsCharacter::class)
     * @ORM\JoinTable(
     *     name="friends",
     *     joinColumns={@ORM\JoinColumn(name="star_wars_character_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="friend_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $friends;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
        $this->friends = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFriendsObjects(): Collection
    {
        return $this->friends;
    }

    public function addFriend(StarWarsCharacter $friend): StarWarsCharacter
    {
        if (!$this->friends->contains($friend) && $friend->getId() !== $this->getId()) {
            $this->friends[] = $friend;
        }

        return $this;
    }

    public function removeFriend(StarWarsCharacter $friend): void
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
        }
    }

    /**
     * @Serializer\VirtualProperty(name="friends")
     * @Serializer\SkipWhenEmpty()
     */
    public function getFriends(): array
    {
        $friends = [];
        foreach ($this->getFriendsObjects() as $friend) {
            $friends[] = $friend->getName();
        }

        return $friends;
    }
}