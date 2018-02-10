<?php
/*
 * Copyright (c) 2017 Tuukka Norri
 * This code is licensed under MIT license (see LICENSE for details).
 */

namespace Grav\Plugin;

require_once(__DIR__ . '/classes/TwigImageUtilsExtension.php');

use Grav\Common\Plugin;

/**
 * Class TwigImageUtilsPlugin
 * @package Grav\Plugin
 */
class TwigImageUtilsPlugin extends Plugin
{
	public static function getSubscribedEvents()
	{
		return [
			'onPluginsInitialized' => ['onPluginsInitialized', 0]
		];
	}

	/**
	 * Initialize the plugin
	 */
	public function onPluginsInitialized()
	{
		// Don't proceed if we are in the admin plugin
		if ($this->isAdmin()) {
			return;
		}
		
		$this->enable([
			'onTwigExtensions' => ['onTwigExtensions', 0]
		]);
	}

	public function onTwigExtensions()
	{
		$this->grav['twig']->twig->addExtension(new \Grav\Plugin\TwigImageUtilsPlugin\TwigImageUtilsExtension());
	}
}

?>
