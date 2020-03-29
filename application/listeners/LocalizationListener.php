<?php
require("application/models/Translate.php");

/**
 * Sets up Internationalization API to use in automatic translation of response
 */
class LocalizationListener extends \Lucinda\STDOUT\EventListeners\Request
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
        $wrapper = new Lucinda\Internationalization\Wrapper($this->application->getTag("xml"), $this->request->parameters(), $this->request->headers());
        \Lucinda\Framework\SingletonRepository::set("translations", $wrapper->getReader());
    }
}
