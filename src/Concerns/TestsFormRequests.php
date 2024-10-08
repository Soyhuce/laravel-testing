<?php declare(strict_types=1);

namespace Soyhuce\Testing\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route;
use Soyhuce\Testing\FormRequest\TestFormRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @see https://gist.github.com/colindecarlo/9ba9bd6524127fee7580ae66c6d4709d
 */
trait TestsFormRequests
{
    /**
     * @template TFormRequest of \Illuminate\Foundation\Http\FormRequest
     * @param class-string<TFormRequest> $requestClass
     * @param array<string, mixed> $headers
     * @return TestFormRequest<TFormRequest>
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

        $request = Request::createFromBase($symfonyRequest);
        $this->app->instance('request', $request);

        $route = new Route('POST', '/test/route', fn () => null);
        $route->parameters = [];
        $request->setRouteResolver(fn () => $route);

        $formRequest = FormRequest::createFrom($request, new $requestClass())
            ->setContainer($this->app)
            ->setRedirector($this->app->make(Redirector::class));

        return new TestFormRequest($formRequest);
    }
}
