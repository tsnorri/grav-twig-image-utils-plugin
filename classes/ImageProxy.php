<?php
/*
 * Copyright (c) 2018 Tuukka Norri
 * This code is licensed under MIT license (see LICENSE for details).
 */

namespace Grav\Plugin\TwigImageUtilsPlugin;


class ImageProxy
{
	protected $image = null;
	protected $defaultMedium = null;
	protected $sizesRules = null;
	protected $parsedown = null;
	
	
	function __construct($image, $defaultMedium, $sizesRules)
	{
		$this->image = $image;
		$this->defaultMedium = $defaultMedium;
		$this->sizesRules = $sizesRules;
	}
	
	
	// Catch all other method calls. (May not be used at all.)
	/*
	public function __call($name, $args)
	{
		return call_user_func_array(array(&$this->image, $name), $args);
	}
	*/
	
	
	// Update src and sizes.
	protected function updateAttributes(&$el)
	{
		$el['attributes']['src'] = $this->defaultMedium->url(false);
		
		if ($this->sizesRules)
		{
			if ($el['attributes']['sizes'])
				$el['attributes']['sizes'] = $this->sizesRules . ", " . $el['attributes']['sizes'];
			else
				$el['attributes']['sizes'] = $this->sizesRules;
		}
	}
	
	
	// Generate HTML.
	public function html($title = null, $alt = null, $class = null, $id = null, $reset = true)
	{
		// Use the default image's parsedownElement() to get the element. If the mode of the image
		// is 'source', add sizes and change the medium.
		$element = $this->image->parsedownElement($title, $alt, $class, $id, $reset);
		$mode = Accessor::property($this->image, 'mode');
		if ('source' == $mode)
			$this->updateAttributes($element);
		
		if (!$this->parsedown)
			$this->parsedown = new \Grav\Common\Markdown\Parsedown(null, null);
		
		return $this->parsedown->elementToHtml($element);
	}
	
	
	// Parsedown element in source mode. (May not be used at all.)
	public function sourceParsedownElement(array $attributes, $reset = true)
	{
		$retval = $this->image->sourceParsedownElement($attributes, $reset);
		$this->updateAttributes($retval);
		
		return $retval;
	}
}

?>
