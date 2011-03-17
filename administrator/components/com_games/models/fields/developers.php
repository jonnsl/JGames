<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

require JPATH_ADMINISTRATOR.'/components/com_games/loader.php';
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.application.component.model');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_games/helpers/html');
JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_games/models');


class JFormFieldDevelopers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Developers';

	/**
	 * Method to get the field options.
	 *
	 * @return array The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();
		$developers = JModel::getInstance('Games', 'GamesModel', array('ignore_request' => true))->getDevelopers();

		foreach ($developers as $option) {
			$options[] = JHtml::_('select.option', $option['value'], $option['text'], 'value', 'text');
		}

		reset($options);

		return array_merge(parent::getOptions(), $options);
	}
}

