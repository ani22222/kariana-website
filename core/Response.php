<?php
namespace Core;

/**
 * HTTP Response Handler
 * Manages HTTP status codes, headers, output buffering, JSON encoding, and redirection
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Send HTTP response with headers and body
     */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            // Default content type if not set
            if (!isset($this->headers['Content-Type'])) {
                $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
            }

            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $this->content;
    }

    /**
     * Helper to create a JSON response
     */
    public static function json(array $data, int $statusCode = 200): self
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        return new self(
            $json !== false ? $json : '{}',
            $statusCode,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    /**
     * Helper to create a redirect response
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->setHeader('Location', $url);
        return $response;
    }
}
