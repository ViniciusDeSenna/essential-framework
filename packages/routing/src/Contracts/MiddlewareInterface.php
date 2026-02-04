<?php

namespace Essential\Routing\Contracts;

/**
 * Protocol-agnostic middleware interface.
 * 
 * Uses base RequestContextInterface and ResponseContextInterface
 * to support middleware for any protocol (HTTP, CLI, Events, gRPC, etc.)
 * 
 * This allows a single middleware to work across multiple protocols
 * by reading/writing generic metadata instead of protocol-specific properties.
 */
interface MiddlewareInterface
{
    /**
     * Process request through middleware.
     * 
     * The middleware can:
     * - Read request data via getMetadata() or getBody()
     * - Set response metadata via setMetadata()
     * - Modify header equivalents (protocol-specific via metadata)
     * - Call $next to continue the pipeline
     * 
     * Works with any protocol by using abstraction instead of HTTP-specific methods.
     * 
     * @param RequestContextInterface $request The request context
     * @param callable $next The next middleware in the stack
     * @return ResponseContextInterface The response context
     */
    public function process(RequestContextInterface $request, callable $next): ResponseContextInterface;
}
