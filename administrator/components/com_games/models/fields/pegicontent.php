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
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_games/helpers/html');

class JFormFieldPegicontent extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Pegicontent';

	protected function getInput()
	{
		?>
			<div id="content">
				<?php
				$content = array('violence', 'bad_language', 'fear', 'sex', 'drugs', 'discrimination', 'gambling', 'online');

				foreach ($content as $v) {
					$selected = (bool)  in_array($v, (array) $this->value);
					$label = JText::_('COM_GAMES_PEGI_'.strtoupper($v).'_LABEL');
					$title = $label.'::'.JText::_('COM_GAMES_PEGI_'.strtoupper($v).'_DESC');
					?>
					<a href="" class="select hasTip<?php if($selected) echo ' selected'; ?>" title="<?php echo $title;?>">
					<img src="../media/games/images/pegi/<?php echo $v; ?>.gif"/>
					<input type="checkbox" name="<?php echo $this->name; ?>[]" value="<?php echo $v; ?>" <?php if($selected) echo 'checked="checked"';?>/>
					</a>
					<?php
				}
				?>
			</div>
	<?php
	}
}