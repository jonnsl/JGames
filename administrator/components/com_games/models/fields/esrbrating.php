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

class JFormFieldEsrbrating extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'Esrbrating';

	protected function getInput()
	{?>
		<div class="rating">
			<a href="" class="select<?php if($this->value == 1) echo ' selected';?>" data-value="1"><img src="../media/games/images/esrb/ka.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_KIDS_TO_ADULTS')?>"/></a>
			<a href="" class="select<?php if($this->value == 2) echo ' selected';?>" data-value="2"><img src="../media/games/images/esrb/e.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_EVERYONE')?>"/></a>
			<a href="" class="select<?php if($this->value == 3) echo ' selected';?>" data-value="3"><img src="../media/games/images/esrb/e10.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_EVERYONE_10')?>"/></a>
			<a href="" class="select<?php if($this->value == 4) echo ' selected';?>" data-value="4"><img src="../media/games/images/esrb/ec.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_EARLY_CHILDHOOD')?>"/></a>
			<a href="" class="select<?php if($this->value == 5) echo ' selected';?>" data-value="5"><img src="../media/games/images/esrb/t.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_TEEN')?>"/></a>
			<a href="" class="select<?php if($this->value == 6) echo ' selected';?>" data-value="6"><img src="../media/games/images/esrb/m.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_MATURE')?>"/></a>
			<a href="" class="select<?php if($this->value == 7) echo ' selected';?>" data-value="7"><img src="../media/games/images/esrb/ao.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_ADULTS_ONLY')?>"/></a>
			<a href="" class="select<?php if($this->value == 0) echo ' selected';?>" data-value="0"><img src="../media/games/images/esrb/rp.gif" alt="<?php echo JText::_('COM_GAMES_ESRB_RATING_PENDING')?>"/></a>
			<input type="hidden" name="<?php echo $this->name; ?>" value="<?php echo $this->value?>">

			<div class="clr"></div>
		</div>
	<?php
	}
}