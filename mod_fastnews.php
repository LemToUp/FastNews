<?php defined('_JEXEC') or die;

/**
 * File       mod_fastnews.php
 * Created    1/06/16
 * Author     Dmitry Rumiantsev | lemtoup@gmail.com
 * License    GNU General Public License version 2, or later.
 */

// Include the helper.
require_once __DIR__ . '/helper.php';

// Instantiate global document object
$doc = JFactory::getDocument();

$loadJquery = $params->get('loadJquery', 1);
$refresh = $params->get('refresh', 10);
$position = $params->get('position', 1);

$format = 'JSON';
$time = time();
$message_prefix = JText::_('MOD_FASTNEWS_TMPL_PREFIX');

// Load jQuery
if ($loadJquery == '1') {
	$doc->addScript('//code.jquery.com/jquery-latest.min.js');
}

$js = "
(function ($) {

jQuery(document).ready(function(){
	  var action  = 'get',
		format = '$format',
		message_prefix = '$message_prefix',
		refreshTime = $refresh;
	
	setInterval(refresh, refreshTime*1000);
	
	function fnTriggerGet() {};

	function refresh () {
	
		request = {
				'option' : 'com_ajax',
				'module' : 'fastnews',
				'cmd'    : action,
				'format' : format
			};
		$.ajax({
			type   : 'POST',
			data   : request,
			success: function (response) {
			if(response.data){
			data = JSON.parse(response.data);
			if (data) {
				title = data['title'];
				//introtext = data['introtext'];
				url = data['url'];
				publish = '<a target=\"_blank\" href=\"'+url+'\"><h4>'+message_prefix+' '+title+'</h4></a>';
				$('#fastnews .fastnews_center').html(publish);
				$('#fastnews').slideDown('slow');
				//action = 'check';
				$( document ).trigger( 'fnGetEvent' );
			}
		}},
			error: function(response) {
				console.log(response);
	        }
		});
		return false;
	}
	});
})(jQuery)
";

$doc->addScriptDeclaration($js);

switch ($position) {
	case 1:
		$doc->addScript('/modules/mod_fastnews/js/fixed.js');
		$doc->addStyleSheet('/modules/mod_fastnews/css/fnfixed.css');

		require(JModuleHelper::getLayoutPath('mod_fastnews','fixed'));

		break;
	default:
		require(JModuleHelper::getLayoutPath('mod_fastnews'));
};


