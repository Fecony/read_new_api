<?php

namespace App\Http\Integrations\News\Requests;

use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\SoloRequest;

class TheGuardianRequest extends SoloRequest
{
    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    protected function defaultAuth(): ?Authenticator
    {
        return new QueryAuthenticator('api-key', config('services.news_providers.the_guardian'));
    }

    /**
     * Define the endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return 'https://content.guardianapis.com/search';
    }
}
