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
	protected $dimensions = null;
	protected $parsedown = null;
	protected $useDataAttributes = false;
	
	
	function __construct($image, $defaultMedium, $sizesRules, $dimensions, $useDataAttributes)
	{
		$this->image = $image;
		$this->defaultMedium = $defaultMedium;
		$this->sizesRules = $sizesRules;
		$this->dimensions = $dimensions;
		$this->useDataAttributes = $useDataAttributes;
	}
	
	
	// Catch all other method calls. (May not be used at all.)
	/*
	public function __call($name, $args)
	{
		return call_user_func_array(array(&$this->image, $name), $args);
	}
	*/
	
	
	// Replace src, srcset and sizes with data attributes in order
	// to update the default size in JavaScript.
	protected function updateAttributes(&$el)
	{
		if (!array_key_exists('attributes', $el))
			$el['attributes'] = [];
		
		if ($this->useDataAttributes)
		{
			$el['attributes']['data-src'] = $this->defaultMedium->url(false);
			if (array_key_exists('srcset', $el['attributes']))
				$el['attributes']['data-srcset'] = $el['attributes']['srcset'];
		}
		else
		{
			$el['attributes']['src'] = $this->defaultMedium->url(false);
		}
		
		if ($this->sizesRules)
		{
			$attrName = "sizes";
			if ($this->useDataAttributes)
				$attrName = "data-sizes";
			
			if (array_key_exists('sizes', $el['attributes']))
				$el['attributes'][$attrName] = $this->sizesRules . ", " . $el['attributes']['sizes'];
			else
				$el['attributes'][$attrName] = $this->sizesRules;
		}

		if ($this->dimensions)
		{
			$el['attributes']['data-width'] = $this->dimensions[0];
			$el['attributes']['data-height'] = $this->dimensions[1];
		}
		
		if ($this->useDataAttributes)
		{
			unset($el['attributes']['src']);
			unset($el['attributes']['srcset']);
			unset($el['attributes']['sizes']);
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
