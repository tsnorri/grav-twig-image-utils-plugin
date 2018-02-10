<?php
/*
 * Copyright (c) 2018 Tuukka Norri
 * This code is licensed under MIT license (see LICENSE for details).
 */

namespace Grav\Plugin\TwigImageUtilsPlugin;


class Accessor
{
	public function getProperty($obj, $name)
	{
		// Check that the given property exists.
		if (!property_exists($obj, $name))
			throw new \Exception("Property '" . $name . "' does not exist.");
		
		// Create a member function in order to bypass access specifiers.
		// Try to get a reference to the value.
		$closure = function & () use($name) {
			return $this->{$name};
		};
		
		// Bind the function to the object and call it.
		$bound = \Closure::bind($closure, $obj, get_class($obj));
		return $bound();
	}
	
	public static function property($obj, $name)
	{
		$acc = new Accessor;
		return $acc->getProperty($obj, $name);
	}
}

?>
