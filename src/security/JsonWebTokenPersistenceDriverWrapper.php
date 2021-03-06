<?php
require_once("PersistenceDriverWrapper.php");

/**
 * Binds JsonWebTokenPersistenceDriver @ SECURITY API with settings from configuration.xml @ SERVLETS-API and sets up an object on which one can
 * forward json web token operations.
 */
class JsonWebTokenPersistenceDriverWrapper extends PersistenceDriverWrapper {
	const DEFAULT_EXPIRATION_TIME = 3600;
	const DEFAULT_REGENERATION_TIME = 60;
	
	/**
	 * {@inheritDoc}
	 * @see PersistenceDriverWrapper::setDriver()
	 */
	protected function setDriver(SimpleXMLElement $xml) {
		$secret = (string) $xml["secret"];
		if(!$secret) throw new ApplicationException("'secret' key of security.persistence.json_web_token tag is mandatory!");
		
		$expirationTime = (integer) $xml["expiration"];
		if(!$expirationTime) $expirationTime = self::DEFAULT_EXPIRATION_TIME;
		
		$regenerationTime = (integer) $xml["regeneration"];
		if(!$regenerationTime) $regenerationTime = self::DEFAULT_REGENERATION_TIME;
		
		$this->driver = new JsonWebTokenPersistenceDriver($secret, $expirationTime, $regenerationTime);
	}
}