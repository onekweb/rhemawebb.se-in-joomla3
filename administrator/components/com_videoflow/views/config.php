<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.2 
* @ Copyright (C) 2008 - 2012 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
  JHtml::_('formbehavior.chosen', 'select');
class VideoflowViewConfig
{  
  
  function listMenu( &$rows, &$pageNav, &$lists)
  {
    global $vparams;
    JHtml::_('behavior.framework');
    $ordering = ($lists['order'] == 'm.ordering');
    $m = JRequest::getVar ('m', 'jmenu');
    $tbar= JToolBar::getInstance( 'toolbar' );
    JToolbarHelper::title( JText::_('COM_VIDEOFLOW_CONFIG_MANAGER'));
    JToolBarHelper::save( 'save' );
    JToolBarHelper::cancel( 'cancel' );
    $tbar->appendButton( 'Help', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=61', 725, 480 );
    ?>
    <form action="index.php?option=com_videoflow" method="post" name="adminForm" id="adminForm">
    <div id="filter-bar" class="btn-toolbar">
        <div class="btn-group pull-right hidden-phone">
	<label for="limit" class="element-invisible"><?php echo JText::_('COM_VIDEOFLOW_LIST_LIMIT');?></label>
	<?php echo $pageNav->getLimitBox(); ?>
        </div>
  </div>
    <div class="clearfix"></div>
    <table class="adminlist table-striped" style="width: 100%; margin-left: 20px; margin-right:20px;">
    <thead>
    <tr>
    <th width="10%">
    <?php echo JText::_( 'Num' ); ?>
    </th>
    <th width="10%">
    <?php
      if (version_compare(JVERSION, '3.0.0', 'lt')) {
    ?>  
      <input type="checkbox" name="toggle" value=""  onclick="checkAll(<?php echo count( $rows ); ?>);" />
    <?php
      } else {
    ?>
      <input type="checkbox" name="checkall-toggle" value=""  onclick="Joomla.checkAll(this)" />
    <?php  
      }
    ?>
    </th>
    <th nowrap="nowrap" class="name" style="text-align: left;" width="50%">
    <?php echo JHTML::_('grid.sort',  'Name', 'm.propername', @$lists['order_Dir'], @$lists['order'], 'morder' ); ?>
    </th>
    <th width="20%" nowrap="nowrap">
      <div class="vfmidalign">
	<div class="vfmidalign" style="display:inline-block;">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_ORDER'), 'm.ordering', @$lists['order_Dir'], @$lists['order'], 'morder' ); ?>
	</div>
	<div class="vfmidalign" style="display:inline-block;">
	<?php if ($ordering) echo JHTML::_('grid.order',  $rows ); ?>
	</div>
      </div>
    </th>
    <th width="10%" nowrap="nowrap">
    <?php echo JHTML::_('grid.sort',   'Id', 'm.pid', @$lists['order_Dir'], @$lists['order'], 'morder' ); ?>
    </th>
    </tr>
    </thead>
    <tfoot>
    <tr>
    <td colspan="6">
    <div style="margin-top: 10px;">  
    <?php echo $pageNav->getListFooter(); ?>
    </div>
    </td>
    </tr>
    </tfoot>
    <tbody>
    <?php
    $k = 0;
      for ($i=0, $n=count( $rows ); $i < $n; $i++) {
	$row = &$rows[$i];
	$checked = JHTML::_('grid.id', $i, $row -> pid );
    ?>
    <tr class="<?php echo "row$k"; ?>">
    <td align="center">
    <?php echo $pageNav->getRowOffset($i); ?>
    </td>
    <td align="center">
    <?php echo $checked; ?>
    </td>
    <td align="left">
    <?php echo $row->propername; ?>
    </span>
    </td>
    <td align="center">
    <?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
    <div><?php echo $pageNav->orderUpIcon( $i, true, 'morderup', JText::_('COM_VIDEOFLOW_MOVEUP'), true); ?><?php echo $pageNav->orderDownIcon( $i, $n, true, 'morderdown', JText::_('COM_VIDEOFLOW_MOVEDOWN'), true); ?></div>
    <div><input type="text" name="order[]" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="vfinput" /></div>							
    </td>	
    <td align="center">
    <?php echo $row->pid; ?>
    </td>
    </tr>
    <?php
    $k = 1 - $k;
    }
    ?>
    </tbody>
    </table>
    <input type="hidden" name="c" value="config" />
    <input type="hidden" name="option" value="com_videoflow" />
    <input type="hidden" name="task" value="morder" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="m" value="<?php echo $m; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
    </form>
    <?php
    }

    
  function listSettings( &$row ) 
  {    
  JHTML::_('behavior.modal', 'a.modal-vfpop');
  JToolbarHelper::title( JText::_('COM_VIDEOFLOW_CONFIG_MANAGER'));
  JToolBarHelper::apply ( 'apply' );
  JToolBarHelper::cancel( 'cancel' );
  $tbar= JToolBar::getInstance( 'toolbar' );
  $tbar->appendButton( 'Help', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=60', 725, 520 );

  if ($row->prostatus) $type = 'Pro'; else $type = 'Standard';
  jimport('joomla.html.pane');
  $seltab = JRequest::getInt('vtab');
  if (version_compare(JVERSION, '1.6.0') < 0) {
    $vfpress = 'function submitbutton(pressbutton)';
  } else {
  $vfpress = 'Joomla.submitbutton = function(pressbutton)';
  }

  $tabc = 'var vftab = 0;';
  $tabc .= $vfpress;
  $tabc .= '
  {
      if (pressbutton == "save") {
      document.adminForm.vtab.value = vftab;
      }
      submitform(pressbutton);
  }';
   
  $doc = JFactory::getDocument();
  $doc->addScriptDeclaration($tabc);
 ?>
	<form action="<?php JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	  <div id="j-sidebar-container" class="span2">
	  <?php
	  echo JHtmlSidebar::render();
	  ?>
	  </div>
	<div id="j-main-container" class="span10">
	<div class="col100 vfbackend vfconfig-h3">
	<?php	
	if (version_compare(JVERSION, '3.0.0', 'lt')) {
	  $vfTabs = JPane::getInstance('tabs', array('startOffset'=>$seltab));
	  echo $vfTabs->startPane( 'vftabs' );
	  echo $vfTabs->startPanel( JText::_('COM_VIDEOFLOW_GENERAL_SETTINGS'), 'tabone' );
	  } else {
	jimport( 'joomla.html.html.tabs' ); 
	$toptions = array(
	'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
	}',
	'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
	}',
	'startOffset' => $seltab, 
	'useCookie' => true, 
	);
      echo JHtml::_('tabs.start', 'vftabs', $toptions);
      echo JHtml::_('tabs.panel', JText::_('COM_VIDEOFLOW_GENERAL_SETTINGS'), 'tabone');
      }
      echo '<span id="tab1" onClick="vftab = 0">';
   ?>  
      <fieldset class="adminform">
	  <legend><?php echo JText::_( 'COM_VIDEOFLOW_SYS_SETTINGS' ); ?></legend>
          <table class="admintable">
            <tr>	
            <td class="key">
	    <label for="mode">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SYS_MODE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectsys, 'mode', null, 'value', 'text', $row->mode); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="jsframework">
	    <?php echo JText::_( 'COM_VIDEOFLOW_JSFRAMEWORK' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectjs, 'jsframework', null, 'value', 'text', $row->jsframework); ?>
	    </td>
            </tr>
      <?php if ($row->jsframework == 'auto' || $row->jsframework == 'jquery') {
      ?>
       <tr>
            <td class="key">
	    <label for="loadbootstyle">
	    <?php echo JText::_( 'COM_VIDEOFLOW_LOAD_BOOTSTRAPSTYLE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'loadbootstyle', null, 'value', 'text', $row->loadbootstyle); ?>
	    </td>
            </tr>
     <?php 
      }
      ?>
      <?php
      if ($row->jsframework == 'mootools') {
      ?>
            <tr>
            <td class="key">
	    <label for="mootools12">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MOO_LEGACY' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'mootools12', null, 'value', 'text', $row->mootools12); ?>
	    </td>
            </tr>
      <?php
      }
      ?>
	    <tr>
            <td class="key">
	    <label for="upsys">
	    <?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_SYS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->upsysselect, 'upsys', null, 'value', 'text', $row->upsys); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="mediadir">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_DIR' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="mediadir" value="<?php echo $row->mediadir; ?>" />        
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="autothumb">
	    <?php echo JText::_( 'COM_VIDEOFLOW_GEN_THUMB' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'autothumb', null, 'value', 'text', $row->autothumb); ?>
            </td>
            </tr>
	    <?php
	    if (!empty($row->autothumb)) {
	    if (empty($row->ffmpegdetected)) {
	    ?>
	    <tr>
            <td class="key">
	    <label for="ffmpegpath">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FFMPEG_PATH' ); ?>:
	    </label>
	    </td>
            <td>
	    <div style="float:left; margin-right: 5px; border: none; clear: none;">  
            <input type="text" size="30" maxsize="80" name="ffmpegpath" value="<?php echo $row->ffmpegpath; ?>" /> 
	    </div>
	    <?php
	    if (empty($row->ffmpegpath)) {
	    echo '<div style="float:left; clear: none; margin: 4px 0px 0px; border: none;"><font color="red">'.JText::_('COM_VIDEOFLOW_FFMPEG_WARN').'</font></div>';
	    }
	    ?>
	    </td>
            </tr>
	    <?php
	    }
	    ?>
	    <tr>
            <td class="key">
	    <label for="ffmpegsec">
	    <?php echo JText::_( 'COM_VIDEOFLOW_THUMB_POINT' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="ffmpegsec" value="<?php echo $row->ffmpegsec; ?>" /> 
	    </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="ffmpegthumbwidth">
	    <?php echo JText::_( 'COM_VIDEOFLOW_GENTHUMB_WIDTH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="ffmpegthumbwidth" value="<?php echo $row->ffmpegthumbwidth; ?>" /> 
	    </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="ffmpegthumbheight">
	    <?php echo JText::_( 'COM_VIDEOFLOW_GENTHUMB_HEIGHT' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="ffmpegthumbheight" value="<?php echo $row->ffmpegthumbheight; ?>" /> 
	    </td>
            </tr>
	    <?php
	    }
	    ?>
	    <tr>
            <td class="key">
	    <label for="commentsys">
	    <?php echo JText::_( 'COM_VIDEOFLOW_COMM_SYS' ); ?>:
	    </label>
	    </td>
            <td>
             <?php echo JHTML::_('select.genericlist', $row->selectcomsys, 'commentsys', null, 'value', 'text', $row->commentsys); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="fbcommentint">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FBCOMM_INT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->fbcommintselect, 'fbcommentint', null, 'value', 'text', $row->fbcommentint); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="likebutton">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOW_FBLIKE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'likebutton', null, 'value', 'text', $row->likebutton); ?>
            </td>
            </tr>
      <?php
      if ($row->likebutton) {
      ?>
      <tr>
            <td class="key">
	    <label for="fbsharecode">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FBCODE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectfbcode, 'fbsharecode', null, 'value', 'text', $row->fbsharecode); ?>
            </td>
            </tr>
       <?php
      }
      ?>
      <tr>
            <td class="key">
	    <label for="twitterbutton">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOW_TWITTER' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'twitterbutton', null, 'value', 'text', $row->twitterbutton); ?>
            </td>
            </tr>
       <?php
        if (!empty($row->twitterbutton)) {
        ?>
        <tr>
            <td class="key">
	    <label for="twitterhandle">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TWITTERHANDLE' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="twitterhandle" value="<?php echo $row->twitterhandle; ?>" />
            </td>
            </tr> 
        <tr>
            <td class="key">
	    <label for="hashtags">
	    <?php echo JText::_( 'COM_VIDEOFLOW_HASHTAGS' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="hashtags" value="<?php echo $row->hashtags; ?>" />
            </td>
            </tr> 
            <?php
        }
       ?>
      <tr>
            <td class="key">
	    <label for="addthis">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOW_ADDTHIS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'addthis', null, 'value', 'text', $row->addthis); ?>
            </td>
            </tr>
      <?php
      if ($row->addthis) {
      ?>
      <tr>
            <td class="key">
	    <label for="addthisid">
	    <?php echo JText::_( 'COM_VIDEOFLOW_ADDTHIS_PUBID' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="addthisid" value="<?php echo $row->addthisid; ?>" />
            </td>
            </tr>   
     <?php
      }      
     ?>       
     <tr>
            <td class="key">
	    <label for="adminemail">
	    <?php echo JText::_( 'COM_VIDEOFLOW_ADMIN_EMAIL' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="adminemail" value="<?php echo $row->adminemail; ?>" />
            </td>
            </tr>   
            <tr>
            <td class="key">
	    <label for="uploadlog">
	    <?php echo JText::_( 'COM_VIDEOFLOW_KEEP_LOG' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'uploadlog', null, 'value', 'text', $row->uploadlog); ?>
            </td>
            </tr>
          </table>
        </fieldset>   
        <fieldset class="adminform">
       	<legend><?php echo JText::_( 'COM_VIDEOFLOW_DISP_SETTINGS' ); ?></legend>
        <table class="admintable">
	    <tr>
            <td class="key">
	    <label for="findvmods">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DISP_MODE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->findmods, 'findvmods', null, 'value', 'text', $row->findvmods); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="limit">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PAGEITEMS' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="limit" value="<?php echo $row->limit; ?>" />
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="deflisting">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DEF_ORDER' ); ?>:
	    </label>
	    </td>
            <td>	      
            <?php echo JHTML::_('select.genericlist', $row->deflistingselect, 'deflisting', null, 'value', 'text', $row->deflisting); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="titlelimit">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TITLE_LENGTH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="titlelimit" value="<?php echo $row->titlelimit; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="shorttitle">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHORTT_LEN' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="shorttitle" value="<?php echo $row->shorttitle; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="commentlimit">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DESC_LENGTH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="commentlimit" value="<?php echo $row->commentlimit; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="repunderscore">
	    <?php echo JText::_( 'COM_VIDEOFLOW_REPLACE_US' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'repunderscore', null, 'value', 'text', $row->repunderscore); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="slist">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOW_SBAR' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'slist', null, 'value', 'text', $row->slist); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="slistlimit">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SBAR_ITEMS' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="slistlimit" value="<?php echo $row->slistlimit; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="thumbwidth">
	    <?php echo JText::_( 'COM_VIDEOFLOW_THUMBWIDTH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="thumbwidth" value="<?php echo $row->thumbwidth; ?>" />
            </td>
            </tr>            
            <tr>
            <td class="key">
	    <label for="thumbheight">
	    <?php echo JText::_( 'COM_VIDEOFLOW_THUMBHEIGHT' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="thumbheight" value="<?php echo $row->thumbheight; ?>" />
            </td>
            </tr> 
            
            <tr>
            <td class="key">
	    <label for="iconplay">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PLAYICON_OVERLAY' ); ?>:
	    </label>
	    </td>
            <td>
             <?php echo JHTML::_('select.radiolist', $row->bselect, 'iconplay', null, 'value', 'text', $row->iconplay); ?>
            </td>
            </tr> 
            <tr>
            <td class="key">
	    <label for="playicon">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PLAYICONSTYLE' ); ?>:
	    </label>
	    </td>
            <td>
             <?php echo JHTML::_('select.genericlist', $row->selectpicon, 'playicon', null, 'value', 'text', $row->playicon); ?>
            </td>
            </tr> 
                       
            <tr>
            <td class="key">
	    <label for="displayname">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DISPUSER' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectname, 'displayname', null, 'value', 'text', $row->displayname); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="shortname">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHORTUSER' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="shortname" value="<?php echo $row->shortname; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="showpro">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOW_PRO' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showpro', null, 'value', 'text', $row->showpro); ?>
            </td>
            </tr>            
	    <tr>
            <td class="key">
	    <label for="lightbox">
	    <?php echo JText::_( 'COM_VIDEOFLOW_USELBOX' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'lightbox', null, 'value', 'text', $row->lightbox); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="lighboxsys">
	    <?php echo JText::_( 'COM_VIDEOFLOW_LBOXSYS' ); ?>:
	    </label>
	    </td>
            <td>
             <?php echo JHTML::_('select.genericlist', $row->selectlbox, 'lightboxsys', null, 'value', 'text', $row->lightboxsys); ?>
            </td>
            </tr>
      <?php
      if ($row->lightboxsys == 'colorbox') {
      ?>     
            <tr>
            <td class="key">
	    <label for="cboxtheme">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CBOXTHEME' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectcbtheme, 'cboxtheme', null, 'value', 'text', $row->cboxtheme); ?>
            </td>
            </tr> 
       <tr>
            <td class="key">
	    <label for="iframecenter">
	    <?php echo JText::_( 'COM_VIDEOFLOW_IFRAMECENTRE' ); ?>:
	     </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'iframecentre', null, 'value', 'text', $row->iframecentre); ?>
            </td>
            </tr>       
      <?php
      }
      ?>      
            <tr>
            <td class="key">
	    <label for="lightboxfull">
	    <?php echo JText::_( 'COM_VIDEOFLOW_LBOXMODE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->lboxmode, 'lightboxfull', null, 'value', 'text', $row->lightboxfull); ?>
            </td>
            </tr>  
            <tr>
            <td class="key">
	    <label for="lboxh">
	    <?php echo JText::_( 'COM_VIDEOFLOW_LBOXH_OFFSET' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="lboxh" value="<?php echo $row->lboxh; ?>" />
            </td>
            </tr> 
            <tr>
            <td class="key">
	    <label for="lboxw">
	    <?php echo JText::_( 'COM_VIDEOFLOW_LBOXW_OFFSET' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="lboxw" value="<?php echo $row->lboxw; ?>" />
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="playall">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CONTPLAY' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'playall', null, 'value', 'text', $row->playall); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="catplay">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CATVIEW_MODE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->catmode, 'catplay', null, 'value', 'text', $row->catplay); ?>
            </td>
            </tr>   
	    <tr>
            <td class="key">
	    <label for="showcredit">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWCREDIT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showcredit', null, 'value', 'text', $row->showcredit); ?>
            </td>
            </tr> 
          </table> 
      </fieldset>
       <fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_VIDEOFLOW_PLAYER_SETTINGS' ); ?></legend>
          <table class="admintable">
            <tr>	
            <td class="key">
	    <label for="player">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PLAYER' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectplayer, 'player', null, 'value', 'text', $row->player); ?>
            </td>
            </tr>
            <?php if ($row->player == 'JW') {
            ?>
            <tr>	
            <td class="key">
	    <label for="jwplayerurl">
	    <?php echo JText::_( 'COM_VIDEOFLOW_HOSTEDJWP' ); ?>:
	    </label>
	    </td>
            <td>
             <input type="text" size="80" maxsize="150" name="jwplayerurl" value="<?php echo $row->jwplayerurl; ?>" />   
            </td>
            </tr>
            <?php
            }
            ?>
            <tr>	
            <td class="key">
	    <label for="jwforyoutube">
	    <?php echo JText::_( 'COM_VIDEOFLOW_YTUBE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'jwforyoutube', null, 'value', 'text', $row->jwforyoutube); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="skin">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PLAYER_SKIN' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="80" maxsize="150" name="skin" value="<?php echo $row->skin; ?>" />        
            </td>
            </tr>
            <tr>	
            <td class="key">
	    <label for="playerwidth">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PLAYERW' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="playerwidth" value="<?php echo $row->playerwidth; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="playerheight">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PLAYERH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="playerheight" value="<?php echo $row->playerheight; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="lplayerwidth">
	    <?php echo JText::_( 'COM_VIDEOFLOW_LPLAYERW' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="lplayerwidth" value="<?php echo $row->lplayerwidth; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="lplayerheight">
	    <?php echo JText::_( 'COM_VIDEOFLOW_LPLAYERH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="lplayerheight" value="<?php echo $row->lplayerheight; ?>" />
            </td>
            </tr>
             <tr>	
            <td class="key">
	    <label for="maxplayerwidth">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MAXPLAYERWIDTH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="maxplayerwidth" value="<?php echo $row->maxplayerwidth; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="vsources">
	    <?php echo JText::_( 'COM_VIDEOFLOW_VSOURCES' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo $row->vsources; ?>
            </td>
            </tr>
          </table>
        </fieldset>
        <fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_VIDEOFLOW_USR_SETTINGS' ); ?></legend>
          <table class="admintable">
            <tr>	
            <td class="key">
            <label for="ratings">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MRATE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'ratings', null, 'value', 'text', $row->ratings); ?>
            </td>
            </tr>
            <tr>	
            <td class="key">
            <label for="useradd">
	    <?php echo JText::_( 'COM_VIDEOFLOW_REMOTEADD' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'useradd', null, 'value', 'text', $row->useradd); ?>
            </td>
            </tr>
            <tr>	
            <td class="key">
            <label for="useradd">
	    <?php echo JText::_( 'COM_VIDEOFLOW_RAUTOPUB' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'autopubadds', null, 'value', 'text', $row->autopubadds); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
            <label for="userupload">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MUPLOAD' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'userupload', null, 'value', 'text', $row->userupload); ?>
            </td>
            </tr>
            <?php
            if ($row->userupload) {
            ?>
            <tr>
            <td class="key">
	    <label for="maxmedsize">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MMAX' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="maxmedsize" value="<?php echo $row->maxmedsize; ?>" />        
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="maxthumbsize">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TMAX' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="maxthumbsize" value="<?php echo $row->maxthumbsize; ?>" />        
            </td>
            </tr>
            <?php
            }
            ?>
            <tr>	
            <td class="key">
            <label for="useradd">
	    <?php echo JText::_( 'COM_VIDEOFLOW_UAUTOPUB' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'autopubups', null, 'value', 'text', $row->autopubups); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
            <label for="candelete">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DELOWNFILES' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'candelete', null, 'value', 'text', $row->candelete); ?>
            </td>
            </tr>
             <tr>
            <td class="key">
            <label for="useredit">
	    <?php echo JText::_( 'COM_VIDEOFLOW_EDITOWNFILES' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'useredit', null, 'value', 'text', $row->useredit); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
            <label for="downloads">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DLOAD' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'downloads', null, 'value', 'text', $row->downloads); ?>
            </td>
            </tr>
             <tr>
            <td class="key">
            <label for="downloadfree">
	    <?php echo JText::_( 'COM_VIDEDFLOW_FREEDOWNLOAD' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'downloadfree', null, 'value', 'text', $row->downloadfree); ?>
            </td>
            </tr>
          </table>
        </fieldset>
	
	<fieldset class="adminform">
	  <legend><?php echo JText::_( 'COM_VIDEOFLOW_TOOLS_SETTINGS' ); ?></legend>
          <table class="admintable">
	    <tr>
            <td class="key">
	    <label for="toolcolour">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TCOLOUR' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->tcolour, 'toolcolour', null, 'value', 'text', $row->toolcolour); ?>
            </td>
            </tr>
            <tr>	
            <td class="key">
            <label for="showadd">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWADD' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showadd', null, 'value', 'text', $row->showadd); ?>
            </td>
            </tr>
	    <tr>	
            <td class="key">
            <label for="showemail">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWEMAIL' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showemail', null, 'value', 'text', $row->showemail); ?>
            </td>
            </tr>
            <tr>	
            <td class="key">
            <label for="showshare">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWSHARE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showshare', null, 'value', 'text', $row->showshare); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
            <label for="showreport">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWFLAG' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showreport', null, 'value', 'text', $row->showreport); ?>
            </td>
            </tr>
          </table>
        </fieldset>
        
	
 <?php
   echo '</span>';
   if (version_compare(JVERSION, '3.0.0', 'lt')) {
   echo $vfTabs->endPanel();
   echo $vfTabs->startPanel( JText::_('COM_VIDEOFLOW_JOOMLA_SETTINGS'), 'tabtwo' );
   } else {
   echo JHtml::_('tabs.panel', JText::_('COM_VIDEOFLOW_JOOMLA_SETTINGS'), 'tabtwo');
   }
   echo '<span id="tab2" onClick="vftab = 1">';
  ?>
        <fieldset class="adminform">
       	<legend><?php echo JText::_( 'COM_VIDEOFLOW_SYS_SETTINGS' ); ?></legend>
        <table class="admintable">  
            <tr>
            <td class="key">
	    <label for="facebook">
	    <?php echo JText::_( 'COM_VIDEOFLOW_USEFBCONN' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'facebook', null, 'value', 'text', $row->facebook); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="message">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MCENTRE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'message', null, 'value', 'text', $row->message); ?>
            </td>
            </tr>
          </table> 
      </fieldset>
      
        <fieldset class="adminform">
       	<legend><?php echo JText::_( 'COM_VIDEOFLOW_DISP_SETTINGS' ); ?></legend>
        <table class="admintable">        
            <tr>
            <td class="key">
	    <label for="jtemplate">
	    <?php echo JText::_( 'COM_VIDEOFLOW_JTEMP' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectjtemp, 'jtemplate', null, 'value', 'text', $row->jtemplate); ?>
            </td>
            </tr>
            <?php if ($row->jtemplate == 'grid') {
            ?>
            <tr>	
            <td class="key">
	    <label for="columns">
	    <?php echo JText::_( 'COM_VIDEOFLOW_NCOLUMNS' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="columns" value="<?php echo $row->columns; ?>" />
            </td>
            </tr>
            <?php
            }
            ?>
	    <tr>  
            <td class="key">
	    <label for="showtabs">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWTABS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showtabs', null, 'value', 'text', $row->showtabs); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="sidebarlimit">
	    <?php echo JText::_( 'COM_VIDEOFLOW_NTABITEMS' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="5" maxsize="10" name="sidebarlimit" value="<?php echo $row->sidebarlimit; ?>" />
            </td>
            </tr>   
            <tr>
            <td class="key">
	    <label for="showuser">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWUSR' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showuser', null, 'value', 'text', $row->showuser); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="showviews">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWPCOUNT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showviews', null, 'value', 'text', $row->showviews); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="showplaylistcount">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWPLISTCOUNT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showplaylistcount', null, 'value', 'text', $row->showplaylistcount); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="showrating">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWRATING' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showrating', null, 'value', 'text', $row->showrating); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="showvotes">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWVOTES' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showvotes', null, 'value', 'text', $row->showvotes); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="showdownloads">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWDLOADS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showdownloads', null, 'value', 'text', $row->showdownloads); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="showdate">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWDATE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showdate', null, 'value', 'text', $row->showdate); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="showcat">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWCAT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showcat', null, 'value', 'text', $row->showcat); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="flowid">
	    <?php echo JText::_( 'COM_VIDEOFLOW_ITEMID' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="5" maxsize="10" name="flowid" value="<?php echo $row->flowid; ?>" />
            </td>
            </tr>   
        </table> 
      </fieldset>
      
       <fieldset class="adminform">
	  <legend><?php echo JText::_( 'COM_VIDEOFLOW_MENU_SETTINGS' ); ?></legend>
          <table class="admintable">
            <tr>	
            <td class="key">
            <label for="menu">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MACTIVE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo $row->jmenu; ?>
            </td>
	    <tr>	
            <td class="key">
            <label for="menuorder">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MORDER' ); ?>:
	    </label>
	    </td>
            <td>
            <a href="index.php?option=com_videoflow&c=config&task=morder&m=jmenu" onClick="this.form.submit()"><?php echo JText::_('COM_VIDEOFLOW_REORDER'); ?></a>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="helpid">
	    <?php echo JText::_( 'COM_VIDEOFLOW_HELPID' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="helpid" value="<?php echo $row->helpid; ?>" />        
            </td>
            </tr>
          </table>
        </fieldset>
        <fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_VIDEOFLOW_SYSINFO' ); ?></legend>
          <table class="admintable">
            <tr>	
            <td class="key">
            <label for="xmlview">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SITEMAP' ); ?>:
	    </label>
	    </td>
            <td>
            <a href="<?php echo JURI::root().'index.php?option=com_videoflow&view=xml&format=raw'; ?>" target="_blank"><?php echo JURI::root().'index.php?option=com_videoflow&view=xml&format=raw'; ?></a>
            </td>
            </tr>
          </table>
        </fieldset>
 <?php
  echo '</span>';
  if (version_compare(JVERSION, '3.0.0', 'lt')) {
  echo $vfTabs->endPanel();
  echo $vfTabs->startPanel( JText::_('COM_VIDEOFLOW_FB_SETTINGS'), 'tabthree' );
  } else {
  echo JHtml::_('tabs.panel', JText::_('COM_VIDEOFLOW_FB_SETTINGS'), 'tabthree');
  }
  echo '<span id="tab3" onClick="vftab = 2">';
  ?>
        <fieldset class="adminform">
       	<legend><?php echo JText::_( 'COM_VIDEOFLOW_SYS_SETTINGS' ); ?></legend>
        <table class="admintable">        
            <tr>
            <td class="key">
	    <label for="fbkey">
	    <?php echo JText::_( 'COM_VIDEOFLOW_APPID' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="fbkey" value="<?php echo $row->fbkey; ?>" />
            </td>
            </tr>  
            <tr>
            <td class="key">
	    <label for="fbsecret">
	    <?php echo JText::_( 'COM_VIDEOFLOW_APPSECRET' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="fbsecret" value="<?php echo $row->fbsecret; ?>" />
            </td>
            </tr>   
            <tr>
            <td class="key">
	    <label for="appname">
	    <?php echo JText::_( 'COM_VIDEOFLOW_APPNAME' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="30" maxsize="80" name="appname" value="<?php echo $row->appname; ?>" />
            </td>
            </tr>   
            <tr>
            <td class="key">
	    <label for="canvasurl">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CANVAS' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="canvasurl" value="<?php echo $row->canvasurl; ?>" />
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="canvasheight">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CANVASH' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="canvasheight" value="<?php echo $row->canvasheight; ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="fbcomments">
	    <?php echo JText::_( 'COM_VIDEOFLOW_USECOMM' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbcomments', null, 'value', 'text', $row->fbcomments); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="wallposts">
	    <?php echo JText::_('COM_VIDEOFLOW_GENFPOSTS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'wallposts', null, 'value', 'text', $row->wallposts); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="bwallposts">
	    <?php echo JText::_('COM_VIDEOFLOW_GENBPOSTS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'bwallposts', null, 'value', 'text', $row->bwallposts); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="profile_id">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FBADMINID' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="profile_id" value="<?php echo $row->profile_id; ?>" />
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="fanpage_id">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CPAGEID' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="fanpage_id" value="<?php echo $row->fanpage_id; ?>" />
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="fanpage_url">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CPAGEURL' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="80" maxsize="150" name="fanpage_url" value="<?php echo $row->fanpage_url; ?>" />
	    &nbsp;<a href="<?php echo JRoute::_('index.php?option=com_videoflow&c=config&task=findurl'); ?>"><?php echo JText::_('COM_VIDEOFLOW_URLRETRIEVE'); ?></a>	    
            </td>
            </tr>
	    <tr>
	    <td class="key">
	    <label for="fb_sesskey">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SESSKEY' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="80" maxsize="150" name="fb_sesskey" value="<?php echo $row->fb_sesskey; ?>" />
	    <?php
	    if (version_compare(JVERSION, '2.5.0', 'lt')) {
	    jimport( 'joomla.utilities.utility' );
	    $sess = JUtility::getToken();
	    } else {
	    $session = JFactory::getSession();
	    $sess = $session->getName().'='.$session->getId().'&'.$session->getFormToken();
	    }
	    $redir = urlencode(JURI::current().'?option=com_videoflow&c=config&task=gentok&'.$sess.'=1&fk=1');
	    $url = "https://graph.facebook.com/oauth/authorize?client_id=$row->fbkey&scope=offline_access,publish_stream,manage_pages&redirect_uri=$redir";
	    ?>
	    &nbsp;<a href="<?php echo $url; ?>"><?php echo JText::_('COM_VIDEOFLOW_GENSESSKEY'); ?></a>
            </td>
            </tr>
           </table> 
      </fieldset>
      
        <fieldset class="adminform">
       	<legend><?php echo JText::_( 'COM_VIDEOFLOW_DISP_SETTINGS' ); ?></legend>
        <table class="admintable">        
            <tr>
            <td class="key">
	    <label for="ftemplate">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FBTEMP' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.genericlist', $row->selectftemp, 'ftemplate', null, 'value', 'text', $row->ftemplate); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="dashboard">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWDASH' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'dashboard', null, 'value', 'text', $row->dashboard); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="fbshowmylist">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWADD' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbshowmylist', null, 'value', 'text', $row->fbshowmylist); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="fbshowuser">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWUSR' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbshowuser', null, 'value', 'text', $row->fbshowuser); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="fbshowviews">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWPCOUNT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbshowviews', null, 'value', 'text', $row->fbshowviews); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="fbshowplaylists">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWPLISTCOUNT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbshowplaylists', null, 'value', 'text', $row->fbshowplaylists); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="fbshowrating">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWRATING' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbshowrating', null, 'value', 'text', $row->fbshowrating); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="fshowvotes">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWVOTES' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fshowvotes', null, 'value', 'text', $row->fshowvotes); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="fshowdownloads">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWDLOADS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fshowdownloads', null, 'value', 'text', $row->fshowdownloads); ?>
            </td>
            </tr>
	    <tr>
            <td class="key">
	    <label for="fbshowdate">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWDATE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbshowdate', null, 'value', 'text', $row->fbshowdate); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="fbshowcategory">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWCAT' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'fbshowcategory', null, 'value', 'text', $row->fbshowcategory); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="showfull">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SHOWJARTS' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.radiolist', $row->bselect, 'showfull', null, 'value', 'text', $row->showfull); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="ncatid">
	    <?php echo JText::_( 'COM_VIDEOFLOW_ARTCATS' ); ?>:
	    </label>
	    </td>
            <td>
           <input type="text" size="80" maxsize="150" name="ncatid" value="<?php echo $row->ncatid; ?>" />
            </td>
            </tr> 
            <tr>
            <td class="key">
	    <label for="fbhelpid">
	    <?php echo JText::_( 'COM_VIDEOFLOW_HELPID' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="5" maxsize="10" name="fbhelpid" value="<?php echo $row->fbhelpid; ?>" />        
            </td>
            </tr>
          </table> 
      </fieldset>
      
       <fieldset class="adminform">
	  <legend><?php echo JText::_( 'COM_VIDEOFLOW_MENU_SETTINGS' ); ?></legend>
          <table class="admintable">
            <tr>	
            <td class="key">
            <label for="menu">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MACTIVE' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo $row->fmenu; ?>
            </td>
            </tr>
	    <tr>	
            <td class="key">
            <label for="fbmenuorder">
	    <?php echo JText::_( 'COM_VIDEOFLOW_MORDER' ); ?>:
	    </label>
	    </td>
            <td>
            <a href="index.php?option=com_videoflow&c=config&task=morder&m=fbmenu" onClick="this.form.submit()"><?php echo JText::_('COM_VIDEOFLOW_REORDER'); ?></a>
            </td>
            </tr>
          </table>
        </fieldset>
        
          <input type="hidden" name="fid" value="<?php echo $row->fid; ?>" />
          <input type="hidden" name="option" value="com_videoflow" />
          <input type="hidden" name="task" value="save" />
          <input type="hidden" name="vtab" value="" />	
          <input type="hidden" name="c" value="config" />
          <input type="hidden" name="helplink" value="54#edit" />
  <?php echo JHTML::_( 'form.token' ); ?>
  
 <?php
  echo '</span>';
  if (version_compare(JVERSION, '3.0.0', 'lt')) {
  echo $vfTabs->endPanel();
  echo $vfTabs->startPanel( JText::_('COM_VIDEOFLOW_PROUPDATES'), 'tabfour' );
  } else {
  echo JHtml::_('tabs.panel', JText::_('COM_VIDEOFLOW_PROUPDATES'), 'tabfour');
  }
  echo '<span id="tab4" onClick="vftab = 3">';
  if (!$row->prostatus){
  $advisory = JText::_('COM_VIDEOFLOW_PROREQUIRED');
  $upnow = JText::_('COM_VIDEOFLOW_UPGRADENOW');
  $uplink = JRoute::_('index.php?option=com_videoflow&c=upgrade');
  echo "<br />".$advisory." <a href='$uplink'>$upnow</a><br /><br />";
  }   
  
  ?>		
      <table class="adminlist table-striped">
	<thead>
	<tr>
	<th width="10">
	<?php echo JText::_( 'COM_VIDEOFLOW_NUM' ); ?>
	</th>
        <th width="10%">
	<?php echo JText::_( 'COM_VIDEOFLOW_NAME' ); ?>
	</th>
        <th width="10%" nowrap="nowrap" class="title">
	<?php echo JText::_( 'COM_VIDEOFLOW_TYPE' ); ?>
	</th>
	<th nowrap="nowrap">
	<?php echo JText::_( 'COM_VIDEOFLOW_DESC' ); ?>
	</th>
	<th width="10%" nowrap="nowrap">
	<?php echo JText::_( 'COM_VIDEOFLOW_PFORM' ); ?>
	</th>
        <th width="10%" nowrap="nowrap">
	<?php echo JText::_( 'COM_VIDEOFLOW_STATUS' ); ?>
	</th>
	<th width="10%" nowrap="nowrap">
	<?php echo JText::_( 'COM_VIDEOFLOW_ACTION' ); ?>
	</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
	<td colspan="12">
	</td>
	</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	$i = 1;
        foreach($row->proadds as $vrow) {	
        if ($vrow->status == 1) {
        $vstatus = JText::_('COM_VIDEOFLOW_INSTALLED');
        $vlink = JRoute::_('index.php?option=com_videoflow&c=upgrade&task=autoupdate&action=remove&aid='.$vrow->id.'&aname='.$vrow->name);
        $vdisp = JText::_('COM_VIDEOFLOW_REMOVE');
        } else {
        $vstatus = JText::_('COM_VIDEOFLOW_NOTINSTALLED');
        $vlink = JRoute::_('index.php?option=com_videoflow&c=upgrade&task=autoupdate&action=install&aid='.$vrow->id.'&aname='.$vrow->name);
        $vdisp = JText::_('COM_VIDEOFLOW_INSTALL');
        }
        $vaction = '<a href="'.$vlink.'">'.$vdisp.'</a>'; 
        if (!$row->prostatus) $vaction = $vdisp;			        		    
	?>
	<tr class="<?php echo "row$k"; ?>">
	<td align="center">
	<?php echo $i++; ?>
	</td>
	<td>
	<?php echo $vrow->propername; ?>
	</td>
        <td align="center">
	<?php echo $vrow->type; ?>
	</td>
	<td>
	<?php echo $vrow->desc;?>
	</td>
	<td align="center">
	<?php echo $vrow->platform;?>
	</td>
	<td align="center">
	<?php echo $vstatus;?>
	</td>
	<td align="center">
	<?php echo $vaction;?>
	</td>
        </tr>
	<?php
	$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
       
 <?php
   
  echo '</span>';
  if (version_compare(JVERSION, '3.0.0', 'lt')) {
  echo $vfTabs->endPanel();
  echo $vfTabs->startPanel( JText::_('COM_VIDEOFLOW_SYSINFO'), 'tabfive' );
  } else {
  echo JHtml::_('tabs.panel', JText::_('COM_VIDEOFLOW_SYSINFO'), 'tabfive');
  }
  echo '<span id="tab5" onClick="vftab = 4">';
  ?>
      
       <fieldset class="adminform">
          <table class="admintable">
            <tr>	
            <td class="key">
            <label for="vversion">
	    <?php echo JText::_( 'COM_VIDEFLOW_VFVERSION' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo $row->version.' '.$type; ?>
            </td>
            </tr>
	    <tr>	
            <td class="key">
            <label for="hostdomain">
	    <?php echo JText::_( 'COM_VIDEOFLOW_HDOMAIN' ); ?>:
	    </label>
	    </td>
            <td>
	    <?php
	    $h = JURI::getInstance();
	    echo $h->getHost();
	    ?>  
            </td>
            </tr>
	    <?php if (!empty ($row->prostatus)) {
	    ?>
	    <tr>	
            <td class="key">
            <label for="upgrade">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PRODATE' ); ?>:
	    </label>
	    </td>
            <td>
	    <?php echo $row->vdate; ?>
            </td>
            </tr>
	    <tr>	
            <td class="key">
            <label for="reset">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PRORESET' ); ?>:
	    </label>
	    </td>
            <td>
	    <?php 
	    echo JText::_('COM_VIDEOFLOW_RESETINFO');
	    echo "<a href='index.php?option=com_videoflow&c=upgrade&task=resetpro&version=$row->version&vcode=$row->fkey&prostatus=$row->prostatus'> ". JText::_('COM_VIDEOFLOW_RESETCONT')."</a>";
	    ?>  
            </td>
            </tr>
	    <?php
	    }
	    ?>
          </table>
        </fieldset>       
 <?php
 echo '</span>';
 if (version_compare(JVERSION, '3.0.0', 'lt')) {
  echo $vfTabs->endPanel();
  echo $vfTabs->startPanel( JText::_('COM_VIDEOFLOW_ABOUT'), 'tabsix' );
  } else {
  echo JHtml::_('tabs.panel', JText::_('COM_VIDEOFLOW_ABOUT'), 'tabsix');
  }
  echo '<span id="tab6" onClick="vftab = 5">';
  ?>
      
       <fieldset class="adminform">
          <table class="admintable">
            <tr>	
            <td>
           	<?php echo $row->vcredit; ?>
            </td>
            </tr>
         </table>
        </fieldset>

       
 <?php
 echo '</span>';
 if (version_compare(JVERSION, '3.0.0', 'lt')) {
  echo $vfTabs->endPanel();
  echo $vfTabs->endPane();
  } else {
  echo JHtml::_('tabs.end'); 
  }
 ?>
        <br />
 <?php
    if ($row->message) {
    ?>       
        <fieldset class="adminform">
          <legend><?php echo JText::_( 'COM_VIDEOFLOW_MESSAGE_CENTRE' ); ?></legend>
          <table class="admintable">
            <tr>	
            <td>
            <?php echo $row->msg;?>
            </td>
           </tr>
          </table>
        </fieldset>
  <?php
      }
      $hrefcond = '<a href="'.JRoute::_('index.php?option=com_videoflow&c=config&task=terms&format=raw').'" class="modal-vfpop" rel="{handler: \'iframe\', size: {x: 725, y: 520}}">';
      $hrefend = '</a>';
   ?>     
      <fieldset class="adminform">
          <table class="admintable" align="center">
            <tr>	
            <td align="center"><?php echo sprintf( JText::_('COM_VIDEOFLOW_ACCEPTCONDS'), $hrefcond, $hrefend); ?> 
            <br />
            <br />
            <a href="http://www.videoflow.tv" target="_blank">VideoFlow</a> <?php echo $row->version.' '.$type;?> 
            <br />
           Copyright: 2008 - 2013 <a href="mailto: fideri@fidsoft.com"> Kirungi F. Fideri</a><br /><a href="http://www.fidsoft.com" target="_blank">fidsoft.com</a>
            </td>
            </tr>
          </table>
        </fieldset>
      </div>
	</div>
  </form>
  <div class="clr"></div>
<?php 
}

function donate(){
global $vparams;
JToolbarHelper::title( JText::_('COM_VIDEOFLOW_UPGRADE_MANAGER'));
if (empty($vparams->fkey)) $vc = mt_rand (100000,999999); else $vc = $vparams->fkey;
$h = JURI::getInstance();
$custom = '120'.$vc.$h->getHost();
$uplink = 'index.php?option=com_videoflow&c=upgrade&task=processpro&vcode='.$vc;
$hrefcond = '<a href="'.JRoute::_('index.php?option=com_videoflow&c=config&task=terms&format=raw').'" class="modal-vfpop" rel="{handler: \'iframe\', size: {x: 725, y: 520}}">';
$hrefend = '</a>';
if ($vparams->prostatus) $type = 'Pro'; else $type = 'Standard';
?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<div id="j-sidebar-container" class="span2">
<?php echo JHtmlSidebar::render(); ?>
</div>
<div id="j-main-container" class="span10">
<div class="clearfix"> </div>
<fieldset class="adminform">
<table class="admintable">
<tr><td>
<?php echo JText::_( 'COM_VIDEOFLOW_PROUPGRADE_PROCESS' ); ?>
<b>STEP 1:</b> Click the PayPal button below to pay <b>US $30 (thirty United States dollars)</b> for a one year subscription or to renew your subscription.<br/><br/>
During your subscription period, all new plugins, modules and software updates are free.<br />
Your installed VideoFlow software continues to work in pro mode without any limitations when your subscription expires.<br/>
Each subscription is for only ONE domain and TWO subdomains. <br/>
<a href="http://videoflow.fidsoft.com/index.php?option=com_content&view=article&id=66" target="_blank">Read the terms of subscription</a> before you upgrade.<br/><br/>
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PRX6Y33VSEJEU">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="custom" value="<?php echo $custom; ?>"> 
<div style="clear:both;"></div>
<br/>
<b>STEP 2:</b> After payment, <a href="<?php echo $uplink; ?>">click here to complete the upgrade process</a>.<br/><br/> 
You can <a href="index.php?option=com_videoflow&c=upgrade&task=processpro">try out the pro version without payment</a> for seven days. Pro templates cannot be installed on the trial version.<br/><br/>
A non-exhaustive comparison between Pro and Standard versions is <a href="http://videoflow.fidsoft.com/index.php?option=com_content&view=article&id=62" target="_blank">available here.</a><br/><br/>
</td></tr>
</table>
<table class="admintable" align="center">
<tr>	
<td align="center"><?php echo sprintf( JText::_('COM_VIDEOFLOW_ACCEPTCONDS'), $hrefcond, $hrefend); ?> 
<br />
<br />
<a href="http://www.videoflow.tv" target="_blank">VideoFlow</a> <?php echo $vparams->version.' '.$type;?> 
<br />
Copyright: 2008 - 2013 <a href="mailto: fideri@fidsoft.com"> Kirungi F. Fideri</a><br /><a href="http://www.fidsoft.com" target="_blank">fidsoft.com</a>
</td>
</tr>
</table>
</fieldset>
</div>
</form>
</div>
</form>
<?php
}
}