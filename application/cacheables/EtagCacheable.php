<?php
/**
 * CacheableDriver that generates an ETAG based on host, response body & headers.
 */
class EtagCacheable extends \Lucinda\Framework\AbstractCacheable
{
    /**
     * {@inheritDoc}
     * @see \Lucinda\Framework\AbstractCacheable::setTime()
     */
    protected function setTime()
    {
    }
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\Framework\AbstractCacheable::setEtag()
     */
    protected function setEtag()
    {
        $this->etag = sha1($this->request->getServer()->getName()."#".$this->response->getBody());
    }
}
