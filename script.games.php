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
		
		JHtml::_('behavior.framework', true);
		$script = "window.addEvent('domready',function(){document.id('InstallSampleData').addEvent('click',function(){var e=this;var request=new Request.JSON({url:'index.php?option=com_games&controller=ajax&task=InstallSampleData&format=json',onRequest:function(){e.set('value','Installing Sample Data.');e.setStyle('padding-left',18);e.setStyle('background','url(data:image/png;base64,R0lGODlhEAALAPQAAP///wAAANra2tDQ0Orq6gYGBgAAAC4uLoKCgmBgYLq6uiIiIkpKSoqKimRkZL6+viYmJgQEBE5OTubm5tjY2PT09Dg4ONzc3PLy8ra2tqCgoMrKyu7u7gAAAAAAAAAAACH+GkNyZWF0ZWQgd2l0aCBhamF4bG9hZC5pbmZvACH5BAALAAAAIf8LTkVUU0NBUEUyLjADAQAAACwAAAAAEAALAAAFLSAgjmRpnqSgCuLKAq5AEIM4zDVw03ve27ifDgfkEYe04kDIDC5zrtYKRa2WQgAh+QQACwABACwAAAAAEAALAAAFJGBhGAVgnqhpHIeRvsDawqns0qeN5+y967tYLyicBYE7EYkYAgAh+QQACwACACwAAAAAEAALAAAFNiAgjothLOOIJAkiGgxjpGKiKMkbz7SN6zIawJcDwIK9W/HISxGBzdHTuBNOmcJVCyoUlk7CEAAh+QQACwADACwAAAAAEAALAAAFNSAgjqQIRRFUAo3jNGIkSdHqPI8Tz3V55zuaDacDyIQ+YrBH+hWPzJFzOQQaeavWi7oqnVIhACH5BAALAAQALAAAAAAQAAsAAAUyICCOZGme1rJY5kRRk7hI0mJSVUXJtF3iOl7tltsBZsNfUegjAY3I5sgFY55KqdX1GgIAIfkEAAsABQAsAAAAABAACwAABTcgII5kaZ4kcV2EqLJipmnZhWGXaOOitm2aXQ4g7P2Ct2ER4AMul00kj5g0Al8tADY2y6C+4FIIACH5BAALAAYALAAAAAAQAAsAAAUvICCOZGme5ERRk6iy7qpyHCVStA3gNa/7txxwlwv2isSacYUc+l4tADQGQ1mvpBAAIfkEAAsABwAsAAAAABAACwAABS8gII5kaZ7kRFGTqLLuqnIcJVK0DeA1r/u3HHCXC/aKxJpxhRz6Xi0ANAZDWa+kEAA7AAAAAAAAAAAA) no-repeat');},onSuccess:function(jsonResponse){if(jsonResponse.success==true){e.set('disabled','disabled');e.set('value',jsonResponse.msg);e.setStyle('padding-left',15);e.setStyle('background','url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNXG14zYAAAHLSURBVCiRlZLPS5MBGMe/z/vu1dSpaSqRwxgNV5QMUnNeitigOqhUuymCHjx4kCJSu3iqLur0YAgO9B/wYnoIITp4KTyIBsNpbkHx1hzNsTR0P95vlwydivOBBx54+Dw8fL9fIYmzloj6NrdEfoDkmVq7nOetfVHG85XKStYQAK38umm2bd5CR3vhEoCSbMHSGy4s9vutvDdsIYBaksgGtLlbEXr91caODw7mVqgjB3ZKPoCbJ4CNnieIDet2PgvdZtV9cxCA+n9felEJtI+cY8MDvN9/5x/4yDOgpYbCNez/7jKcA5cIwHnoeEERJnwLdi4mHex6AxZYZRRAT8tgIQcjdexdu5t+OHuNORWKN/MzIYmaO/kzY3NtzSnzT8wsLGFtPQV3kxWbkRzjl+wo754Ggvr872qS6UN+k4SI2Du8Vauebjf1rTBiRhybUTCel5CPU99k+ZXuJPkpMywmACAZuGCTSZvL36kVFxmRHUpMEhJajot/Ijx6HAgAyv4Q3UDvnG/9z7a2K9G9PUYTSVkZ14PJSPr5iTk9KIBqRl/LdDVbN24ZV/sqj6h7xMoMX1VLU/Hnet8VauWml6cG6JhgPAbwBYByGvwXxzG/0vUSIXoAAAAASUVORK5CYII=) no-repeat');}else{var systemMessage=document.id('system-message');if(systemMessage){var dt=new Element('dt',{'class':'error','html':'Error'}).inject(systemMessage,'bottom');var dd=new Element('dd',{'class':'error message','html':'<ul><li>'+jsonResponse.msg+'</li></ul>'}).inject(systemMessage,'bottom');}else{var error=new Element('dl',{'id':'system-message'});error.set('html','<dt class=\"error\">Error</dt><dd class=\"error message\"><ul><li>'+jsonResponse.msg+'</li></ul></dd>');error.inject(document.id('element-box'),'before');}}}}).send();});});";
		JFactory::getDocument()->addScriptDeclaration($script);
?>
		<input type="button" id="InstallSampleData" value="Install Sample Data"/>
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
