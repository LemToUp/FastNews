<?php defined('_JEXEC') or die;

/**
 * File       mod_fastnews.php
 * Created    1/06/16
 * Author     Dmitry Rumiantsev | lemtoup@gmail.com
 * License    GNU General Public License version 2, or later.
 */


class modFastnewsHelper {

	protected $smt = '';

	public static function getAjax() {

		// Get module parameters
		jimport('joomla.application.module.helper');
		$input  = JFactory::getApplication()->input;
		$module = JModuleHelper::getModule('fastnews');
		$params = new JRegistry();
		$params->loadString($module->params);
		$node        = $params->get('node', 'data');
		$refresh 	 = $params->get('refresh', 10)/**1000*/;
		$session     = JFactory::getSession();
		$sessionData = $session->get($node);

		if (is_null($sessionData)) {
			$sessionData = array();
			$session->set($node, $sessionData);
		}

		if ($input->get('cmd')) {
			$cmd  = $input->get('cmd');

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			$date = JFactory::getDate();
			$pastDate = new JDate('now - ' . $refresh . ' seconds');

			$nowDate = $db->quote($date->toSql());
			$pastDate = $db->quote($pastDate->toSql());
			$nullDate = $db->quote($db->getNullDate());

			switch ($cmd) {
				case "get" :
					$query->select('a.id, a.catid, a.title');
					break;

				default:
					$query->select($db->qn('id'));
					break;
			}
			$query->from('#__content AS a');

			$query->where('( a.publish_up > ' . $pastDate . ') ', 'AND');
			$query->where('( a.publish_up <= ' . $nowDate . ') ', 'AND');
			$query->where('( a.publish_down = '. $nullDate .' OR a.publish_down >= ' . $nowDate . ')');
			$query->order($db->qn('publish_up').' DESC');
			

			$db->setQuery($query);
			$item = $db->loadObject();

			if ($item) {
				$item->url = JRoute::_('index.php?view=article&catid=' . $item->catid . '&id=' . $item->id);

				$item = json_encode($item);
				return $item;
			}
			return FALSE;
		}
		return FALSE;
	}
}