<?php
/**
 * Bible Gateway Verse of the Day Module
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__).DS.'votd.php');

$moduleclass_sfx = $params->def('moduleclass_sfx', '');
$version_id		 = $params->def('version', '31');
$method		 	 = $params->def('method', '0');

$config = JFactory::getConfig();
//$offset = $config->getValue('config.offset' );
$offset = $config->get('config.offset' ); 

if ($method == 'js') {
	require(JModuleHelper::getLayoutPath('mod_bg_votd', 'default.js'));
}
else {
	require(JModuleHelper::getLayoutPath('mod_bg_votd'));
}
?>
