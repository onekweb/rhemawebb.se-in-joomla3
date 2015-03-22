<?php
/**
 * @version   $Id: sidemodule.php 11960 2013-07-07 18:01:24Z arifin $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();
gantry_import('core.gantryfeature');


/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureSidemodule extends GantryFeature
{
	var $_feature_name = 'sidemodule';

	function init(){
		global $gantry;
	}

	function render($position="") {
		ob_start();
		global $gantry;
		return ob_get_clean();
	}
}
