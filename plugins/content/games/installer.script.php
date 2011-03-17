<?php

// No direct access
defined('JPATH_BASE') or die;

class plgContentGamesInstallerScript
{
	public function install()
	{
		$db = JFactory::getDbo();
		$db->setQuery('CREATE TABLE IF NOT EXISTS `#__games_content` (
				`content_id` integer NOT NULL,
				`game_id` integer NOT NULL
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8');
		return (bool)$db->query();
	}

	public function uninstall()
	{
		$db = JFactory::getDbo();
		$db->setQuery('DROP TABLE IF EXISTS `#__games_content`');
		return (bool)$db->query();
	}
	
	public function postflight()
	{
		$db = JFactory::getDbo();
		$db->setQuery('UPDATE `#__extensions` SET `enabled` = 1 where `name` = "plg_content_games"');
		return (bool)$db->query();
	}
}
