<?php
/*
 * Copyright (c) 2018 Tuukka Norri
 * This code is licensed under MIT license (see LICENSE for details).
 */

namespace Grav\Plugin\TwigImageUtilsPlugin;

require_once(__DIR__ . '/Accessor.php');
require_once(__DIR__ . '/ImageProxy.php');


class TwigImageUtilsExtension extends \Twig_Extension
{
	// Generate a rule for img's sizes attribute s.t. the image will not be scaled bigger than its pixel dimensions.
	protected function maxWidthSizeRule($image)
	{
		if (is_null($image))
			return NULL;
		
		// Get the alternatives; srcset uses the same value but returns a string.
		$alternatives = Accessor::property($image, "alternatives");
		
		$maxWidth = $image->get('width');
		foreach ($alternatives as $ratio => $medium)
		{
			$width = $medium->get('width');
			if ($maxWidth < $width)
				$maxWidth = $width;
		}
		
		// Viewport minimum width.
		return sprintf("(min-width: %dpx) %dpx", $maxWidth, $maxWidth);
	}
	
	
	// Return the medium the size of which on the given axis is at most maxSize.
	protected function mediumWithMaximumSize($image, $alternatives, $maxSize, $axis)
	{
		// Check the requested dimension.
		$sizeKey = 'width';
		if (1 == $axis)
			$sizeKey = 'height';
		
		// Find the default derivative.
		$foundSize = $image->get($sizeKey);
		$foundMedium = $image;
		foreach ($alternatives as $ratio => $medium)
		{
			$size = $medium->get($sizeKey);
			if (($foundSize < $size && $size <= $maxSize) || ($maxSize < $foundSize && $size < $foundSize))
			{
				$foundSize = $size;
				$foundMedium = $medium;
			}
		}
		
		return $foundMedium;
	}
	
	
	public function getName()
	{
		return 'TwigImageUtilsExtension';
	}
	
	
	public function getFilters()
	{
		return [
			// Return a rule for the sizes attribute that restricts the width of the image to its maximum size.
			'max_width_size_rule' => new \Twig_SimpleFilter('max_width_size_rule', function ($image) {
				
				return $this->maxWidthSizeRule($image);
			}),
			
			
			// Return an image proxy with the given minimum size as the default derivative.
			'image_with_default' => new \Twig_SimpleFilter('image_with_default', function ($image, $maxSize, $axis) {
				
				if (is_null($image))
					return NULL;
				
				// Get the alternatives; srcset uses the same value but returns a string.
				$alternatives = Accessor::property($image, "alternatives");
				
				$defaultMedium = $this->mediumWithMaximumSize($image, $alternatives, $maxSize, $axis);
				
				// Maximum size.
				$mwsr = $this->maxWidthSizeRule($image);
				
				return new ImageProxy($image, $defaultMedium, $mwsr);
			}),
			
			
			// Take a Grav Image and return an array with two elements: a “primary” image
			// (either a resized image with the given minimum size or the given image)
			// and derivatives for use with e.g. srcset, that is, [primary_medium, [[width_1, height_1, medium_1], …]].
			'image_alternatives' => new \Twig_SimpleFilter('image_alternatives', function ($image, $maxSize, $axis) {
				if (is_null($image))
					return NULL;
				
				// Get the alternatives; srcset uses the same value but returns a string.
				$alternatives = Accessor::property($image, "alternatives");
				
				// Find the primary medium.
				$defaultMedium = $this->mediumWithMaximumSize($image, $alternatives, $maxSize, $axis);
				
				// Return the given image as the primary.
				if (is_null($defaultMedium))
					return [$image, []];
				
				// Format the alternatives array.
				$formattedAlts = [];
				foreach ($alternatives as $ratio => $medium)
					$formattedAlts[] = [$medium->get('width'), $medium->get('height'), $medium];
				usort($formattedAlts, function($a, $b) { if ($a[0] == $b[0]) return 0; else return ($a[0] < $b[0] ? -1 : 1); });
				
				return [$defaultMedium, $formattedAlts];
			})
		];
	}
}

?>
