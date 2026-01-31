<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client for Givebutter Contacts API (paginated).
 * Fetches all pages using links.next or page query parameter.
 */
class GivebutterContactsClient
{
    public function __construct(
        protected string $baseUrl,
        protected string $apiKey,
        protected int $delayBetweenPagesMs = 300
    ) {}

    public static function fromConfig(): self
    {
        $config = config('services.givebutter', []);
        $baseUrl = rtrim($config['api_url'] ?? 'https://api.givebutter.com/v1', '/');
        $apiKey = $config['api_key'] ?? '';

        return new self($baseUrl, $apiKey);
    }

    /**
     * Fetch all contacts, yielding each page's data array and meta.
     * Keys in yielded: 'data' => array of contact objects, 'meta' => [ current_page, last_page, total, ... ]
     *
     * @return \Generator<array{data: array, meta: array}>
     * @throws RequestException
     */
    public function fetchAllPages(): \Generator
    {
        $url = $this->baseUrl . '/contacts';
        $page = 1;

        do {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->get($url, $page > 1 ? ['page' => $page] : []);

            $response->throw();

            $body = $response->json();
            $data = $body['data'] ?? [];
            $meta = $body['meta'] ?? [];
            $links = $body['links'] ?? [];

            yield [
                'data' => $data,
                'meta' => $meta,
            ];

            $nextUrl = $links['next'] ?? null;
            $currentPage = (int) ($meta['current_page'] ?? $page);
            $lastPage = (int) ($meta['last_page'] ?? 1);

            if ($nextUrl) {
                $url = $nextUrl;
                $page = $currentPage + 1;
            } else {
                $page = $currentPage + 1;
                $url = $this->baseUrl . '/contacts';
            }

            if ($currentPage >= $lastPage || empty($data)) {
                break;
            }

            if ($this->delayBetweenPagesMs > 0) {
                usleep($this->delayBetweenPagesMs * 1000);
            }
        } while (true);
    }

    /**
     * Test connectivity (single page request).
     */
    public function ping(): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->get($this->baseUrl . '/contacts', ['page' => 1]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Givebutter contacts ping failed', ['message' => $e->getMessage()]);

            return false;
        }
    }
}
