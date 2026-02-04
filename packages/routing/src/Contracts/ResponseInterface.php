<?php

namespace Essential\Routing\Contracts;

/**
 * HTTP response interface (backward compatibility alias).
 * 
 * This interface is now an alias for HttpResponseInterface.
 * 
 * For protocol-agnostic code that should work with any protocol:
 * @see ResponseContextInterface
 * 
 * For HTTP-specific code:
 * @see HttpResponseInterface
 * 
 * MIGRATION NOTE:
 * - Old code using ResponseInterface continues to work
 * - New code should use ResponseContextInterface (for generic routing)
 * - Or HttpResponseInterface (for HTTP-specific adapters)
 */
interface ResponseInterface extends HttpResponseInterface {}