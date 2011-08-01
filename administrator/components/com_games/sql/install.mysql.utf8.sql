CREATE TABLE IF NOT EXISTS `#__games` (
  `id` integer unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `site` varchar(255) NOT NULL default '',
  `description` varchar(5120) NOT NULL default '',
  `developer` varchar(255) NOT NULL default '',
  `publisher` varchar(255) NOT NULL default '',
  `serie` varchar(255) NOT NULL default '',
  `boxarts` TEXT NOT NULL default '',
  `esrb_rating` int(11) unsigned NOT NULL default '0',
  `esrb_content` varchar(255) NOT NULL default '',
  `pegi_rating` int(11) unsigned NOT NULL default '0',
  `pegi_content` varchar(255) NOT NULL default '',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `achievements` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__games_genre_map` (
  `game_id` integer NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__games_platform_map` (
  `game_id` integer NOT NULL,
  `platform_id` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__games_content` (
  `content_id` integer unsigned NOT NULL,
  `game_id` integer NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;