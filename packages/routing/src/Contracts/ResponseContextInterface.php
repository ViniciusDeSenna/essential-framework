<?php

namespace Essential\Routing\Contracts;

/**
 * Protocol-agnostic response context.
 * 
 * Base interface for ANY protocol (HTTP, CLI, gRPC, Events, etc.)
 * Provides generic metadata storage and content setting.
 * 
 * Specific protocols extend with their own methods:
 * - HttpResponseInterface for HTTP protocol
 * - Custom interfaces for other protocols
 * 
 * This design allows the Router to dispatch responses to any protocol through the base interface,
 * while still enabling protocol-specific adapters to set their specialized data.
 * 
 * Example:
 * 
 * HTTP:  HttpResponse extends ResponseContextInterface + provides setStatusCode(), setHeader()
 * CLI:   CliResponse extends ResponseContextInterface + provides setExitCode(), setOutput()
 * gRPC:  GrpcResponse extends ResponseContextInterface + provides setStatus(), setTrailers()
 * Events: EventResponse extends ResponseContextInterface + provides setResult(), setError()
 */
interface ResponseContextInterface
{
    /**
     * Set metadata for this response.
     * 
     * Metadata is flexible storage for protocol-specific response data:
     * - HTTP: Headers, cookies, status codes, other attributes
     * - CLI: Exit codes, output stream selection, color codes, etc.
     * - gRPC: Status code, status details, trailers metadata, etc.
     * - Events: Result data, error information, processing metadata, etc.
     * 
     * @param string $key The metadata key to set
     * @param mixed $value The metadata value
     * @return self Fluent interface for chaining
     */
    public function setMetadata(string $key, mixed $value): self;
    
    /**
     * Get metadata by key.
     * 
     * @param string $key The metadata key to retrieve
     * @param mixed $default Default value if key not found
     * @return mixed The metadata value, or $default if not found
     */
    public function getMetadata(string $key, mixed $default = null): mixed;
    
    /**
     * Set response body/content.
     * 
     * Meaning varies by protocol:
     * - HTTP: Response body to send back to client
     * - CLI: Output to print to console
     * - Events: Event result/response object
     * - gRPC: Response message
     * - WebSocket: Message to send
     * 
     * @param mixed $content The response body/content
     * @return self Fluent interface for chaining
     */
    public function setBody(mixed $content): self;
    
    /**
     * Get response body/content.
     * 
     * @return mixed The response body/content
     */
    public function getBody(): mixed;
    
    /**
     * Mark response as sent/completed.
     * 
     * Signal that response processing is done. Implementation varies by protocol:
     * - HTTP: Actually send HTTP headers and body to client
     * - CLI: Write output to stdout/stderr
     * - Events: Store in event result
     * - gRPC: Send RPC response
     * 
     * Should be called by the dispatcher after handler completes.
     * 
     * @return void
     */
    public function send(): void;
}
