<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

/**
 *
 */
class GamesViewGame extends JView
{
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->params	= $this->state->params;
		
		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->_template = $this->loadTemplate($tpl);

		if (JError::isError($this->_template)) {
			return $this->_template;
		}

		$this->parseTemplate();
		echo $this->renderTemplate();
	}
	
	private function parseTemplate()
	{
		$matches = array();
		$this->_modules = array();
		if (preg_match_all('#<jdoc:include\ type="modules" (.*)\/>#iU', $this->_template, $matches, PREG_SET_ORDER))
		{
			foreach ($matches as $matche)
			{
				$attribs = JUtility::parseAttributes($matche[1]);
				$position  = isset($attribs['name']) ? $attribs['name'] : null;
				$this->_modules[$matche[0]] = array('position' => $position, 'attribs' => $attribs);
			}
		}
	}
	
	private function renderTemplate()
	{
		$search = array();
		$replace = array();

		foreach($this->_modules as $jdoc => $args)
		{
			$search[] = $jdoc;
			$rendered = '';
			$modules = JModuleHelper::getModules($args['position']);
			foreach ($modules as $module)
			{
				$rendered .= $this->renderModule($module, $args);
			}
			$replace[] = $rendered;
		}
		return str_replace($search, $replace, $this->_template);
	}
	
	public function renderModule($module, $attribs = array(), $content = null)
	{
		if (!is_object($module))
		{
			$title	= isset($attribs['title']) ? $attribs['title'] : null;

			$module = JModuleHelper::getModule($module, $title);

			if (!is_object($module))
			{
				if (is_null($content)) {
					return '';
				}
				else {
					/**
					 * If module isn't found in the database but data has been pushed in the buffer
					 * we want to render it
					 */
					$tmp = $module;
					$module = new stdClass();
					$module->params = null;
					$module->module = $tmp;
					$module->id = 0;
					$module->user = 0;
				}
			}
		}

		// get the user and configuration object
		//$user = JFactory::getUser();
		$conf = JFactory::getConfig();

		// set the module content
		if (!is_null($content)) {
			$module->content = $content;
		}

		//get module parameters
		$params = new JRegistry;
		$params->loadJSON($module->params);
		
		// use parameters from template
		if (isset($attribs['params'])) {
			$template_params = new JRegistry;
			$template_params->loadJSON(html_entity_decode($attribs['params'], ENT_COMPAT, 'UTF-8'));
			$params->merge($template_params);
			$module = clone $module;
			$module->params = (string) $params;
		}
		
		$contents = '';

		$cachemode = $params->get('cachemode','oldstatic');  // default for compatibility purposes. Set cachemode parameter or use JModuleHelper::moduleCache from within the module instead

		if ($params->get('cache', 0) == 1  && $conf->get('caching') >= 1 && $cachemode != 'id' && $cachemode != 'safeuri')
		{

			// default to itemid creating mehod and workarounds on
			$cacheparams = new stdClass;
			$cacheparams->cachemode = $cachemode;
			$cacheparams->class = 'JModuleHelper';
			$cacheparams->method = 'renderModule';
			$cacheparams->methodparams = array($module, $attribs);

			$contents = JModuleHelper::ModuleCache($module, $params, $cacheparams);

		}
		else {
			$contents = JModuleHelper::renderModule($module, $attribs);
		}

		return $contents;
	}
}