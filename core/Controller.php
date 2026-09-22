<?php
namespace Core;

/**
 * Base Controller
 * Provides rendering shortcuts, JSON responses, model instantiators, and redirection
 */
abstract class Controller
{
    /**
     * Render a view within a layout and return a Response object
     */
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main', int $statusCode = 200): Response
    {
        $content = View::render($view, $data, $layout);
        return new Response($content, $statusCode);
    }

    /**
     * Return a JSON response
     */
    protected function json(array $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    /**
     * Redirect to a local or absolute URL
     */
    protected function redirect(string $path, int $statusCode = 302): Response
    {
        // If relative path without protocol, prefix with request base URL
        if (!preg_match('#^https?://#i', $path)) {
            $request = new Request();
            $path = $request->url($path);
        }
        return Response::redirect($path, $statusCode);
    }

    /**
     * Model loader helper
     * Example: $this->model('Post') returns an instance of \App\Models\Post
     */
    protected function model(string $modelName): Model
    {
        $class = str_starts_with($modelName, '\\') ? $modelName : "\\App\\Models\\{$modelName}";
        if (!class_exists($class)) {
            throw new \RuntimeException("Model class not found: {$class}");
        }
        return new $class();
    }

    /**
     * Validate CSRF token from Request POST body or HTTP header
     */
    protected function validateCsrf(Request $request): bool
    {
        $token = $request->post('csrf_token') ?? $request->getHeader('X-CSRF-TOKEN');
        return Csrf::validate($token);
    }
}
