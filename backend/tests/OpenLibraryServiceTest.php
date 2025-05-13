<?php

namespace App\Tests;

use App\Service\OpenLibraryService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\CacheInterface;

class OpenLibraryServiceTest extends TestCase
{
    private OpenLibraryService $service;
    private MockHttpClient $httpClient;
    private CacheInterface $cache;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->cache = $this->createMock(CacheInterface::class);
        $this->service = new OpenLibraryService($this->httpClient, $this->cache);
    }

    public function testSearchBooks(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'docs' => [
                [
                    'title' => 'Test Book',
                    'author_name' => ['Test Author'],
                    'first_publish_year' => 2024,
                    'cover_i' => 123456,
                    'isbn' => ['9781234567890']
                ]
            ]
        ]));

        $this->httpClient->setResponseFactory($mockResponse);
        $this->cache->method('get')->willReturnCallback(function ($key, $callback) {
            return $callback();
        });

        $results = $this->service->searchBooks('test');
        
        $this->assertCount(1, $results);
        $this->assertEquals('Test Book', $results[0]->title);
        $this->assertEquals('Test Author', $results[0]->authorName);
    }

    public function testGetBookDetails(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'title' => 'Test Book',
            'authors' => [['name' => 'Test Author']],
            'publish_date' => '2024'
        ]));

        $this->httpClient->setResponseFactory($mockResponse);
        $this->cache->method('get')->willReturnCallback(function ($key, $callback) {
            return $callback();
        });

        $details = $this->service->getBookDetails('OL123456W');
        
        $this->assertEquals('Test Book', $details['title']);
        $this->assertEquals('Test Author', $details['authors'][0]['name']);
    }

    public function testSearchByIsbn(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'title' => 'Test Book',
            'authors' => [['name' => 'Test Author']],
            'publish_date' => '2024',
            'covers' => [123456]
        ]));

        $this->httpClient->setResponseFactory($mockResponse);
        $this->cache->method('get')->willReturnCallback(function ($key, $callback) {
            return $callback();
        });

        $results = $this->service->searchByIsbn('9781234567890');
        
        $this->assertCount(1, $results);
        $this->assertEquals('Test Book', $results[0]->title);
        $this->assertEquals('Test Author', $results[0]->authorName);
    }
} 