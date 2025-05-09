<?php

namespace App\Entity;

use App\Repository\ReadingChallengeRepository;
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

#[ORM\Entity(repositoryClass: ReadingChallengeRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['readingChallenge:read']],
    denormalizationContext: ['groups' => ['readingChallenge:write']]
)]
class ReadingChallenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['readingChallenge:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'readingChallenges')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['readingChallenge:read', 'readingChallenge:write'])]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    #[Groups(['readingChallenge:read', 'readingChallenge:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['readingChallenge:read', 'readingChallenge:write'])]
    private ?string $goal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['readingChallenge:read', 'readingChallenge:write'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['readingChallenge:read', 'readingChallenge:write'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\OneToMany(mappedBy: 'challenge', targetEntity: ChallengeParticipant::class, orphanRemoval: true)]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(string $goal): static
    {
        $this->goal = $goal;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return Collection<int, ChallengeParticipant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(ChallengeParticipant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setChallenge($this);
        }
        return $this;
    }

    public function removeParticipant(ChallengeParticipant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            if ($participant->getChallenge() === $this) {
                $participant->setChallenge(null);
            }
        }
        return $this;
    }
} 