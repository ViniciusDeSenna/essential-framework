# Análise: Request/Response Abstractions - Desacoplamento de HTTP

## Problema Identificado

### Localização
- [src/Contracts/RequestInterface.php](src/Contracts/RequestInterface.php)
- [src/Contracts/ResponseInterface.php](src/Contracts/ResponseInterface.php)
- Usados por: `Router`, `MiddlewareInterface`, `RouterAdapterInterface`

### Descrição

As interfaces Request e Response contêm métodos **específicos de HTTP**, tornando impossível estender o framework para outros protocolos:

```php
// RequestInterface - FORTEMENTE HTTP-CENTRIC
interface RequestInterface
{
    public function getMethod(): string;        // ← HTTP concept
    public function getUri(): string;           // ← HTTP concept
    public function getPath(): string;          // ← HTTP concept
    public function getHeaders(): array;        // ← HTTP concept
    public function getHeader(string $name): ?string;  // ← HTTP concept
    public function getQueryParams(): array;    // ← HTTP concept
    public function getBody(): mixed;
}

// ResponseInterface - FORTEMENTE HTTP-CENTRIC
interface ResponseInterface
{
    public function setStatusCode(int $code): self;    // ← HTTP concept
    public function getStatusCode(): int;              // ← HTTP concept
    public function setHeader(string $name, string $value): self;  // ← HTTP concept
    public function setHeaders(array $headers): self;   // ← HTTP concept
    public function getHeaders(): array;                 // ← HTTP concept
    public function setBody(mixed $content): self;
    public function getBody(): mixed;
    public function sent(): void;
}
```

### Cenários Bloqueados

| Protocolo | Problema | Bloqueado? |
|-----------|----------|-----------|
| **HTTP** | Uses `getMethod()`, `getHeaders()`, `setStatusCode()` | ✅ Funciona |
| **CLI** | Não tem "método HTTP", "path", "query params", "status code" | ❌ Impossível |
| **gRPC** | Usa status codes diferentes de HTTP, metadata instead of headers | ❌ Impossível |
| **WebSocket** | Não tem "método", "path", "status code" tradicional | ❌ Impossível |
| **Event Router** | Evento é apenas um objeto, sem headers/status | ❌ Impossível |
| **Webhook** | Estrutura customizada, não segue HTTP | ❌ Impossível |

### Por Que É Problemático

**Cenário 1: CLI Router**
```php
// CLI é diferente de HTTP
class CliRequest {
    public function getCommand(): string;      // Não há "method"
    public function getArguments(): array;     // Não há "query params"
}

class CliResponse {
    public function setExitCode(int $code): self;  // Não há "status code HTTP"
}

// Mas CliAdapter precisa usar RouterInterface
// Que força RequestInterface | ResponseInterface
// Que só falam HTTP!

// Solução atual: Adaptar conceitos
// $request->getMethod() → "execute"?  ❌ Confuso
// $response->setStatusCode(0) → sucesso? ❌ Semanticamente errado
```

**Cenário 2: Event Router**
```php
class EventRequest {
    private object $event;
    
    public function getEvent(): object { return $this->event; }
}

// EventAdapterimplementa RouterAdapterInterface
// Mas espera RequestInterface... que não tem getEvent()
// Precisa fazer type casting ou violação de interface
```

**Cenário 3: Future Protocol**
```php
// GraphQL Subscriptions
class SubscriptionRequest {
    public function getSubscriptionId(): string;
    public function getPayload(): array;
}

// Não tem "method", "path", "headers" de HTTP
// Mas precisa passar por Router que espera RequestInterface
```

---

## Raiz do Problema

### Acoplamento Arquitetural

```
Current Structure (HTTP-FIRST):
┌───────────────────────────────────┐
│   RouterInterface                 │
│   + match(RequestInterface)        │ ← Força HTTP
│   + dispatch(RequestInterface)     │ ← Força HTTP
└───────────────────────────────────┘
           ↓
┌───────────────────────────────────┐
│   RequestInterface (HTTP-centric) │
│   - getMethod()                   │
│   - getHeaders()                  │
│   - getQueryParams()              │
└───────────────────────────────────┘

Problem: Router depends on HTTP-specific abstraction
```

### O Framework Nega Sua Própria Extensibilidade

```php
// Router.php says:
"I match routes and dispatch to handlers"

// But RequestInterface says:
"I'm HTTP request with methods, headers, status codes"

// These don't match!
// Router should work with ANY request
// Not just HTTP requests
```

---

## Solução Recomendada: Estratificação de Abstrações

### Abordagem: 3 Camadas de Interfaces

```
Layer 1: GENÉRICA (Core Routing)
┌─────────────────────────────────────┐
│ RequestContextInterface             │
│ - getMetadata(string $key): mixed   │
│ - getAttribute(string $name): mixed │
└─────────────────────────────────────┘

Layer 2: PROTOCOL-SPECIFIC (HTTP)
┌─────────────────────────────────────┐
│ HttpRequestInterface                │
│ extends RequestContextInterface     │
│ + getMethod(): string               │
│ + getHeaders(): array               │
│ + getPath(): string                 │
└─────────────────────────────────────┘

Layer 3: CUSTOM (Adapter Specific)
┌─────────────────────────────────────┐
│ CliTokenInterface                   │
│ + getCommand(): string              │
│ + getArguments(): array             │
└─────────────────────────────────────┘
```

### Passo 1: Criar Interfaces Base Genéricas

```php
// NEW: RequestContextInterface (Protocol-agnostic)
interface RequestContextInterface
{
    /**
     * Get metadata - flexible key/value storage
     * Adapters can store anything relevant to their protocol
     */
    public function getMetadata(string $key, mixed $default = null): mixed;
    
    /**
     * Get all metadata
     */
    public function getAllMetadata(): array;
    
    /**
     * Get body/content - works for all protocols
     */
    public function getBody(): mixed;
}

// NEW: ResponseContextInterface (Protocol-agnostic)
interface ResponseContextInterface
{
    /**
     * Set metadata - flexible key/value storage
     * Adapters can set anything relevant to their protocol
     */
    public function setMetadata(string $key, mixed $value): self;
    
    /**
     * Get metadata
     */
    public function getMetadata(string $key, mixed $default = null): mixed;
    
    /**
     * Return content - works for all protocols
     */
    public function setBody(mixed $content): self;
    public function getBody(): mixed;
    
    /**
     * Mark as sent/completed
     */
    public function send(): void;
}
```

### Passo 2: Estender com HTTP-Específico

```php
// UPDATED: HttpRequestInterface extends base
interface HttpRequestInterface extends RequestContextInterface
{
    // HTTP-specific methods
    public function getMethod(): string;
    public function getUri(): string;
    public function getPath(): string;
    public function getHeaders(): array;
    public function getHeader(string $name): ?string;
    public function getQueryParams(): array;
    
    // Inherited from RequestContextInterface
    // public function getBody(): mixed;
    // public function getMetadata(string $key): mixed;
}

// UPDATED: HttpResponseInterface extends base
interface HttpResponseInterface extends ResponseContextInterface
{
    // HTTP-specific methods
    public function setStatusCode(int $code): self;
    public function getStatusCode(): int;
    public function setHeader(string $name, string $value): self;
    public function setHeaders(array $headers): self;
    public function getHeaders(): array;
    
    // Inherited from ResponseContextInterface
    // public function setBody(mixed $content): self;
    // public function getBody(): mixed;
    // public function setMetadata(string $key, mixed $value): self;
}
```

### Passo 3: Router Usa Abstração Base

```php
// UPDATED: RouterInterface
interface RouterInterface
{
    // Uses BASE context, not HTTP-specific
    public function match(RequestContextInterface $request): RouteMatchInterface;
    public function dispatch(RequestContextInterface $request): ResponseContextInterface;
    
    // ... other methods
}

// UPDATED: Router implementation
class Router implements RouterInterface
{
    public function match(RequestContextInterface $request): RouteMatchInterface
    {
        // Works with ANY protocol
        // HTTP adapter provides HttpRequestInterface -> also RequestContextInterface ✅
        // CLI adapter provides CliRequest -> also RequestContextInterface ✅
        
        return $this->adapter->match($request);
    }
    
    public function dispatch(RequestContextInterface $request): ResponseContextInterface
    {
        // Works with ANY protocol
        return $this->createResponse($matchedRoute);
    }
}
```

### Passo 4: MiddlewareInterface Usa Base

```php
// UPDATED: MiddlewareInterface
interface MiddlewareInterface
{
    // Uses BASE context, not HTTP-specific
    public function process(RequestContextInterface $request, callable $next): ResponseContextInterface;
}
```

---

## Implementação Concreta

### Novo Arquivo: RequestContextInterface

```php
<?php

namespace Essential\Routing\Contracts;

/**
 * Protocol-agnostic request context.
 * 
 * Base interface for ANY protocol (HTTP, CLI, gRPC, Events, etc.)
 * Provides generic metadata storage and body access.
 * 
 * Specific protocols extend with their own methods.
 */
interface RequestContextInterface
{
    /**
     * Get metadata by key.
     * 
     * Metadata is flexible storage for protocol-specific data:
     * - HTTP: May store cookies, user, etc.
     * - CLI: May store environment variables, flags, etc.
     * - gRPC: May store service metadata, etc.
     */
    public function getMetadata(string $key, mixed $default = null): mixed;
    
    /**
     * Get all metadata.
     */
    public function getAllMetadata(): array;
    
    /**
     * Get body/content.
     * 
     * Meaning varies by protocol:
     * - HTTP: Request body (POST data, JSON, etc.)
     * - CLI: Command input
     * - Events: Event object
     * - gRPC: Request message
     */
    public function getBody(): mixed;
}
```

### Novo Arquivo: ResponseContextInterface

```php
<?php

namespace Essential\Routing\Contracts;

/**
 * Protocol-agnostic response context.
 * 
 * Base interface for ANY protocol (HTTP, CLI, gRPC, Events, etc.)
 * Provides generic metadata storage and content setting.
 * 
 * Specific protocols extend with their own methods.
 */
interface ResponseContextInterface
{
    /**
     * Set metadata.
     * 
     * Flexible storage for protocol-specific response data:
     * - HTTP: Headers, cookies, status
     * - CLI: Exit codes, output streams
     * - gRPC: Status, metadata trailers
     */
    public function setMetadata(string $key, mixed $value): self;
    
    /**
     * Get metadata.
     */
    public function getMetadata(string $key, mixed $default = null): mixed;
    
    /**
     * Set response body/content.
     * 
     * Meaning varies by protocol:
     * - HTTP: Response body to send back
     * - CLI: Output to print
     * - Events: Event result
     * - gRPC: Response message
     */
    public function setBody(mixed $content): self;
    
    /**
     * Get response body.
     */
    public function getBody(): mixed;
    
    /**
     * Mark response as sent/completed.
     * 
     * Signal that response processing is done.
     * Implementation varies by protocol.
     */
    public function send(): void;
}
```

### Atualizar RequestInterface

```php
<?php

namespace Essential\Routing\Contracts;

/**
 * HTTP-specific request interface.
 * 
 * Extends protocol-agnostic RequestContextInterface with HTTP methods.
 * Use RequestContextInterface for protocol-agnostic routing.
 * Use HttpRequestInterface for HTTP-specific adapters.
 */
interface HttpRequestInterface extends RequestContextInterface
{
    public function getMethod(): string;
    public function getUri(): string;
    public function getPath(): string;
    public function getHeaders(): array;
    public function getHeader(string $name): ?string;
    public function getQueryParams(): array;
    // getBody() inherited from RequestContextInterface
}

// Keep RequestInterface as alias for backward compatibility
// OR deprecate in favor of HttpRequestInterface
interface RequestInterface extends HttpRequestInterface {}
```

### Atualizar ResponseInterface

```php
<?php

namespace Essential\Routing\Contracts;

/**
 * HTTP-specific response interface.
 * 
 * Extends protocol-agnostic ResponseContextInterface with HTTP methods.
 * Use ResponseContextInterface for protocol-agnostic routing.
 * Use HttpResponseInterface for HTTP-specific adapters.
 */
interface HttpResponseInterface extends ResponseContextInterface
{
    public function setStatusCode(int $code): self;
    public function getStatusCode(): int;
    public function setHeader(string $name, string $value): self;
    public function setHeaders(array $headers): self;
    public function getHeaders(): array;
    // setBody(), getBody(), setMetadata(), send() inherited from ResponseContextInterface
}

// Keep ResponseInterface as alias for backward compatibility
// OR deprecate in favor of HttpResponseInterface
interface ResponseInterface extends HttpResponseInterface {}
```

### Atualizar RouterInterface

```php
<?php

namespace Essential\Routing\Contracts;

interface RouterInterface
{
    // Use base context interfaces, not HTTP-specific
    public function match(RequestContextInterface $request): RouteMatchInterface;
    public function dispatch(RequestContextInterface $request): ResponseContextInterface;
    
    // ... other methods remain the same (they're protocol-agnostic)
    public function get(string $path, mixed $handler): RouteInterface;
    public function post(string $path, mixed $handler): RouteInterface;
    public function put(string $path, mixed $handler): RouteInterface;
    public function patch(string $path, mixed $handler): RouteInterface;
    public function delete(string $path, mixed $handler): RouteInterface;
    public function options(string $path, mixed $handler): RouteInterface;
    public function addRoute(string|array $methods, string $path, mixed $handler): RouteInterface;
    
    public function resource(string $path, string $controller): void;
    public function group(callable $callback): RouteGroupInterface;
    public function url(string $name, array $params = []): string;
    public function getRoutes(): RouteCollectionInterface;
}
```

### Atualizar MiddlewareInterface

```php
<?php

namespace Essential\Routing\Contracts;

/**
 * Protocol-agnostic middleware interface.
 * 
 * Works with any protocol by using base RequestContextInterface
 * and ResponseContextInterface instead of HTTP-specific versions.
 */
interface MiddlewareInterface
{
    // Use base context interfaces
    public function process(RequestContextInterface $request, callable $next): ResponseContextInterface;
}
```

### Atualizar Router

Mudar qualquer referência de `RequestInterface` para `RequestContextInterface` e `ResponseInterface` para `ResponseContextInterface` onde o código não seja HTTP-específico.

```php
// BEFORE
class Router implements RouterInterface
{
    public function match(RequestInterface $request): RouteMatchInterface
    {
        return $this->adapter->match($request);
    }
    
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        // ...
    }
}

// AFTER
class Router implements RouterInterface
{
    public function match(RequestContextInterface $request): RouteMatchInterface
    {
        return $this->adapter->match($request);
    }
    
    public function dispatch(RequestContextInterface $request): ResponseContextInterface
    {
        // ...
    }
}
```

---

## Impacto nos Adapters

### HTTP Adapter

```php
class HttpAdapter implements RouterAdapterInterface
{
    public function match(RequestContextInterface $request): RouteMatchInterface
    {
        // Cast to HTTP-specific type if needed (it IS HttpRequest under the hood)
        if ($request instanceof HttpRequestInterface) {
            $method = $request->getMethod();
            $path = $request->getPath();
        }
        
        // Or use metadata:
        $method = $request->getMetadata('http.method');
        $path = $request->getMetadata('http.path');
    }
}
```

### CLI Adapter (NEW!)

```php
class CliAdapter implements RouterAdapterInterface
{
    public function match(RequestContextInterface $request): RouteMatchInterface
    {
        // Works with CliRequest that extends RequestContextInterface
        $command = $request->getMetadata('cli.command');
        $args = $request->getMetadata('cli.arguments');
        
        // Match command to routes registered as 'COMMAND command-name'
        // No need to force HTTP concepts!
    }
}
```

### Event Adapter (NEW!)

```php
class EventAdapter implements RouterAdapterInterface
{
    public function match(RequestContextInterface $request): RouteMatchInterface
    {
        // Works with EventRequest that extends RequestContextInterface
        $event = $request->getBody();
        $eventName = get_class($event);
        
        // Match event type to routes
        // No need to force HTTP concepts!
    }
}
```

---

## Backward Compatibility Strategy

### Strategy A: Keep RequestInterface / ResponseInterface as Aliases

```php
// RequestInterface remains for backward compatibility
interface RequestInterface extends HttpRequestInterface {}
interface ResponseInterface extends HttpResponseInterface {}

// Code using RequestInterface still works ✅
```

**Pros:**
- ✅ 100% backward compatible
- ✅ Old code never breaks
- ✅ New code can use base contexts

**Cons:**
- ❌ Creates alias confusion
- ❌ Doesn't encourage migration

### Strategy B: Update RouterInterface Gradually

```php
// Phase 1: Both versions exist
interface RouterInterface
{
    // Old signature (HTTP-specific)
    public function dispatch(RequestInterface $request): ResponseInterface;
    
    // @deprecated Use dispatchContext() instead
    // public function dispatch(RequestInterface $request): ResponseInterface;
}

// New adapters extend abstract base:
abstract class BaseRouter implements RouterInterface
{
    // Implements dispatch() as wrapper around dispatchContext()
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $response = $this->dispatchContext(
            HttpRequest::wrap($request)
        );
        return HttpResponse::wrap($response);
    }
    
    protected abstract function dispatchContext(
        RequestContextInterface $request
    ): ResponseContextInterface;
}

// Phase 2: After deprecation period, remove old signature
```

---

## Benefícios Desbloqueados

| Benefício | Antes | Depois |
|-----------|-------|--------|
| suportar CLI routing | ❌ Impossível | ✅ Possível |
| Suportar gRPC | ❌ Impossível | ✅ Possível |
| Suportar Event routing | ❌ Impossível | ✅ Possível |
| Suportar Custom protocols | ❌ Impossível | ✅ Possível |
| HTTP remain default | ✅ Sim | ✅ Sim |
| Backward compatible | N/A | ✅ Sim |
| Middleware multiprotocolo | ❌ Impossível | ✅ Possível |
| Type safety | ✅ Sim | ✅ Sim (melhor) |

---

## Comparação: Antes vs. Depois

```
ANTES (HTTP-first with no flexibility):

User tries to make CLI router:
    ├─ Needs to implement RequestInterface
    ├─ RequestInterface has getMethod(), getHeaders()
    ├─ CLI doesn't have these concepts
    ├─ Forced to create "fake" implementations
    ├─ Semantics are confusing
    └─ Middleware doesn't work right → ❌ FAIL

DEPOIS (Protocol-agnostic with HTTP as default):

User makes CLI router:
    ├─ Implements RequestContextInterface
    ├─ RequestContextInterface is flexible (metadata)
    ├─ CLI can store command, args in metadata
    ├─ HttpRequestInterface extends RequestContextInterface
    ├─ HTTP router still works perfectly
    ├─ Middleware works with base context
    └─ Everything is clean → ✅ SUCCESS
```

---

## Recommended Implementation Order

### Phase 1: Foundation (No breaking changes)
1. [x] Create `RequestContextInterface` - new file
2. [x] Create `ResponseContextInterface` - new file
3. [x] Create `HttpRequestInterface extends RequestContextInterface`
4. [x] Create `HttpResponseInterface extends ResponseContextInterface`
5. [x] Keep `RequestInterface` and `ResponseInterface` as backward-compatible aliases

### Phase 2: Core Update (Minimal breaking changes)
6. [ ] Update `RouterInterface` to use base context interfaces
7. [ ] Update `Router.php` implementation
8. [ ] Update `MiddlewareInterface`
9. [ ] Update `FastRouteAdapter` if needed

### Phase 3: New Adapters (Showcase flexibility)
10. [ ] Create example `CliAdapter` implementing RouterAdapterInterface
11. [ ] Create example `EventAdapter` implementing RouterAdapterInterface
12. [ ] Document in examples

### Phase 4: Deprecation (Gradual migration)
13. [ ] Mark HTTP-specific methods with `@see` pointing to base
14. [ ] Document migration guide
15. [ ] Plan removal in next major version

---

## Files to Create/Modify

| File | Action | Impact |
|------|--------|--------|
| `src/Contracts/RequestContextInterface.php` | Create | New base interface |
| `src/Contracts/ResponseContextInterface.php` | Create | New base interface |
| `src/Contracts/HttpRequestInterface.php` | Create | Rename from RequestInterface |
| `src/Contracts/HttpResponseInterface.php` | Create | Rename from ResponseInterface |
| `src/Contracts/RequestInterface.php` | Update | Alias for backward compat |
| `src/Contracts/ResponseInterface.php` | Update | Alias for backward compat |
| `src/Contracts/RouterInterface.php` | Update | Use base context |
| `src/Contracts/MiddlewareInterface.php` | Update | Use base context |
| `src/Router.php` | Update | Use base context |
| `src/Adapters/FastRouteAdapter.php` | Update | Type hints |

---

## Key Principle Applied

**Protocol-agnostic contracts. Protocol-specific implementations.**

```
RequestContextInterface      ← What Router needs (generic)
    ↑
    ├── HttpRequestInterface ← What HTTP adapters provide (specific)
    ├── CliRequest          ← What CLI adapters provide (specific)
    └── EventRequest        ← What Event adapters provide (specific)
```

This way:
- Router works with ANY protocol (via base interface)
- HTTP remains default and convenient (via HttpRequestInterface)
- Other protocols become possible (via implementation)
- Everything is properly abstracted and typed
