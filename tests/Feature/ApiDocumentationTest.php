<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_ticket_endpoints_document_both_supported_api_key_methods(): void
    {
        $this->app->detectEnvironment(fn (): string => 'local');

        $response = $this->getJson('/docs/api.json');

        $response->assertOk()
            ->assertJsonPath('components.securitySchemes.sourceSystemBearer.type', 'http')
            ->assertJsonPath('components.securitySchemes.sourceSystemBearer.scheme', 'bearer')
            ->assertJsonPath('components.securitySchemes.sourceSystemApiKey.type', 'apiKey')
            ->assertJsonPath('components.securitySchemes.sourceSystemApiKey.in', 'header')
            ->assertJsonPath('components.securitySchemes.sourceSystemApiKey.name', 'X-API-Key')
            ->assertJsonPath('paths./v1/tickets.post.security.0.sourceSystemBearer', [])
            ->assertJsonPath('paths./v1/tickets.post.security.1.sourceSystemApiKey', [])
            ->assertJsonPath('paths./v1/tickets.post.responses.401.description', 'API Key ارسال نشده یا معتبر نیست.')
            ->assertJsonMissingPath('components.schemas.StoreTicketRequest.properties.external_ticket_id');
    }
}
