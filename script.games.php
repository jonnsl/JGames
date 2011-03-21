<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

class Com_GamesInstallerScript
{
	public function __construct($installer)
	{
		$this->installer = $installer;
	}

	public function install($adapter)
	{
		$src = dirname(__FILE__);
		$status = new JObject();
		$db = JFactory::getDbo();
		
		// System - JGames
		$installer = new JInstaller;
		$result = $installer->install($src.'/plugins/system/games');
		$status->plugins[] = array('name' => 'System - JGames','group' => 'system', 'result' => $result);
		
		// Content - JGames
		$installer = new JInstaller;
		$result = $installer->install($src.'/plugins/content/games');
		$status->plugins[] = array('name' => 'Content - JGames','group' => 'content', 'result' => $result);
		
		// JGames Libraries
		if ($result = JFolder::move($src.'/libraries/games', JPATH_LIBRARIES.'/games') === true)
		{
			if ($result = JFile::move(JPATH_LIBRARIES.'/games/games.xml', JPATH_MANIFESTS.'/libraries/games.xml') === true)
			{
				$db->setQuery("INSERT INTO `#__extensions` VALUES (NULL, 'JGames Library', 'library', 'games', '', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0)");
				$result = $db->query();
			}
		}
		$status->libraries[] = array('name' => 'JGames Libraries', 'result' => $result);
?>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
					<th width="30%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2">JGames</td>
					<td><strong><?php echo JText::_('Installed'); ?></strong></td>
				</tr>
				<?php if (count($status->plugins)) : ?>
					<tr>
						<th><?php echo JText::_('Plugin'); ?></th>
						<th><?php echo JText::_('Group'); ?></th>
						<th></th>
					</tr>
					<?php foreach ($status->plugins as $plugin) : ?>
					<tr class="row<?php echo (++ $rows % 2); ?>">
						<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
						<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
						<td><strong><?php echo ($plugin['result'])?JText::_('Installed'):JText::_('Not installed'); ?></strong></td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if (count($status->libraries)) : ?>
					<tr>
						<th class="title" colspan="2"><?php echo JText::_('Libraries'); ?></th>
						<th width="30%"><?php echo JText::_('Status'); ?></th>
					</tr>
					<?php foreach ($status->libraries as $library) : ?>
					<tr class="row<?php echo (++ $rows % 2); ?>">
						<tr class="row0">
							<td class="key" colspan="2"><?php echo $library['name']; ?></td>
							<td><strong><?php echo ($library['result'])?JText::_('Installed'):JText::_('Not installed'); ?></strong></td>
						</tr>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
<?php
		return true;
	}
}
