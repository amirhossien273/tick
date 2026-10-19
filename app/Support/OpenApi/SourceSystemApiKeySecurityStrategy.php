<?php

namespace App\Support\OpenApi;

use App\Http\Middleware\SourceSystemApiKeyMiddleware;
use Dedoc\Scramble\Configuration\OperationTransformers;
use Dedoc\Scramble\Configuration\SecurityDocumentationContext;
use Dedoc\Scramble\Contracts\SecurityDocumentationStrategy;
use Dedoc\Scramble\GeneratorConfig;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\SecurityRequirement;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class SourceSystemApiKeySecurityStrategy implements SecurityDocumentationStrategy
{
    public function configure(SecurityDocumentationContext $context): GeneratorConfig
    {
        $hasProtectedRoutes = $context->routes->contains(
            fn (Route $route): bool => $this->routeRequiresApiKey($route),
        );

        if (! $hasProtectedRoutes) {
            return $context->config;
        }

        return $context->config
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->components->addSecurityScheme('sourceSystemBearer', $this->bearerScheme());
                $openApi->components->addSecurityScheme('sourceSystemApiKey', $this->headerScheme());
            })
            ->withOperationTransformers(function (OperationTransformers $transformers): void {
                $transformers->prepend(function (Operation $operation, RouteInfo $routeInfo): void {
                    if (! $this->routeRequiresApiKey($routeInfo->route)) {
                        $operation->security = [];

                        return;
                    }

                    $operation->security = [
                        new SecurityRequirement('sourceSystemBearer'),
                        new SecurityRequirement('sourceSystemApiKey'),
                    ];
                    $operation->addResponse(
                        Response::make(401)
                            ->setDescription('API Key ارسال نشده یا معتبر نیست.')
                            ->setContent('application/json', Schema::fromType(
                                (new ObjectType)
                                    ->addProperty('message', new StringType)
                                    ->setRequired(['message']),
                            )),
                    );
                });
            });
    }

    private function bearerScheme(): SecurityScheme
    {
        return SecurityScheme::http('bearer', 'API Key')
            ->as('sourceSystemBearer')
            ->setDescription('API Key سامانه را به صورت `Authorization: Bearer {API_KEY}` ارسال کنید.');
    }

    private function headerScheme(): SecurityScheme
    {
        return SecurityScheme::apiKey('header', 'X-API-Key')
            ->as('sourceSystemApiKey')
            ->setDescription('روش جایگزین: API Key سامانه را در هدر `X-API-Key` ارسال کنید.');
    }

    private function routeRequiresApiKey(Route $route): bool
    {
        return collect($route->gatherMiddleware())->contains(
            fn (string $middleware): bool => Str::is(
                ['source-system.api-key', SourceSystemApiKeyMiddleware::class],
                $middleware,
            ),
        );
    }
}
