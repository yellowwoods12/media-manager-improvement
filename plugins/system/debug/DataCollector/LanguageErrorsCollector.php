<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 *
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Debug\DataCollector;

use DebugBar\DataCollector\AssetProvider;
use Joomla\CMS\Factory;
use Joomla\Plugin\System\Debug\AbstractDataCollector;

/**
 * LanguageErrorsDataCollector
 *
 * @since  __DEPLOY_VERSION__
 */
class LanguageErrorsCollector extends AbstractDataCollector implements AssetProvider
{
	/**
	 * Collector name.
	 *
	 * @var   string
	 * @since __DEPLOY_VERSION__
	 */
	private $name = 'languageErrors';

	/**
	 * The count.
	 *
	 * @var   integer
	 * @since __DEPLOY_VERSION__
	 */
	private $count = 0;

	/**
	 * Called by the DebugBar when data needs to be collected
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return array Collected data
	 */
	public function collect(): array
	{
		return [
			'data'  => [
				'files' => $this->getData(),
				'jroot' => JPATH_ROOT,
				'xdebugLink' => $this->getXdebugLinkTemplate(),
			],
			'count' => $this->getCount(),
		];
	}

	/**
	 * Returns the unique name of the collector
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Returns a hash where keys are control names and their values
	 * an array of options as defined in {@see DebugBar\JavascriptRenderer::addControl()}
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return array
	 */
	public function getWidgets(): array
	{
		return [
			'errors'       => [
				'icon' => 'warning',
				'widget'  => 'PhpDebugBar.Widgets.languageErrorsWidget',
				'map'     => $this->name . '.data',
				'default' => '',
			],
			'errors:badge' => [
				'map'     => $this->name . '.count',
				'default' => 'null',
			],
		];
	}

	/**
	 * Returns an array with the following keys:
	 *  - base_path
	 *  - base_url
	 *  - css: an array of filenames
	 *  - js: an array of filenames
	 *
	 * @since  __DEPLOY_VERSION__
	 * @return array
	 */
	public function getAssets()
	{
		return array(
			'js' => \JUri::root(true) . '/media/plg_system_debug/widgets/languageErrors/widget.min.js',
			'css' => \JUri::root(true) . '/media/plg_system_debug/widgets/languageErrors/widget.min.css',
		);
	}

	/**
	 * Collect data.
	 *
	 * @return array
	 *
	 * @since __DEPLOY_VERSION__
	 */
	private function getData(): array
	{
		$errorFiles = Factory::getLanguage()->getErrorFiles();
		$errors     = [];

		if (\count($errorFiles))
		{
			foreach ($errorFiles as $file => $lines)
			{
				foreach ($lines as $line)
				{
					$errors[] = [$file, $line];
					$this->count++;
				}
			}
		}

		return $errors;
	}

	/**
	 * Get a count value.
	 *
	 * @return int
	 *
	 * @since __DEPLOY_VERSION__
	 */
	private function getCount(): int
	{
		return $this->count;
	}
}
