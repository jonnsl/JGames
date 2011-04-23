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
JHtml::_('stylesheet', 'games/gameitem.css', array(), true);
JHtml::_('script', 'games/gameitem.js', true, true);
$game = $this->item;
?>
<div id="game_item" class="<?php echo $this->pageclass_sfx;?>">

	<div class="position-left">
		<?php echo JHtml::_('games.boxart', $game->title, $game->boxarts, $this->state->get('filter.platform', 0));?>
		<ul class="details">
			<?php 
				if (!empty($game->developer))	echo '<li><a href="'.JRoute::_('index.php?option=com_games&developer='.$game->developer).'" title="Developer">'.$game->developer.'</a></li>';
				if (!empty($game->publisher))	echo '<li><a href="'.JRoute::_('index.php?option=com_games&publisher='.$game->publisher).'" title="Publisher">'.$game->publisher.'</a></li>';
				if (!empty($game->serie))		echo '<li><a href="'.JRoute::_('index.php?option=com_games&serie='.$game->serie).'" title="Serie">'.$game->serie.'</a></li>';
				if (!empty($game->site))		echo '<li><a href="'.htmlspecialchars($game->site, ENT_COMPAT, 'UTF-8').'" title="'.htmlspecialchars($game->site, ENT_COMPAT, 'UTF-8').'" rel="external">Official Site</a></li>';
				if ($this->platform_params->get('achievements_type')) echo '<li><a href="'.JRoute::_('index.php?option=com_games&view=achievements&id='.$game->id.'&platform='.$this->state->get('filter.platform', 0)).'" title="">'.JText::_('ACHIEVEMENTS_TYPE_'.$this->platform_params->get('achievements_type')).'</a></li>';
			?>
		</ul>
		<jdoc:include type="modules" name="games-position-left" />
	</div>
	<div class="position-right">
		<h1><?php echo $game->title; ?></h1>
		<div class="platforms">
			<?php echo JHtml::_('games.platformsList', $game, $this->state->get('filter.platform', 0)); ?>
		</div>
		<div id="achievements">
			<?php
			echo $this->loadTemplate($this->platform_params->get('achievements_type'));
			?>
		</div>
	</div>

</div>
<pre>
	<?php //echo htmlspecialchars(print_r($game, 1), ENT_COMPAT, 'UTF-8'); ?>
</pre>
