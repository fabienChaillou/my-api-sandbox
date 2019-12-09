<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 * @ApiResource(attributes={"pagination_items_per_page"=3})
 */
class Player
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="players")
     */
    private $creator;

    /**
     * @var Game[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Game",
     *      mappedBy="player",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $games;

    /**
     * @var MediaObject|null
     *
     * @ORM\OneToOne(targetEntity="MediaObject")
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     */
    protected $avatar;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setPlayer($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getPlayer() === $this) {
                $game->setPlayer(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?MediaObject
    {
        return $this->avatar;
    }

    public function setAvatar(?MediaObject $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
