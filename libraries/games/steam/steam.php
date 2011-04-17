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
			$app = $this->getCommunityGameName($app);
			$xml = $this->getData(STEAMWORKS_API_SERVER.'/'.$user.'/stats/'.$app.'/?xml=1');
			
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
	
	public function getStats($CommunityGameName, $userId = 'ChetFaliszek')
	{
		// $url = STEAMWORKS_API_SERVER.'/'.$userId.'/stats/'.$CommunityGameName.'/?xml=1';
	}
	
	public function getAppId($CommunityGameName)
	{
		$games = $this->getGames();
		foreach($games as $appId => $info)
		{
			if(isset($info->communityGameName) && $info->communityGameName == $CommunityGameName)
			{
				return $appId;
			}
		}
		throw new JException('Invalid CommunityGameName');
	}
	
	public function getCommunityGameName($appId)
	{
		$games = $this->getGames();
		if (is_numeric($appId))
		{
			if (isset($games[$appId]))
			{
				return $games[$appId]->communityGameName;
			}
			else
			{
				throw new JException('Invalid AppID');
			}
		}
		else if (is_string($appId))
		{
			foreach($games as $info)
			{
				if(isset($info->name) && $info->name == $appId)
				{
					return $info->communityGameName;
				}
			}
			throw new JException('Invalid CommunityGameName');
		}
		throw new JException('Invalid Arguments suplied to JGSteam::getCommunityGameName()');
	}
	
	private function getData($url)
	{
		JError::raiseWarning(0, $url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		// grab URL and pass it to the browser
		$res = curl_exec($ch);
		
		// close cURL resource, and free up system resources
		curl_close($ch);
		return simplexml_load_string($res);
	}
}