<?php

namespace Essential\Routing\Contracts;

/**
 * HTTP request interface (backward compatibility alias).
 * 
 * This interface is now an alias for HttpRequestInterface.
 * 
 * For protocol-agnostic code that should work with any protocol:
 * @see RequestContextInterface
 * 
 * For HTTP-specific code:
 * @see HttpRequestInterface
 * 
 * MIGRATION NOTE:
 * - Old code using RequestInterface continues to work
 * - New code should use RequestContextInterface (for generic routing)
 * - Or HttpRequestInterface (for HTTP-specific adapters)
 */
interface RequestInterface extends HttpRequestInterface {}