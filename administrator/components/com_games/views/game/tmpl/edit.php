<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.framework', true);
JHtml::_('script', 'system/modal.js', true, true);
JHtml::_('stylesheet', 'system/modal.css', array(), true);
if (JDEBUG) {
	JHtml::_('script', 'games/admin/game.js', false, true, false, false);
	JHtml::_('script', 'games/admin/game-autocomplete.js', false, true, false, false);
	JHtml::_('script', 'games/admin/game-boxart.js', false, true, false, false);
	JHtml::_('script', 'games/admin/game-carousel.js', false, true, false, false);
	JHtml::_('stylesheet', 'games/admin/game.css', array(), true);
	JHtml::_('stylesheet', 'games/admin/game-autocomplete.css', array(), true);
} else {
	JHtml::_('script', 'games/admin/game.min.js', false, true, false, false);
	JHtml::_('stylesheet', 'games/admin/game.min.css', array(), true);
}
?>

<script type="text/javascript">
<!--
Joomla.submitbutton = function(task)
{
	if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		<?php echo $this->form->getField('description')->save(); ?>
		Joomla.submitform(task);
	}
	else {
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	}
};
window.addEvent('domready',function(){
	new Boxart(<?php echo $this->platformsParams.', '.$this->item->boxarts; ?>);
});
// -->
</script>

<form action="<?php JRoute::_('index.php?option=com_games'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::sprintf('COM_GAMES_FIELDSET_BASIC', $this->item->id); ?></legend>

		<div>
		<?php echo $this->form->getLabel('title'); ?>
		<?php echo $this->form->getInput('title'); ?>
		</div>

		<div>
		<?php echo $this->form->getLabel('alias'); ?>
		<?php echo $this->form->getInput('alias'); ?>
		</div>

		<div>
		<?php echo $this->form->getLabel('genres'); ?>
		<?php echo $this->form->getInput('genres'); ?>
		</div>

		<div>
		<?php echo $this->form->getLabel('platforms'); ?>
		<?php echo $this->form->getInput('platforms'); ?>
		</div>

		<div class="clr"></div>
		<?php echo $this->form->getLabel('description'); ?>
		<div class="clr"></div>
		<?php echo $this->form->getInput('description'); ?>

		</fieldset>
	</div>

	<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start','game-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

		<?php echo JHtml::_('sliders.panel',JText::_('COM_GAMES_FIELDSET_BOXART'), 'fieldset_boxart'); ?>
		<fieldset class="panelform">
			<img src="../media/games/images/button_prev.png" alt="Previous" id="previous"/>
			<div id="boxarts" class="carousel">
				<div id="boxarts_inner" class="inner"></div>
			</div>
			<img src="../media/games/images/button_next.png" alt="Next" id="next"/>
		</fieldset>

		<?php echo JHtml::_('sliders.panel',JText::_('COM_GAMES_FIELDSET_DETAILS'), 'fieldset_details'); ?>
		<fieldset class="panelform">
			<div>
			<?php echo $this->form->getLabel('site'); ?>
			<?php echo $this->form->getInput('site'); ?>
			</div>

			<div>
			<?php echo $this->form->getLabel('developer'); ?>
			<?php echo $this->form->getInput('developer'); ?>
			</div>

			<div>
			<?php echo $this->form->getLabel('publisher'); ?>
			<?php echo $this->form->getInput('publisher'); ?>
			</div>

			<div>
			<?php echo $this->form->getLabel('serie'); ?>
			<?php echo $this->form->getInput('serie'); ?>
			</div>

		<div class="clr"></div>
		</fieldset>

		<?php echo JHtml::_('sliders.panel',JText::_('COM_GAMES_FIELDSET_ESRB'), 'fieldset_esrb'); ?>
		<fieldset class="panelform">
			<div id="esrb">
				<?php echo $this->form->getLabel('esrb_rating'); ?>
				<div class="clr"></div>
				<?php $this->form->getInput('esrb_rating'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('esrb_content'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('esrb_content'); ?>
			</div>
		</fieldset>

		<?php echo JHtml::_('sliders.panel',JText::_('COM_GAMES_FIELDSET_PEGI'), 'fieldset_pegi'); ?>
		<fieldset class="panelform">
			<div id="pegi">
				<?php echo $this->form->getLabel('pegi_rating'); ?>
				<div class="clr"></div>
				<?php $this->form->getInput('pegi_rating')?>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('pegi_content'); ?>
				<div class="clr"></div>
				<?php $this->form->getInput('pegi_content')?>
			</div>
		</fieldset>

	<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clr"></div>
