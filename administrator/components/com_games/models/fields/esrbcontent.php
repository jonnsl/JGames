<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('groupedlist');

class JFormFieldEsrbcontent extends JFormFieldGroupedList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'Esrbcontent';

	/**
	 * Method to get the field option groups.
	 *
	 * @return array The field option objects as a nested array in groups.
	 */
	protected function getGroups()
	{
		// Initialize variables.
		$groups = array();

		$options = array(
			'COM_GAMES_ESRB_CONTENT_SUBSTANCE' => array(
				'COM_GAMES_ESRB_CONTENT_ALCOHOL_AND_TOBACCO_REFERENCE' => 1,
				'COM_GAMES_ESRB_CONTENT_ALCOHOL_REFERENCE' => 2,
				'COM_GAMES_ESRB_CONTENT_TOBACCO_REFERENCE' => 3,
				'COM_GAMES_ESRB_CONTENT_USE_OF_ALCOHOL' => 4,
				'COM_GAMES_ESRB_CONTENT_USE_OF_ALCOHOL_AND_TOBACCO' => 5,
				'COM_GAMES_ESRB_CONTENT_USE_OF_DRUGS' => 6,
				'COM_GAMES_ESRB_CONTENT_USE_OF_DRUGS_AND_ALCOHOL' => 7,
				'COM_GAMES_ESRB_CONTENT_USE_OF_TOBACCO' => 8,
				'COM_GAMES_ESRB_CONTENT_DRUG_AND_ALCOHOL_REFERENCE' => 9,
				'COM_GAMES_ESRB_CONTENT_DRUG_REFERENCE' => 10),
			'COM_GAMES_ESRB_CONTENT_VIOLENCE' => array(
				'COM_GAMES_ESRB_CONTENT_ANIMATED_BLOOD' => 11,
				'COM_GAMES_ESRB_CONTENT_VIOLENCE' => 12,
				'COM_GAMES_ESRB_CONTENT_VIOLENT_REFERENCES' => 13,
				'COM_GAMES_ESRB_CONTENT_FANTASY_VIOLENCE' => 14,
				'COM_GAMES_ESRB_CONTENT_INTENSE_VIOLENCE' => 15,
				'COM_GAMES_ESRB_CONTENT_MILD_VIOLENCE' => 16,
				'COM_GAMES_ESRB_CONTENT_SEXUAL_VIOLENCE' => 17,
				'COM_GAMES_ESRB_CONTENT_MILD_BLOOD' => 18,
				'COM_GAMES_ESRB_CONTENT_MILD_CARTOON_VIOLENCE' => 19,
				'COM_GAMES_ESRB_CONTENT_MILD_FANTASY_VIOLENCE' => 20),
			'COM_GAMES_ESRB_CONTENT_BLOOD' => array(
				'COM_GAMES_ESRB_CONTENT_BLOOD_AND_GORE' => 21,
				'COM_GAMES_ESRB_CONTENT_CARTOON_VIOLENCE' => 22),
			'COM_GAMES_ESRB_CONTENT_HUMOR' => array(
				'COM_GAMES_ESRB_CONTENT_COMIC_MISCHIEF' => 23,
				'COM_GAMES_ESRB_CONTENT_CRUDE_HUMOR' => 24,
				'COM_GAMES_ESRB_CONTENT_MATURE_HUMOR' => 25),
			'COM_GAMES_ESRB_CONTENT_LANGUAGE_AND_LYRICS' => array(
				'COM_GAMES_ESRB_CONTENT_LANGUAGE' => 26,
				'COM_GAMES_ESRB_CONTENT_LYRICS' => 27,
				'COM_GAMES_ESRB_CONTENT_MILD_LANGUAGE' => 28,
				'COM_GAMES_ESRB_CONTENT_MILD_LYRICS' => 29,
				'COM_GAMES_ESRB_CONTENT_STRONG_LANGUAGE' => 30,
				'COM_GAMES_ESRB_CONTENT_STRONG_LYRICS' => 31),
			'COM_GAMES_ESRB_CONTENT_SEXUALITY' => array(
				'COM_GAMES_ESRB_CONTENT_MILD_SEXUAL_CONTENT' => 32,
				'COM_GAMES_ESRB_CONTENT_MILD_SEXUAL_THEMES' => 33,
				'COM_GAMES_ESRB_CONTENT_MILD_SUGGESTIVE_THEMES' => 34,
				'COM_GAMES_ESRB_CONTENT_NUDITY' => 35,
				'COM_GAMES_ESRB_CONTENT_PARTIAL_NUDITY' => 36,
				'COM_GAMES_ESRB_CONTENT_SEXUAL_CONTENT' => 37,
				'COM_GAMES_ESRB_CONTENT_SEXUAL_THEMES' => 38,
				'COM_GAMES_ESRB_CONTENT_STRONG_SEXUAL_CONTENT' => 39,
				'COM_GAMES_ESRB_CONTENT_SUGGESTIVE_THEMES' => 40),
			'COM_GAMES_ESRB_CONTENT_MISCELLANEOUS' => array(
				'COM_GAMES_ESRB_CONTENT_NO_DESCRIPTORS' => 41),
			'COM_GAMES_ESRB_CONTENT_GAMBLING' => array(
				'COM_GAMES_ESRB_CONTENT_REAL_GAMBLING' => 42,
				'COM_GAMES_ESRB_CONTENT_SIMULATED_GAMBLING' => 43)
		);

		// Build the grouped list array.
		foreach($options as $group => $array)
		{
			// Initialize the group.
			$group = JText::_($group);
			$groups[$group] = array();

			foreach ($array as $text => $value)
			{
				$groups[$group][] = JHtml::_('select.option', $value, JText::_($text));
			}
		}

		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}