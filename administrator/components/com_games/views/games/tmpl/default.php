<?php
/**
 * @version		$Id:$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');

$user		= JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="<?php echo JRoute::_('index.php?option=com_games&view=games');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_GAMES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

		</div>
		<div class="filter-select fltrt">

			<select name="filter_serie" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_SERIE');?></option>
				<?php echo JHtml::_('select.options', $this->series, 'value', 'text', $this->state->get('filter.serie'));?>
			</select>

			<select name="filter_genre" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_GENRE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_games.genres'), 'value', 'text', $this->state->get('filter.genre'));?>
			</select>

			<select name="filter_platform" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_PLATFORM');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_games.platforms'), 'value', 'text', $this->state->get('filter.platform'));?>
			</select>

			<select name="filter_developer" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_DEVELOPER');?></option>
				<?php echo JHtml::_('select.options', $this->developers, 'value', 'text', $this->state->get('filter.developer'));?>
			</select>

			<select name="filter_publisher" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_PUBLISHER');?></option>
				<?php echo JHtml::_('select.options', $this->publishers, 'value', 'text', $this->state->get('filter.publisher'));?>
			</select>

		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_GAMES_HEADING_SERIE', 'a.serie', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_GAMES_HEADING_DEVELOPER', 'a.developer', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_GAMES_HEADING_PUBLISHER', 'a.publisher', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_GAMES_HEADING_PLATFORMS'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_GAMES_HEADING_GENRES'); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_games&task=game.edit&id='.$item->id);?>">
						<?php echo $this->escape($item->title); ?>
					</a>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
				<td class="center">
					<?php echo $this->escape($item->serie); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->developer); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->publisher); ?>
				</td>
				<td class="center">
					<?php echo nl2br($this->escape($item->platforms)); ?>
				</td>
				<td class="center">
					<?php echo nl2br($this->escape($item->genres)); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
