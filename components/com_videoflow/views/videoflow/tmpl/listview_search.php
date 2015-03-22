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

defined('_JEXEC') or die('Restricted access'); 
global $vparams;
$Itemid = JRequest::getInt('Itemid', $vparams->flowid);
$lo = JRequest::getCmd('layout');
$c = JRequest::getCmd('c');
if ($c == 'fb') {
$target = 'target="_parent"';
$action = $vparams->canvasurl.'&task=search&vs=1';
} else {
  $target = '';
  $action = JRoute::_('index.php');
}
?>
<div id="vfsearch" class="row-fluid">
<div class="span12">
<form id="searchForm" class="form-search" action="<?php echo $action; ?>" <?php echo $target; ?> method="get" name="searchForm">
<?php
if ($c != 'fb') {
  ?>
  <input type="hidden" name="option" value="com_videoflow" />
  <input type="hidden" name="task" value="search" />
  <input type="hidden" name="vs" value="1" />
  <input type="hidden" name="layout" value="<?php echo $lo; ?>" />
  <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
  <?php
}
?>
	<input type="text" name="searchword" id="search_searchword" class="input-large" placeholder="<?php echo JText::_( 'COM_VIDEOFLOW_SEARCH_KEY' ); ?>"  value="" />
	<button name="sbtn" onclick="this.form.submit()" class="btn"><?php echo JText::_( 'COM_VIDEOFLOW_SEARCH' );?></button>	
 </form>
</div>
</div>