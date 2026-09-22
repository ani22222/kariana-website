<?php
namespace Core;

/**
 * Unicode-Compliant Regex Router
 * Native PCRE /u engine supporting multi-byte Bengali URL slugs, route parameters, and middleware pipelines
 */
class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private ?\Closure $notFoundHandler = null;

    /**
     * Register a GET route
     */
    public function get(string $path, array|callable $handler, array $middlewares = [], string $name = ''): self
    {
        $this->addRoute('GET', $path, $handler, $middlewares, $name);
        return $this;
    }

    /**
     * Register a POST route
     */
    public function post(string $path, array|callable $handler, array $middlewares = [], string $name = ''): self
    {
        $this->addRoute('POST', $path, $handler, $middlewares, $name);
        return $this;
    }

    /**
     * Register a PUT route
     */
    public function put(string $path, array|callable $handler, array $middlewares = [], string $name = ''): self
    {
        $this->addRoute('PUT', $path, $handler, $middlewares, $name);
        return $this;
    }

    /**
     * Register a DELETE route
     */
    public function delete(string $path, array|callable $handler, array $middlewares = [], string $name = ''): self
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares, $name);
        return $this;
    }

    /**
     * Register a PATCH route
     */
    public function patch(string $path, array|callable $handler, array $middlewares = [], string $name = ''): self
    {
        $this->addRoute('PATCH', $path, $handler, $middlewares, $name);
        return $this;
    }

    /**
     * Register a route responding to any HTTP method
     */
    public function any(string $path, array|callable $handler, array $middlewares = [], string $name = ''): self
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
            $this->addRoute($method, $path, $handler, $middlewares, $name);
        }
        return $this;
    }

    /**
     * Add route internal definition
     */
    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares, string $name): void
    {
        $cleanPath = '/' . trim($path, '/');
        $cleanPath = $cleanPath === '' ? '/' : $cleanPath;

        $route = [
            'method'      => strtoupper($method),
            'path'        => $cleanPath,
            'handler'     => $handler,
            'middlewares' => $middlewares,
            'name'        => $name,
        ];

        $this->routes[] = $route;

        if (!empty($name)) {
            $this->namedRoutes[$name] = $cleanPath;
        }
    }

    /**
     * Set a custom 404 Not Found handler
     */
    public function setNotFound(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * Get route URL by name
     */
    public function getNamedRoute(string $name, array $params = []): ?string
    {
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }

        $path = $this->namedRoutes[$name];
        foreach ($params as $key => $val) {
            $path = str_replace('{' . $key . '}', (string)$val, $path);
        }
        return $path;
    }

    /**
     * Dispatch incoming request through the routing table
     */
    public function dispatch(Request $request): Response
    {
        $requestMethod = $request->getMethod();
        if ($requestMethod === 'HEAD') {
            $requestMethod = 'GET';
        }
        $requestPath = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Convert route pattern with parameters to Unicode regex
            // Example 1: /blog/{slug} -> #^/blog/(?P<slug>[^/]+)$#u
            // Example 2: /lesson/{id:\d+} -> #^/lesson/(?P<id>\d+)$#u
            $routePattern = $route['path'];

            $regexPattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([^}]+))?\}/u', function ($matches) {
                $paramName = $matches[1];
                $paramRegex = isset($matches[2]) && $matches[2] !== '' ? $matches[2] : '[^/]+';
                return '(?P<' . $paramName . '>' . $paramRegex . ')';
            }, $routePattern);

            $regex = '#^' . $regexPattern . '$#u';

            if (preg_match($regex, $requestPath, $matches)) {
                // Filter named string parameters captured by regex
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Execute Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $mwInstance = is_string($middleware) ? new $middleware() : $middleware;
                    $mwResult = $mwInstance->handle($request);
                    if ($mwResult instanceof Response) {
                        return $mwResult;
                    }
                }

                // Execute Route Handler
                return $this->invokeHandler($route['handler'], $request, $params);
            }
        }

        // 404 Route Not Found handling
        return $this->handleNotFound($request);
    }

    /**
     * Intelligently invoke callable or controller method matching signature
     */
    private function invokeHandler(array|callable $handler, Request $request, array $params): Response
    {
        if (is_array($handler)) {
            [$controllerClass, $method] = $handler;
            $controller = is_string($controllerClass) ? new $controllerClass() : $controllerClass;
            $refMethod = new \ReflectionMethod($controller, $method);

            $args = $this->buildArguments($refMethod, $request, $params);
            $result = $refMethod->invokeArgs($controller, $args);
        } elseif (is_callable($handler)) {
            $refFunc = new \ReflectionFunction(\Closure::fromCallable($handler));
            $args = $this->buildArguments($refFunc, $request, $params);
            $result = call_user_func_array($handler, $args);
        } else {
            throw new \RuntimeException("Invalid route handler provided");
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_string($result)) {
            return new Response($result, 200);
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return new Response('', 200);
    }

    /**
     * Build arguments array for reflection invoker
     */
    private function buildArguments(\ReflectionFunctionAbstract $ref, Request $request, array $params): array
    {
        $args = [];
        $parameters = $ref->getParameters();

        foreach ($parameters as $param) {
            $name = $param->getName();
            $type = $param->getType();
            $typeName = null;
            if ($type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();
            } elseif ($type instanceof \ReflectionUnionType) {
                foreach ($type->getTypes() as $subType) {
                    if ($subType instanceof \ReflectionNamedType && $subType->getName() === Request::class) {
                        $typeName = Request::class;
                        break;
                    }
                }
            }

            if ($typeName === Request::class || $name === 'request' || $name === 'req') {
                $args[] = $request;
            } elseif (array_key_exists($name, $params)) {
                $args[] = $params[$name];
            } elseif ($name === 'params') {
                $args[] = $params;
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                // Positional fallback from params
                $firstVal = reset($params);
                if ($firstVal !== false) {
                    $args[] = $firstVal;
                    array_shift($params);
                } else {
                    $args[] = null;
                }
            }
        }

        return $args;
    }

    /**
     * Handle 404 Not Found response
     */
    private function handleNotFound(Request $request): Response
    {
        if ($this->notFoundHandler !== null) {
            $result = call_user_func($this->notFoundHandler, $request);
            if ($result instanceof Response) {
                return $result;
            }
        }

        // Check if custom error view exists
        try {
            $html = View::render('errors/404', [
                'title' => 'পাতাটি পাওয়া যায়নি (৪০৪) | কারিয়ানা কুরআন',
                'requestedPath' => $request->getPath(),
            ]);
            return new Response($html, 404);
        } catch (\Throwable $e) {
            // Minimal fallback
            $fallbackHtml = '<!DOCTYPE html><html lang="bn"><head><meta charset="utf-8"><title>৪০৪ - পাওয়া যায়নি</title>'
                . '<style>body{font-family:sans-serif;text-align:center;padding:50px;background:#fcfbf7;color:#064e3b;}'
                . 'h1{font-size:3rem;margin-bottom:10px;}p{font-size:1.2rem;color:#64748b;}'
                . 'a{display:inline-block;margin-top:20px;padding:10px 20px;background:#047857;color:#fff;text-decoration:none;border-radius:6px;}'
                . '</style></head><body>'
                . '<h1>৪০৪</h1><p>দুঃখিত, আপনি যে পাতাটি খুঁজছেন তা পাওয়া যায়নি।</p>'
                . '<a href="' . $request->getBaseUrl() . '/">মূল পাতায় ফিরে যান</a>'
                . '</body></html>';
            return new Response($fallbackHtml, 404);
        }
    }
}
