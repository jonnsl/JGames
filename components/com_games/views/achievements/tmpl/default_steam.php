<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

$game = $this->item;

if (is_array($game->achievements))
{
	foreach ($game->achievements as $achievement) : ?>
	<div class="achievement">
		<img src="<?php echo $this->escape($achievement['iconClosed']); ?>" alt="<?php echo $this->escape($achievement['name']); ?>" />
		<div class="info">
			<h3><?php echo $this->escape($achievement['name']); ?></h3>
			<h5><?php echo $this->escape($achievement['description']); ?></h5>
		</div>
	</div>
			
<?php 
	endforeach;
}
else
{
	echo 'No Steam Achievements available.';
}
