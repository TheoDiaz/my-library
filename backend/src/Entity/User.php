<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?array $preferences = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Book::class, orphanRemoval: true)]
    #[Groups(['user:read'])]
    private Collection $books;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LibraryBook::class, orphanRemoval: true)]
    private Collection $libraryBooks;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Wishlist::class, orphanRemoval: true)]
    private Collection $wishlist;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Category::class, orphanRemoval: true)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ReadingChallenge::class, orphanRemoval: true)]
    private Collection $readingChallenges;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Group::class, orphanRemoval: true)]
    private Collection $groups;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->libraryBooks = new ArrayCollection();
        $this->wishlist = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->readingChallenges = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setOwner($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getOwner() === $this) {
                $book->setOwner(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getPreferences(): ?array
    {
        return $this->preferences;
    }

    public function setPreferences(?array $preferences): static
    {
        $this->preferences = $preferences;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
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
            $libraryBook->setUser($this);
        }
        return $this;
    }

    public function removeLibraryBook(LibraryBook $libraryBook): static
    {
        if ($this->libraryBooks->removeElement($libraryBook)) {
            if ($libraryBook->getUser() === $this) {
                $libraryBook->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getWishlist(): Collection
    {
        return $this->wishlist;
    }

    public function addToWishlist(Wishlist $wishlist): static
    {
        if (!$this->wishlist->contains($wishlist)) {
            $this->wishlist->add($wishlist);
            $wishlist->setUser($this);
        }
        return $this;
    }

    public function removeFromWishlist(Wishlist $wishlist): static
    {
        if ($this->wishlist->removeElement($wishlist)) {
            if ($wishlist->getUser() === $this) {
                $wishlist->setUser(null);
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
            $category->setUser($this);
        }
        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            if ($category->getUser() === $this) {
                $category->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ReadingChallenge>
     */
    public function getReadingChallenges(): Collection
    {
        return $this->readingChallenges;
    }

    public function addReadingChallenge(ReadingChallenge $readingChallenge): static
    {
        if (!$this->readingChallenges->contains($readingChallenge)) {
            $this->readingChallenges->add($readingChallenge);
            $readingChallenge->setUser($this);
        }
        return $this;
    }

    public function removeReadingChallenge(ReadingChallenge $readingChallenge): static
    {
        if ($this->readingChallenges->removeElement($readingChallenge)) {
            if ($readingChallenge->getUser() === $this) {
                $readingChallenge->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->setUser($this);
        }
        return $this;
    }

    public function removeGroup(Group $group): static
    {
        if ($this->groups->removeElement($group)) {
            if ($group->getUser() === $this) {
                $group->setUser(null);
            }
        }
        return $this;
    }
}

