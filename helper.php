<?php defined('_JEXEC') or die;


class modFastnewsHelper {

	protected $smt = '';

	public function __construct()
	{

	}

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

			$sqlGmtTimestamp = date(time());
			$nowSMT = JHtml::date($sqlGmtTimestamp, 'Y-m-d h:i:s');
			$smt = time() - strtotime($nowSMT);

			switch ($cmd) {
				case "check" :
					$query->select('UNIX_TIMESTAMP('.$db->qn('publish_up').') as publish_up');
					break;

				case "get" :
					$query->select($db->qn(array('id', 'catid', 'title')));
					break;

				default:
					$query->select($db->qn('id'));
					break;
			}
			$query->from($db->qn('#__content'));
			$query->where($db->qn('publish_up').'< NOW()', 'AND');
			//$query->where('(UNIX_TIMESTAMP('.$db->qn('publish_up').') + '.$refresh.' > UNIX_TIMESTAMP()) ', 'AND');
			//$query->where('(UNIX_TIMESTAMP('.$db->qn('publish_up').') + '.$smt.' < UNIX_TIMESTAMP()) ', 'AND');
			$query->where('('.$db->qn('publish_down')." = '0000-00-00 00:00:00' OR ".$db->qn('publish_down').' > NOW())');
			$query->order($db->qn('publish_up').' DESC');

			//print_r($query->dump());
			//die();

			$db->setQuery($query);
			$item = $db->loadObject();

			if ($item) {
				$item->time = time();
				if ($item->id && $item->catid) {
					$item->url = JRoute::_('index.php?view=article&catid=' . $item->catid . '&id='.$item->id);

				}
				$item = json_encode($item);
				return $item;
			}
			return FALSE;
		}
	}
}