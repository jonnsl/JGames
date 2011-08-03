<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');
JHtml::_('stylesheet', 'games/games'.(JDEBUG ? '' : '.min').'.css', array(), true);
JHtml::_('script', 'games/games'.(JDEBUG ? '' : '.min').'.js', true, true);
JHtml::_('script', 'games/modal_game_view.js', true, true);

?>
<div id="gameslist" class="<?php echo $this->pageclass_sfx;?>">

	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<div class="clr"></div>

	<div id="filters">
		<form action="<?php echo JRoute::_('index.php');?>" id="filters_form">
			<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_GAMES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

			</div>
			<div class="clr"></div>
			<input type="hidden" name="option" value="com_games">
			<input type="hidden" name="view" value="games">
			<input type="hidden" name="layout" value="modal">
			<input type="hidden" name="tmpl" value="component">
			<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>">
			<input type="hidden" name="field" value="<?php echo $this->escape(JRequest::getCmd('field')); ?>" />
			<select name="platform" class="inputbox">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_PLATFORM'); ?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_games.platforms'), 'value', 'text', $this->state->get('filter.platform'));?>
			</select>

			<select name="genre" class="inputbox">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_GENRE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_games.genres'), 'value', 'text', $this->state->get('filter.genre'));?>
			</select>

			<select name="serie" class="inputbox">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_SERIE');?></option>
				<?php echo JHtml::_('select.options', $this->series, 'value', 'text', $this->state->get('filter.serie'));?>
			</select>

			<select name="developer" class="inputbox">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_DEVELOPER');?></option>
				<?php echo JHtml::_('select.options', $this->developers, 'value', 'text', $this->state->get('filter.developer'));?>
			</select>

			<select name="publisher" class="inputbox">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_SELECT_PUBLISHER');?></option>
				<?php echo JHtml::_('select.options', $this->publishers, 'value', 'text', $this->state->get('filter.publisher'));?>
			</select>

			<select name="filter_order" class="inputbox">
				<option value=""><?php echo JText::_('COM_GAMES_OPTION_ORDER_BY');?></option>
				<?php echo JHtml::_('select.options', $this->orders, 'value', 'text', $this->state->get('list.ordering'), true);?>
			</select>
	

			<div class="clr"></div>
		
			<ul id="games">
				<?php foreach ($this->items as $item) :
				$item->boxarts = new JRegistry($item->boxarts);
				$item->boxarts = $item->boxarts->toArray();
				?>
				<li id="game-<?php echo $item->id; ?>" class="game">
					<?php echo JHtml::_('games.boxart', $item->title, $item->boxarts, $this->state->get('filter.platform', 0));?>
					<div class="clr"></div>
					<div class="game-info">
						<a class="caption select_game" rel="<?php echo (int) $item->id; ?>" href="#"><?php echo $item->title; ?></a>
					</div>
				</li>
				<?php endforeach;?>
			</ul>
	
		</form>
	</div>
	<div class="clr"></div>

	<div id="pagination">
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>

		<div class="clr"></div>

		<div class="pagination">
		 	<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>

			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>

		<div class="clr"></div>
</div>
