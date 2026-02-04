<?php

namespace Essential\Routing\Contracts;

/**
 * HTTP-specific response interface.
 * 
 * Extends the protocol-agnostic ResponseContextInterface with HTTP-specific methods.
 * 
 * Use ResponseContextInterface for protocol-agnostic routing that should work across
 * multiple protocols.
 * 
 * Use HttpResponseInterface for HTTP-specific adapters and middleware that need HTTP methods.
 * 
 * @see ResponseContextInterface For the base protocol-agnostic interface
 */
interface HttpResponseInterface extends ResponseContextInterface
{
    /**
     * Set HTTP status code.
     * 
     * Examples: 200 (OK), 404 (Not Found), 500 (Internal Server Error), etc.
     * 
     * @param int $code The HTTP status code (typically 100-599)
     * @return self Fluent interface for chaining
     */
    public function setStatusCode(int $code): self;
    
    /**
     * Get the current HTTP status code.
     * 
     * Returns the status code previously set with setStatusCode().
     * 
     * @return int The HTTP status code
     */
    public function getStatusCode(): int;
    
    /**
     * Set a single HTTP response header.
     * 
     * Example: $response->setHeader('Content-Type', 'application/json')
     * 
     * @param string $name The header name
     * @param string $value The header value
     * @return self Fluent interface for chaining
     */
    public function setHeader(string $name, string $value): self;
    
    /**
     * Set multiple HTTP response headers at once.
     * 
     * Example: $response->setHeaders(['Content-Type' => 'application/json', 'X-Custom' => 'value'])
     * 
     * @param array $headers Headers with name => value
     * @return self Fluent interface for chaining
     */
    public function setHeaders(array $headers): self;
    
    /**
     * Get all response headers as associative array.
     * 
     * @return array Headers with name => value
     */
    public function getHeaders(): array;
    
    /**
     * Set and Get methods from ResponseContextInterface:
     * 
     * Inherited from ResponseContextInterface:
     * - public function setMetadata(string $key, mixed $value): self;
     * - public function getMetadata(string $key, mixed $default = null): mixed;
     * - public function setBody(mixed $content): self;
     * - public function getBody(): mixed;
     * - public function send(): void;
     */
}
