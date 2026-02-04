<?php

namespace Essential\Routing\Contracts;

/**
 * HTTP-specific request interface.
 * 
 * Extends the protocol-agnostic RequestContextInterface with HTTP-specific methods.
 * 
 * Use RequestContextInterface for protocol-agnostic routing that should work across
 * multiple protocols.
 * 
 * Use HttpRequestInterface for HTTP-specific adapters and middleware that need HTTP methods.
 * 
 * @see RequestContextInterface For the base protocol-agnostic interface
 */
interface HttpRequestInterface extends RequestContextInterface
{
    /**
     * Get HTTP method (GET, POST, PUT, DELETE, PATCH, OPTIONS, HEAD, etc.)
     * 
     * @return string HTTP method in uppercase
     */
    public function getMethod(): string;
    
    /**
     * Get the full request URI including query string.
     * 
     * Example: "https://example.com/api/users?page=1&limit=10"
     * 
     * @return string The full URI
     */
    public function getUri(): string;
    
    /**
     * Get the request path without host or query string.
     * 
     * Example: "/api/users"
     * 
     * @return string The request path
     */
    public function getPath(): string;
    
    /**
     * Get all request headers as associative array.
     * 
     * Header names are typically case-insensitive.
     * Implementation decides case handling (usually normalized).
     * 
     * @return array Headers with name => value
     */
    public function getHeaders(): array;
    
    /**
     * Get a single request header by name.
     * 
     * Case-insensitive header name lookup.
     * 
     * @param string $name The header name
     * @return string|null The header value, or null if not found
     */
    public function getHeader(string $name): ?string;
    
    /**
     * Get query parameters from the URL query string.
     * 
     * Example: For URL "/users?page=1&name=john"
     * Returns: ["page" => "1", "name" => "john"]
     * 
     * @return array Query parameters
     */
    public function getQueryParams(): array;
    
    /**
     * Get body from RequestContextInterface
     * Inherited: public function getBody(): mixed;
     * 
     * For HTTP, typically contains POST/PUT/PATCH data (JSON, form data, etc.)
     */
}
