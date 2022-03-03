<?php declare(strict_types=1);

namespace Soyhuce\Testing\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Soyhuce\Testing\FormRequest\TestFormRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @see https://gist.github.com/colindecarlo/9ba9bd6524127fee7580ae66c6d4709d
 */
trait TestsFormRequests
{
    /**
     * @param array<string, mixed> $headers
     */
    protected function createRequest(string $requestClass, array $headers = []): TestFormRequest
    {
        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest('/test/route'),
            'POST',
            [],
            $this->prepareCookiesForRequest(),
            [],
            array_replace($this->serverVariables, $this->transformHeadersToServerVars($headers))
        );

        $formRequest = FormRequest::createFrom(
            Request::createFromBase($symfonyRequest),
            new $requestClass()
        )
            ->setContainer($this->app);

        $route = new Route('POST', '/test/route', fn () => null);
        $route->parameters = [];
        $formRequest->setRouteResolver(fn () => $route);

        return new TestFormRequest($formRequest);
    }
}
