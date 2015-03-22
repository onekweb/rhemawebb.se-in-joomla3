<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.1 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/
 
// No direct access
 
defined('_JEXEC') or die('Restricted access');  

global $vparams;
$tmpl = JRequest::getCmd ('tmpl');
$c = JRequest::getCmd('c'); 
$layout = JRequest::getCmd('layout');
$direct = JRequest::getCmd('direct');
if ($c == 'fb') {
$rlink = $vparams->canvasurl;	
} else {
$rlink = 'index.php?option=com_videoflow';
if (!empty($layout)) $rlink .= '&layout='.$layout;	            
}            
echo '<div style="padding:4px 8px;">';
if (isset($this->data) && !empty($this->data)) echo $this->data;
echo '</div>';
echo '<div style="padding:4px 8px; margin: 4px auto; text-align:center;">';
if ($tmpl == 'component') {
echo $this->genClose(null, JText::_('COM_VIDEOFLOW_CLOSE'));
} else {
echo $this->genClose($rlink, JText::_('COM_VIDEOFLOW_CONTINUE'));
}
echo '</div>';