<?php
/**
* @version   $Id: mod_popup.php 12358 2013-08-01 09:57:28Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantrylayout');

/**
 *
 * @package gantry
 * @subpackage html.layouts
 */
class GantryLayoutMod_Popup extends GantryLayout {
    var $render_params = array(
        'contents'       =>  null,
        'position'      =>  null,
        'gridCount'     =>  null,
        'pushPull'      =>  ''
    );
    function render($params = array()){
        global $gantry;

        $rparams = $this-> _getParams($params);
        ob_start();
    // XHTML LAYOUT
?>

<div id="rt-popup">
  <?php echo $rparams->contents; ?>
</div>

<?php
        return ob_get_clean();
    }
}