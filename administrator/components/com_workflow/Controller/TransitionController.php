<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_workflow
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Workflow\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * Transition controller
 *
 * @since  __DEPLOY_VERSION__
 */
class TransitionController extends FormController
{
	/**
	 * The workflow where the transition takes place
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	protected $workflowId;

	/**
	 * The extension
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $extension;

	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   \JInput              $input    Input
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \InvalidArgumentException when no extension or workflow id is set
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		// If workflow id is not set try to get it from input or throw an exception
		if (empty($this->workflowId))
		{
			$this->workflowId = $this->input->getInt('workflow_id');

			if (empty($this->workflowId))
			{
				throw new \InvalidArgumentException(Text::_('COM_WORKFLOW_ERROR_WORKFLOW_ID_NOT_SET'));
			}
		}

		// If extension is not set try to get it from input or throw an exception
		if (empty($this->extension))
		{
			$this->extension = $this->input->getCmd('extension');

			if (empty($this->extension))
			{
				throw new \InvalidArgumentException(Text::_('COM_WORKFLOW_ERROR_EXTENSION_NOT_SET'));
			}
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function allowAdd($data = array())
	{
		$user = Factory::getUser();

		return $user->authorise('core.create', $this->extension);
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = isset($data[$key]) ? (int) $data[$key] : 0;
		$user = Factory::getUser();

		// Check "edit" permission on record asset (explicit or inherited)
		if ($user->authorise('core.edit', $this->extension . '.transition.' . $recordId))
		{
			return true;
		}

		// Check "edit own" permission on record asset (explicit or inherited)
		if ($user->authorise('core.edit.own', $this->extension . '.transition.' . $recordId))
		{
			// Need to do a lookup from the model to get the owner
			$record = $this->getModel()->getItem($recordId);

			return !empty($record) && $record->created_by == $user->id;
		}

		return false;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&workflow_id=' . $this->workflowId . '&extension=' . $this->extension;

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&workflow_id=' . $this->workflowId . '&extension=' . $this->extension;

		return $append;
	}
}
