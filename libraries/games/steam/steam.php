<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

define('STEAMWORKS_API_SERVER', 'http://steamcommunity.com/id');

/**
 * SteamWorks Class
 * 
 * obs.: ChetFaliszek have ALL games that are avaiable trough steam
 * @see https://partner.steamgames.com/documentation/community_data
 */
class JGSteam extends JObject
{
	public function getProfile($userId)
	{
		// $url = STEAMWORKS_API_SERVER.'/'.$userId.'/?xml=1';
	}
	
	public function getGames($userId = 'ChetFaliszek')
	{
		$data = $this->getData(STEAMWORKS_API_SERVER.'/'.$userId.'/games/?xml=1');
		
		foreach($data->games->game as $game)
		{
			$tmp = new stdClass;
			foreach($game as $k => $v)
			{
				$tmp->$k = (string) $v;
			}
			if(isset($tmp->statsLink) && preg_match('#/([^/]*)$#i', $tmp->statsLink, $matches)) $tmp->communityGameName = $matches[1];
			$games[$tmp->appID] = $tmp;
		}
		
		return $games;
	}

	/**
	 * Get the Steam Achievements for a game
	 * 
	 * @param	$app	string|integer	The AppId or the CommunityGameName.
	 * @param	$userId	string|integer	The userId or the customUrl.
	 * @return	array	Returns an array containing all the achievements of the given game.
	 */
	public function getAchievements($app, $user = 'ChetFaliszek')
	{
		// Initialise variables
		$achievements = array();

		try
		{
			$game = $this->getGame($app, $user);
			$url = isset($game->statsLink) ? $game->statsLink.'/?xml=1' : STEAMWORKS_API_SERVER.'/'.$user.'/stats/appid/'.$game->appID.'/?xml=1';
			$xml = $this->getData($url);
			
			// Check for an error
			if(isset($xml->error))
			{
				throw new JException($xml->error);
			}

			foreach ($xml->achievements->achievement as $achievement)
			{
				$achievements[] = array('iconClosed' => (string)$achievement->iconClosed, 'iconOpen' => (string)$achievement->iconOpen, 'name' => (string)$achievement->name, 'description' => (string)$achievement->description);
			}
			
		}
		catch(JException $e){throw $e;}

		return $achievements;
	}
	
	public function getGame($app, $user)
	{
		$games = $this->getGames();
		if (is_numeric($app) && isset($games[$app]))
		{
			return $games[$app];
		}
		else if (is_string($app))
		{
			foreach ($games as $game)
			{
				if (
					(isset($game->name) && $game->name == $app) ||
					(isset($game->communityGameName) && $game->communityGameName == $app)
				) {
					return $game;
				}
			}
		}

		throw new JException('Game not found.');
	}
	
	public function getStats($CommunityGameName, $userId = 'ChetFaliszek')
	{
		// $url = STEAMWORKS_API_SERVER.'/'.$userId.'/stats/'.$CommunityGameName.'/?xml=1';
	}
	
	public function getAppId($app)
	{
		return $this->getGame($app, $user)->appID;
	}
	
	public function getCommunityGameName($app)
	{
		return $this->getGame($app, $user)->communityGameName;
	}
	
	private function getData($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($ch);
		curl_close($ch);
		
		if(empty($res) || !$xml = simplexml_load_string($res))
		{
			throw new JException('Could not get the data.');
		} else {
			return $xml;
		}
	}
}