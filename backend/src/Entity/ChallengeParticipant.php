<?php

namespace App\Entity;

use App\Repository\ChallengeParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ChallengeParticipantRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['challengeParticipant:read']],
    denormalizationContext: ['groups' => ['challengeParticipant:write']]
)]
class ChallengeParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['challengeParticipant:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['challengeParticipant:read', 'challengeParticipant:write'])]
    private ?ReadingChallenge $challenge = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['challengeParticipant:read', 'challengeParticipant:write'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['challengeParticipant:read', 'challengeParticipant:write'])]
    private ?int $progress = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChallenge(): ?ReadingChallenge
    {
        return $this->challenge;
    }

    public function setChallenge(?ReadingChallenge $challenge): static
    {
        $this->challenge = $challenge;
        return $this;
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

    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): static
    {
        $this->progress = $progress;
        return $this;
    }
} 