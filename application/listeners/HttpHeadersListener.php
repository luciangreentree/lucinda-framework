<?php
/**
 * Sets up HTTP Headers API for later cache/cors validation or request/response headers operations
 */
class HttpHeadersListener extends \Lucinda\STDOUT\EventListeners\Request
{
    /**
     * @var \Lucinda\Framework\Attributes
     */
    protected $attributes;
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\STDOUT\Runnable::run()
     */
    public function run(): void
    {
        $wrapper = new Lucinda\Headers\Wrapper($this->application->getTag("xml"), $this->attributes->getValidPage(), $this->request->headers());
        $this->attributes->setHeaders($wrapper);
    }
}
