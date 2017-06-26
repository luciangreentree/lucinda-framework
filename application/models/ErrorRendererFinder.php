<?php
/**
 * Locates and instances error renderer based on XML content and information already encapsulated by Application object.
 */
class ErrorRendererFinder {
	protected $renderer;
	
	/**
	 * Reads XML tag errors.handlers.{environment}.renderer, then finds and saves renderer found.
	 *
	 * @param SimpleXMLElement $xml XML tag reference object.
	 * @param Application $application ServletsAPI object that encapsulates relevant information from configuration.xml
	 */
	public function __construct(SimpleXMLElement $xml, Application $application) {
		$extension = $this->detectExtension($application);
		$environment = $application->getAttribute("environment");
		$renderer = (!empty($xml) && !empty($xml->handlers) && !empty($xml->handlers->$environment)?$xml->handlers->$environment->renderer:null);
		if(!$renderer || !isset($renderer->$extension)) {
			return; // it is allowed to render nothing
		}
		
		$this->setRenderer($renderer->$extension, $extension, ((string) $renderer["display_errors"]?true:false), $application->getDefaultCharacterEncoding());
	}
	
	/**
	 * Detects display format (extension) based on requested page semantics and configuration.xml info encapsulated by Application object
	 * 
	 * @param Application $application ServletsAPI object that encapsulates relevant information from configuration.xml
	 * @return string Value of extension found (eg: html).
	 */
	private function detectExtension(Application $application) {
		// get extension
		$extension = $application->getDefaultExtension();
		$pathRequested = str_replace("?".$_SERVER["QUERY_STRING"],"",$_SERVER["REQUEST_URI"]);
		$dotPosition = strrpos($pathRequested,".");
		if($dotPosition!==false) {
			$temp = strtolower(substr($pathRequested,$dotPosition+1));
			if($application->hasFormat($temp)) {
				$extension = $temp;
			}
		}
		return $extension;
	}
	
	/**
	 * Finds renderer in container XML tag based on display format(extension), instances then saves it for latter reference.
	 * 
	 * @param SimpleXMLElement $xml Contents of errors.handlers.{environment}.renderer tag.
	 * @param string $extension Page display format (extension).
	 * @param boolean $displayErrors Whether or not error details should be shown on screen.
	 * @param string $characterEncoding Character encoding to be used in display (relevant for formats such as html, xml or json. 
	 */
	protected function setRenderer(SimpleXMLElement $xml, $extension, $displayErrors, $characterEncoding) {
		$rendererClassName = ucwords($extension)."Renderer";
		if(file_exists("application/models/errors/renderers/".$rendererClassName.".php")) {
			require_once("application/models/errors/renderers/".$rendererClassName.".php");
			$this->renderer = new $rendererClassName($displayErrors, $characterEncoding);
		}
	}
	
	/**
	 * Gets error renderer found.
	 * 
	 * @return ErrorRenderer
	 */
	public function getRenderer() {
		return $this->renderer;
	}
}