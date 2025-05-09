<?php

namespace App\Entity;

use App\Repository\LibraryBookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

#[ORM\Entity(repositoryClass: LibraryBookRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            description: 'Récupère un livre de la bibliothèque par son ID',
            normalizationContext: ['groups' => ['libraryBook:read']]
        ),
        new GetCollection(
            description: 'Récupère la liste des livres de la bibliothèque',
            normalizationContext: ['groups' => ['libraryBook:read']]
        ),
        new Post(
            description: 'Ajoute un nouveau livre à la bibliothèque',
            denormalizationContext: ['groups' => ['libraryBook:write']]
        ),
        new Put(
            description: 'Met à jour un livre de la bibliothèque',
            denormalizationContext: ['groups' => ['libraryBook:write']]
        ),
        new Delete(
            description: 'Supprime un livre de la bibliothèque'
        )
    ],
    normalizationContext: ['groups' => ['libraryBook:read']],
    denormalizationContext: ['groups' => ['libraryBook:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['user' => 'exact', 'book' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['startDate', 'endDate', 'rating'])]
class LibraryBook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['libraryBook:read'])]
    #[ApiProperty(description: 'L\'identifiant unique du livre dans la bibliothèque')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'libraryBooks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['libraryBook:read', 'libraryBook:write'])]
    #[ApiProperty(description: 'L\'utilisateur propriétaire du livre')]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['libraryBook:read', 'libraryBook:write'])]
    #[ApiProperty(description: 'Le livre associé')]
    private ?Book $book = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['libraryBook:read', 'libraryBook:write'])]
    #[ApiProperty(description: 'La date de début de lecture')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['libraryBook:read', 'libraryBook:write'])]
    #[ApiProperty(description: 'La date de fin de lecture')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['libraryBook:read', 'libraryBook:write'])]
    #[ApiProperty(description: 'La note attribuée au livre (sur 5)')]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['libraryBook:read', 'libraryBook:write'])]
    #[ApiProperty(description: 'Les commentaires sur le livre')]
    private ?string $comments = null;

    #[ORM\OneToMany(mappedBy: 'libraryBook', targetEntity: Loan::class, orphanRemoval: true)]
    private Collection $loans;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'libraryBooks')]
    #[Groups(['libraryBook:read', 'libraryBook:write'])]
    private Collection $categories;

    public function __construct()
    {
        $this->loans = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): static
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return Collection<int, Loan>
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): static
    {
        if (!$this->loans->contains($loan)) {
            $this->loans->add($loan);
            $loan->setLibraryBook($this);
        }
        return $this;
    }

    public function removeLoan(Loan $loan): static
    {
        if ($this->loans->removeElement($loan)) {
            if ($loan->getLibraryBook() === $this) {
                $loan->setLibraryBook(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }
} 