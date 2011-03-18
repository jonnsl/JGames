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
JGImport('application.component.model');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_games/helpers/html');
JGModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_games/models');

class JFormFieldSeries extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Series';

	/**
	 * Method to get the field options.
	 *
	 * @return array The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();
		$series = JGModel::getInstance('Games', 'GamesModel', array('ignore_request' => true))->getSeries();

		foreach ($series as $option) {
			$options[] = JHtml::_('select.option', $option['value'], $option['text'], 'value', 'text');
		}

		reset($options);

		return array_merge(parent::getOptions(), $options);
	}
}

