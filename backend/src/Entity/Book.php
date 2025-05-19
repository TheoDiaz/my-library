<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['book:read']],
    denormalizationContext: ['groups' => ['book:write']]
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['book:read', 'libraryBook:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['book:read', 'book:write', 'libraryBook:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['book:read', 'book:write', 'libraryBook:read'])]
    private ?string $author = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book:read', 'book:write', 'libraryBook:read'])]
    private ?int $firstPublishYear = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['book:read', 'book:write', 'libraryBook:read'])]
    private ?string $cover = null;

    #[ORM\Column(length: 13, nullable: true)]
    #[Groups(['book:read', 'book:write', 'libraryBook:read'])]
    private ?string $isbn = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['book:read', 'book:write', 'libraryBook:read'])]
    private ?string $googleBooksId = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['book:read'])]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getFirstPublishYear(): ?int
    {
        return $this->firstPublishYear;
    }

    public function setFirstPublishYear(?int $firstPublishYear): static
    {
        $this->firstPublishYear = $firstPublishYear;
        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): static
    {
        $this->cover = $cover;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getGoogleBooksId(): ?string
    {
        return $this->googleBooksId;
    }

    public function setGoogleBooksId(string $googleBooksId): static
    {
        $this->googleBooksId = $googleBooksId;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }
}