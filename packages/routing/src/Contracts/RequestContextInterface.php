<?php

namespace Essential\Routing\Contracts;

/**
 * Protocol-agnostic request context.
 * 
 * Base interface for ANY protocol (HTTP, CLI, gRPC, Events, etc.)
 * Provides generic metadata storage and body access.
 * 
 * Specific protocols extend with their own methods:
 * - HttpRequestInterface for HTTP protocol
 * - Custom interfaces for other protocols
 * 
 * This design allows the Router to work with any protocol through the base interface,
 * while still enabling protocol-specific adapters to access their specialized data.
 * 
 * Example:
 * 
 * HTTP:  HttpRequest extends RequestContextInterface + provides getMethod(), getHeaders()
 * CLI:   CliRequest extends RequestContextInterface + provides getCommand(), getArguments()
 * gRPC:  GrpcRequest extends RequestContextInterface + provides getService(), getMetadata()
 */
interface RequestContextInterface
{
    /**
     * Get metadata by key.
     * 
     * Metadata is flexible storage for protocol-specific data:
     * - HTTP: May store cookies, authenticated user, attributes, etc.
     * - CLI: May store environment variables, flags, working directory, etc.
     * - gRPC: May store service metadata, deadline, cancellation token, etc.
     * - Events: May store event source, timestamp, etc.
     * 
     * @param string $key The metadata key to retrieve
     * @param mixed $default Default value if key not found
     * @return mixed The metadata value, or $default if not found
     */
    public function getMetadata(string $key, mixed $default = null): mixed;
    
    /**
     * Get all metadata as array.
     * 
     * @return array All stored metadata
     */
    public function getAllMetadata(): array;
    
    /**
     * Get body/content.
     * 
     * Meaning varies by protocol:
     * - HTTP: Request body (POST data, JSON, form data, etc.)
     * - CLI: Command input or stdin
     * - Events: The event object
     * - gRPC: Request message
     * - WebSocket: Initial connection message
     * 
     * @return mixed The request body/content
     */
    public function getBody(): mixed;
}
