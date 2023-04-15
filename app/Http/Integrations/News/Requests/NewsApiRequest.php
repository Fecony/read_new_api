<?php

namespace App\Http\Integrations\News\Requests;

use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\SoloRequest;

class NewsApiRequest extends SoloRequest
{
    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    protected function defaultAuth(): ?Authenticator
    {
        return new QueryAuthenticator('apiKey', config('services.news_providers.news_api'));
    }

    /**
     * Define the endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return 'https://newsapi.org/v2/top-headlines';
    }
}
