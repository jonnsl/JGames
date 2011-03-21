<?php

// No direct access
defined('JPATH_BASE') or die;

class plgContentGamesInstallerScript
{
	public function install()
	{
		$prefix = JFactory::getConfig()->get('dbprefix', 'jos_');
		$db = JFactory::getDbo();
		$db->setQuery('CREATE TABLE IF NOT EXISTS `'.$prefix.'games_content` (
				`content_id` integer NOT NULL,
				`game_id` integer NOT NULL
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8');
		return (bool)$db->query();
	}

	public function uninstall()
	{
		$prefix = JFactory::getConfig()->get('dbprefix', 'jos_');
		$db = JFactory::getDbo();
		$db->setQuery('DROP TABLE IF EXISTS `'.$prefix.'games_content`');
		return (bool)$db->query();
	}
}
