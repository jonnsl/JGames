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


class JFormFieldPegirating extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Pegirating';

	protected function getInput()
	{
		?>
		<div class="rating">
			<a href="" class="select<?php if($this->value == 4) echo ' selected';?>" rel="4"><img src="../media/games/images/pegi/4.gif" alt=""/></a>
			<a href="" class="select<?php if($this->value == 6) echo ' selected';?>" rel="6"><img src="../media/games/images/pegi/6.gif" alt=""/></a>
			<a href="" class="select<?php if($this->value == 12) echo ' selected';?>" rel="12"><img src="../media/games/images/pegi/12.gif" alt=""/></a>
			<a href="" class="select<?php if($this->value == 16) echo ' selected';?>" rel="16"><img src="../media/games/images/pegi/16.gif" alt=""/></a>
			<a href="" class="select<?php if($this->value == 18) echo ' selected';?>" rel="18"><img src="../media/games/images/pegi/18.gif" alt=""/></a>
			<input type="hidden" name="<?php echo $this->name; ?>" value="<?php echo $this->value?>">
			<div class="clr"></div>
		</div>
	<?php
	}
}