<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Studio;
use App\Entity\Comment;
use App\Entity\Category;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $posterFile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="game", orphanRemoval=true, fetch="EAGER")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="games", fetch="EAGER")
     * @ORM\JoinTable(name="game_category")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=Studio::class, inversedBy="games", fetch="EAGER")
     * @ORM\JoinTable(name="game_studio")
     */
    private $studios;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->studios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(?\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function getPosterFile(): ?string
    {
        return $this->posterFile;
    }

    public function setPosterFile(?string $posterFile): self
    {
        $this->posterFile = $posterFile;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setGame($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getGame() === $this) {
                $comment->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Studio[]
     */
    public function getStudios(): Collection
    {
        return $this->studios;
    }

    public function addStudio(Studio $studio): self
    {
        if (!$this->studios->contains($studio)) {
            $this->studios[] = $studio;
        }

        return $this;
    }

    public function removeStudio(Studio $studio): self
    {
        if ($this->studios->contains($studio)) {
            $this->studios->removeElement($studio);
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return (
            [
                "id" => $this->getId(),
                "name" => $this->getName(),
                "posterFile" => $this->getPosterFile(),
                "description" => $this->getDescription(),
                "releasedAt" => $this->getReleasedAt(),
                "comments" => $this->getComments(),
                "studios" => $this->getStudios(),
                "categories" => $this->getCategories()
            ]
        );
    }
}
