<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['category:read']],
    denormalizationContext: ['groups' => ['category:write']]
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['category:read', 'category:write'])]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    #[Groups(['category:read', 'category:write'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: LibraryBook::class, mappedBy: 'categories')]
    private Collection $libraryBooks;

    public function __construct()
    {
        $this->libraryBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, LibraryBook>
     */
    public function getLibraryBooks(): Collection
    {
        return $this->libraryBooks;
    }

    public function addLibraryBook(LibraryBook $libraryBook): static
    {
        if (!$this->libraryBooks->contains($libraryBook)) {
            $this->libraryBooks->add($libraryBook);
            $libraryBook->addCategory($this);
        }
        return $this;
    }

    public function removeLibraryBook(LibraryBook $libraryBook): static
    {
        if ($this->libraryBooks->removeElement($libraryBook)) {
            $libraryBook->removeCategory($this);
        }
        return $this;
    }
} 