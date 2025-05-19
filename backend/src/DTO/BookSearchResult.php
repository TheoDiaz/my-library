<?php

namespace App\DTO;

class BookSearchResult
{
    private ?string $title = null;
    private ?string $authorName = null;
    private ?int $firstPublishYear = null;
    private ?int $coverId = null;
    private ?string $isbn = null;
    private ?string $editionKey = null;
    private ?string $key = null;

    public function __construct(
        ?string $title = null,
        ?string $authorName = null,
        ?int $firstPublishYear = null,
        ?int $coverId = null,
        ?string $isbn = null,
        ?string $editionKey = null,
        ?string $key = null
    ) {
        $this->title = $title;
        $this->authorName = $authorName;
        $this->firstPublishYear = $firstPublishYear;
        $this->coverId = $coverId;
        $this->isbn = $isbn;
        $this->editionKey = $editionKey;
        $this->key = $key;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? null,
            $data['author_name'] ?? null,
            $data['first_publish_year'] ?? null,
            $data['cover_i'] ?? null,
            is_array($data['isbn'] ?? null) ? ($data['isbn'][0] ?? null) : ($data['isbn'] ?? null),
            is_array($data['edition_key'] ?? null) ? ($data['edition_key'][0] ?? null) : ($data['edition_key'] ?? null),
            $data['key'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author_name' => $this->authorName,
            'first_publish_year' => $this->firstPublishYear,
            'cover_i' => $this->coverId,
            'isbn' => $this->isbn,
            'edition_key' => $this->editionKey,
            'key' => $this->key
        ];
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function getFirstPublishYear(): ?int
    {
        return $this->firstPublishYear;
    }

    public function getCoverId(): ?int
    {
        return $this->coverId;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function getEditionKey(): ?string
    {
        return $this->editionKey;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }
}