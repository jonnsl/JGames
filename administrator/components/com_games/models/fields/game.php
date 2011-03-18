<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.form.formfield');
JGImport('html.xhtml');

class JFormFieldGame extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'Game';

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		if (empty($this->value)) $this->value = array('name' => '', 'id' => '');

		// Load the scripts.
		JHtml::_('behavior.modal', 'a.modal_'.$this->id);
		JHtml::_('script', 'games/modal_game_field.js', true, true);

		// Initialize variables.
		$html = array();
		$link = 'index.php?option=com_games&amp;view=games&amp;layout=modal&amp;tmpl=component&amp;field='.$this->id;

		// Create a dummy text field with the game name.
		$div	= new JGXhtml('div', array('class' => 'fltlft'));
		$input	= new JGXhtml('input', array('type' => 'text','id' => $this->id.'_name', 'name' => $this->name.'[name]','value' => $this->value['name'], 'disabled' => true));
		$div->setHtml($input);
		
		$html[] = (string)$div;


		// Create the game select button.
		$div2	= new JGXhtml('div', array('class' => 'button2-left'));
		$div3	= new JGXhtml('div', array('class' => 'blank'));
		$a		= new JGXhtml('a', array('class' => 'modal_'.$this->id, 'title' => JText::_('JLIB_FORM_CHANGE_USER'), 'href' => $link, 'rel' => "{handler: 'iframe', size: {x: 1000, y: 500}}"));
		$a->setText(JText::_('PLG_CONTENT_GAMES_SELECT_GAME'));
		$div3->setHtml($a);
		$div2->setHtml($div3);
		$html[] = (string)$div2;

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="'.$this->id.'_id" name="'.$this->name.'[id]" value="'.(int) $this->value['id'].'" />';

		return implode("\n", $html);
	}
}
