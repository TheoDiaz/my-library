<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\LibraryBook;
use ApiPlatform\Symfony\Routing\IriConverter;
use Psr\Log\LoggerInterface;

class LibraryBookCollectionDataProvider implements ProviderInterface
{
    private CollectionProvider $collectionProvider;
    private TokenStorageInterface $tokenStorage;
    private IriConverter $iriConverter;
    private LoggerInterface $logger;

    public function __construct(CollectionProvider $collectionProvider, TokenStorageInterface $tokenStorage, IriConverter $iriConverter, LoggerInterface $logger)
    {
        $this->collectionProvider = $collectionProvider;
        $this->tokenStorage = $tokenStorage;
        $this->iriConverter = $iriConverter;
        $this->logger = $logger;
        $this->logger->info('[LibraryBookDataProvider] Constructeur appelé');
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $this->logger->info('[LibraryBookDataProvider] Méthode provide appelée');
        $user = $this->tokenStorage->getToken()?->getUser();
        $this->logger->info('[LibraryBookDataProvider] Utilisateur courant', ['user' => $user]);
        if (!$user || !is_object($user)) {
            $this->logger->warning('[LibraryBookDataProvider] Aucun utilisateur authentifié');
            return [];
        }
        //$userIri = $this->iriConverter->getIriFromResource($user);
        //$this->logger->info('[LibraryBookDataProvider] IRI utilisateur courant', ['userIri' => $userIri]);
        $context['filters']['user.id'] = $user->getId();
        $this->logger->info('[LibraryBookDataProvider] Contexte de filtre appliqué', ['filters' => $context['filters']]);
        return $this->collectionProvider->provide($operation, $uriVariables, $context);
    }
} 