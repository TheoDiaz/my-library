<?php

namespace App\DTO;

class BookSearchResult
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $authorName,
        public readonly ?int $firstPublishYear,
        public readonly ?int $coverId,
        public readonly ?string $isbn
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? null,
            $data['author_name'] ?? null,
            $data['first_publish_year'] ?? null,
            $data['cover_i'] ?? null,
            $data['isbn'] ?? null
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
        ];
    }
} 