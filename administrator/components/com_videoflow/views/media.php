<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.0 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no liability arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$doc = JFactory::getDocument();
$fupload = JURI::root().'components/com_videoflow/utilities/js/fupload.js';
$doc->addScript($fupload);
//JHtml::_('behavior.framework');
//JHtml::_('bootstrap.tooltip');
//JHtml::_('behavior.multiselect');
//JHtml::_('dropdown.init');
//JHtml::_('formbehavior.chosen', 'select');

class VideoflowViewMedia extends JViewLegacy
{
    
  static function setMediaToolbar()
  {
    $tbar= JToolBar::getInstance( 'toolbar' );
    JToolbarHelper::title( JText::_('COM_VIDEOFLOW_MEDIA_MANAGER'));
    JToolBarHelper::addNew();
    $tbar->appendButton( 'Standard', 'upload', JText::_( 'COM_VIDEOFLOW_UPLOAD' ), 'vfupload', false);
    $tbar->appendButton( 'Standard', 'edit', JText::_( 'COM_VIDEOFLOW_EMBED' ), 'vfembed', false);
    $tbar->appendButton( 'Standard', 'featured', JText::_( 'COM_VIDEOFLOW_FEATURE' ), 'recommend', true);
    $tbar->appendButton( 'Standard', 'unpublish', JText::_( 'COM_VIDEOFLOW_UNFEATURE' ), 'unrecommend', true);
    JToolBarHelper::publishList();
    JToolBarHelper::unpublishList();
    $tbar->appendButton( 'Standard', 'trash', JText::_( 'COM_VIDEOFLOW_CLEAN' ), 'cleanup', false);
    JToolBarHelper::deleteList();  
    $tbar->appendButton( 'Help', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=61', 725, 480 );
  }
  
  static function setCleanupToolbar()
  {
    $tbar= JToolBar::getInstance( 'toolbar' );
    JToolbarHelper::title( JText::_('COM_VIDEOFLOW_MEDIA_MANAGER'));
    $tbar->appendButton( 'Standard', 'home', JText::_( 'COM_VIDEOFLOW_HOME' ), 'display', false);
    JToolBarHelper::deleteList();
    JToolBarHelper::cancel( 'cancel' );
    $tbar->appendButton( 'Help', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=61', 725, 480 );
  }

  function listMedia( &$rows, &$pageNav, &$lists, &$clist)
  {
    global $vparams;
    
   JHtml::_('formbehavior.chosen', 'select');
   JHTML::_('behavior.tooltip');
   $ordering = ($lists['order'] == 'b.ordering');
   $task = JRequest::getCmd('task', '');
    if ($task == 'cleanup') self::setCleanupToolbar(); else self::setMediaToolbar();
    $user = JFactory::getUser();
    if (version_compare(JVERSION, '1.6.0') < 0) {
    $imgpath = 'images/';
    } else {
    $app = JFactory::getApplication();
    $activetemp = $app->getTemplate();
    $imgpath = 'templates/'.$activetemp.'/images/admin/';  
    }
    $vsidebar = JHtmlSidebar::render();
    ?>
  <form action="index.php?option=com_videoflow" method="post" name="adminForm" id="adminForm">
  <div>
  <?php
  if (!empty ($vsidebar)) {
  ?>  
  <div id="j-sidebar-container" class="span2">
  <?php echo $vsidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
  <?php
  } else {
  ?>
  <div id="j-main-container">
  <?php
  }  
  ?>
    <div id="filter-bar" class="btn-toolbar">
      <div class="filter-search btn-group pull-left">
	<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE');?></label>
	<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
      </div>
      <div class="btn-group pull-left">
	<button type="submit" name="vffilter" onclick="this.form.submit();" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
	<button type="button" name="vffilter_clear" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.getElementById('filter_media_type').value='';this.form.getElementById('filter_cat').value='';this.form.getElementById('filter_server').value='';this.form.getElementById('filter_featured_state').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><i class="icon-remove"></i></button>	
      </div>
      <div class="btn-group pull-right hidden-phone">
	<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
	<?php echo $pageNav->getLimitBox(); ?>
      </div>
    </div>
    <div class="clearfix"> </div>    
    <table class="adminlist table-striped" cellpadding=2>
    <thead>
      <tr style="padding-bottom:50px;">
	<th width="10">
	<?php echo JText::_( 'COM_VIDEOFLOW_NUM' ); ?>
	</th>
	<th width="15">
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
	<th width="74">
	<?php echo JText::_( 'COM_VIDEOFLOW_IMAGE' ); ?>
	</th>
        <th nowrap="nowrap" class="title">
	<?php echo JHTML::_('grid.sort',  JText::_('COM_VIDEOFLOW_COLUMN_TITLE'), 'b.title', @$lists['order_Dir'], @$lists['order'] ); ?>
	</th>
        <th width="10%" nowrap="nowrap">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_CATEGORY'), 'b.cat', @$lists['order_Dir'], @$lists['order'] ); ?>
	</th>
	<th width="8%" nowrap="nowrap">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_DATE'), 'b.dateadded', @$lists['order_Dir'], @$lists['order'] ); ?>
	</th>
        <th width="5%" nowrap="nowrap">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_FEATURED'), 'b.recommended', @$lists['order_Dir'], @$lists['order'] ); ?>
	</th>
	<th width="60">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_VIEWS'), 'b.views', @$lists['order_Dir'], @$lists['order'] ); ?>
	</th>
	<th width="5%" nowrap="nowrap">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_PUBLISHED'), 'b.published', @$lists['order_Dir'], @$lists['order'] ); ?>
	</th>
	<th width="14%" nowrap="nowrap" valign="middle">
	<div class="vfmidalign">
	<div class="vfmidalign" style="display:inline-block;">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_ORDER'), 'b.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
	</div>
	<div class="vfmidalign" style="display:inline-block;">
	<?php if ($ordering) echo JHTML::_('grid.order',  $rows ); ?>
	</div>
	</div>
	</th> 
        <th width="1%" nowrap="nowrap">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_ID'), 'b.id', @$lists['order_Dir'], @$lists['order'] ); ?>
	</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
	<td colspan="12">
	<div style="margin-top: 10px;">  
	<?php echo $pageNav->getListFooter(); ?>
	</div>
	</td>
      </tr>
    </tfoot>
    <tbody>
    <?php
    $k = 0;
    $cdate = JFactory::getDate();
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
	$row = &$rows[$i];
      if($row->dateadded == '0000-00-00 00:00:00') $row->dateadded = $cdate->toFormat();
	$link = JRoute::_( 'index.php?option=com_videoflow&task=edit&cid[]='. $row->id );
	$published = JHTML::_('grid.published', $row, $i );
	$checked = JHTML::_('grid.id', $i, $row -> id );      
      if (!empty($row->pixlink)) {
         if (stripos($row->pixlink, 'http://') === FALSE) {  
         $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->pixlink;
         } else {
         $pixpreview = $row->pixlink;
         }
       } else if (empty($row->pixlink) && file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$row->title.'.jpg')){
       $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->title.'.jpg';
       } else if($row->type == 'jpg' || $row->type == 'gif' || $row->type == 'png' ) {
	   $pixpreview = self::getThumb($row);
	   } else {
      $pixpreview = JURI::root().'components/com_videoflow/players/vflow.png';
      }
      if ($row->recommended > 0 ){
        $rec = 'unrecommend';
        } else {
        $rec = 'recommend';
        }
      if ($row->published == 0 ){
        $pub = 'publish';
        } elseif ($row->published == 1)  {
        $pub = 'unpublish';
        } else {
        $pub = '';
        } 
	?>
      <tr class="<?php echo "row$k"; ?>">
	<td align="center">
	<?php echo $pageNav->getRowOffset($i); ?>
	</td>
	<td align="center">
	<?php echo $checked; ?>
	</td>
	<td align="center">
	<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_VIDEOFLOW_EDIT' );?>::<?php echo $row->title; ?>"><a href="<?php echo $link; ?>"> <img src="<?php echo $pixpreview; ?>" width=70 /></a></span>
	</td>
        <td>
	<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_VIDEOFLOW_EDIT' );?>::<?php echo $row->title; ?>">
	<a href="<?php echo $link; ?>"> <?php echo $row->title; ?></a>
	</span>
	</td>
	<td align="center">
	<?php echo $clist[$row->cat]->name;?>
	</td>
	<td align="center">
	<?php echo JHTML::_('date', $row->dateadded, JText::_('DATE_FORMAT_LC4')); ?>
	</td>
        <td align="center">
	<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $rec; ?>')" title="<?php echo ( $row->recommended ) ? JText::_( 'JYES' ) : JText::_( 'JNO' );?>">
	<img src="<?php echo $imgpath;?><?php echo ( $row->recommended ) ? 'featured.png' : 'publish_x.png' ;?>" width="16" height="16" border="0" alt="<?php echo ( $row->recommended ) ? JText::_( 'JYES' ) : JText::_( 'JNO' );?>" /></a>
	</td>
	<td align="center">
	<?php echo $row->views;?>
	</td>
	<td align="center">
	<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $pub; ?>')" title="<?php echo ( $row->published == 1) ? JText::_( 'JYES' ) : JText::_( 'JNO' );?>">
	<img src="<?php echo $imgpath;?><?php echo ( $row->published == 1 ) ? 'tick.png' : ( $row->published == 0 ? 'publish_x.png' : 'disabled.png' ) ;?>" width="16" height="16" border="0" alt="<?php echo ( $row->published ) ? JText::_( 'JYES' ) : JText::_( 'JNO' );?>" /></a>
	</td>
        <td align="center">
        <?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
	<div><?php echo $pageNav->orderUpIcon( $i, true, 'orderup', JText::_('COM_VIDEOFLOW_MOVEUP'), true); ?><?php echo $pageNav->orderDownIcon( $i, $n, true, 'orderdown', JText::_('COM_VIDEOFLOW_MOVEDOWN'), true); ?></div>
	<div><input type="text" name="order[]" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="vfinput" /></div>							
	</td>
	<td align="center">
	<?php echo $row->id; ?>
	</td>
      </tr>
      <?php
      $k = 1 - $k;
      }
      ?>
    </tbody>
    </table>
    <input type="hidden" name="c" value="media" />
    <input type="hidden" name="option" value="com_videoflow" />
    <input type="hidden" name="ctask" value="<?php echo $task; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
    <?php echo $this-> printFooter(); ?>
  </div>
  </div>
  </form>
    <?php
    }

  
  static function setCatToolbar()
  {
    $tbar= JToolBar::getInstance( 'toolbar' );
    JToolbarHelper::title( JText::_('COM_VIDEOFLOW_CAT_MANAGER'));
    JToolBarHelper::addNew('addcat', JText::_( 'COM_VIDEOFLOW_NEW' ));
    JToolBarHelper::editList('editcat', JText::_( 'COM_VIDEOFLOW_EDIT' ));
    JToolBarHelper::custom( 'deletecat', 'delete.png', 'delete_f2.png', JText::_( 'COM_VIDEOFLOW_DELETE' ) );
    $tbar->appendButton( 'Help', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=61', 725, 520 );
  }
  
  
  function listCats( &$rows, &$pageNav, &$lists)
  {
    global $vparams;
    JHtml::_('formbehavior.chosen', 'select');
    $ordering = ($lists['order'] == 'v.ordering');
    VideoflowViewMedia::setCatToolbar();
    JHTML::_('behavior.tooltip');
    JHTML::_('behavior.modal', 'a.modal-vfpop');
    ?>
  <form action="index.php?option=com_videoflow" method="post" name="adminForm" id="adminForm">
  <div>
  <div id="j-sidebar-container" class="span2">
  <?php
  echo JHtmlSidebar::render();
  ?>
  </div>
  <div id="j-main-container" class="span10">
  <div id="filter-bar" class="btn-toolbar">
        <div class="btn-group pull-right hidden-phone">
	<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
	<?php echo $pageNav->getLimitBox(); ?>
        </div>
  </div>
  <div class="clearfix"></div>
    <table cellpadding=2 class="adminlist table-striped" style="width:100%">
    <thead>
    <tr>
    <th width="10">
    <?php echo JText::_( 'COM_VIDEOFLOW_NUM' ); ?>
    </th>
    <th width="20">
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
    <th width="100">
    <?php echo JText::_( 'COM_VIDEOFLOW_IMAGE' ); ?>
    </th>
    <th nowrap="nowrap" class="name">
    <?php echo JHTML::_('grid.sort',  JText::_('COM_VIDEOFLOW_COLUMN_NAME'), 'v.name', @$lists['order_Dir'], @$lists['order'], 'categorylist' ); ?>
    </th>
    <th nowrap="nowrap">
    <?php echo JText::_( 'COM_VIDEOFLOW_DESCRIPTION' ); ?>
    </th>
    <th width="10%" nowrap="nowrap">
      <div class="vfmidalign">
	<div class="vfmidalign" style="display:inline-block;">
	<?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_ORDER'), 'v.ordering', @$lists['order_Dir'], @$lists['order'], 'categorylist' ); ?>
	</div>
	<div class="vfmidalign" style="display:inline-block;">
	<?php if ($ordering) echo JHTML::_('grid.order',  $rows ); ?>
	</div>
      </div>
    </th>
    <th width="15%" nowrap="nowrap">
    <?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_DATE'), 'v.date', @$lists['order_Dir'], @$lists['order'] , 'categorylist'); ?>
    </th>
    <th width="8%" nowrap="nowrap">
    <?php echo JHTML::_('grid.sort',   JText::_('COM_VIDEOFLOW_COLUMN_ID'), 'v.id', @$lists['order_Dir'], @$lists['order'], 'categorylist' ); ?>
    </th>
    </tr>
    </thead>
    <tfoot>
    <tr>
    <td colspan="8">
    <div style="margin-top: 10px;">  
    <?php echo $pageNav->getListFooter(); ?>
    </div>
    </td>
    </tr>
    </tfoot>
    <tbody>
    <?php
    $k = 0;
    $cdate = JFactory::getDate();
      for ($i=0, $n=count( $rows ); $i < $n; $i++) {
	$row = &$rows[$i];
	if($row->date == '0000-00-00 00:00:00') $row->date = $cdate->toFormat();
	$link = JRoute::_( 'index.php?option=com_videoflow&task=editcat&cid[]='. $row->id);
	$checked = JHTML::_('grid.id', $i, $row -> id );
        if (!empty($row->pixlink)) {
         if (stripos($row->pixlink, 'http://') === FALSE) {  
         $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->pixlink;
         } else {
         $pixpreview = $row->pixlink;
         }
       } else if (empty($row->pixlink) && file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$row->name.'.jpg')){
       $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->name.'.jpg';
       } else {
      $pixpreview = JURI::root().'components/com_videoflow/players/vflow.png';
      }  
    ?>
    <tr class="<?php echo "row$k"; ?>">
    <td align="center">
    <?php echo $pageNav->getRowOffset($i); ?>
    </td>
    <td align="center">
    <?php echo $checked; ?>
    </td>
    <td align="center">
    <span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_VIDEOFLOW_EDIT' );?>::<?php echo $row->name; ?>"><a href="<?php echo $link; ?>"> <img src="<?php echo $pixpreview; ?>" width=70 /></a></span>
    </td>
    <td style="padding: 0px 5px;">
    <span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_VIDEOFLOW_EDIT' );?>::<?php echo $row->name; ?>">
    <a href="<?php echo $link; ?>"> <?php echo $row->name; ?></a>
    </span>
    </td>
    <td>
    <?php echo $row->desc; ?>					
    </td>
    
    <td align="center">
    <?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
    <div><?php echo $pageNav->orderUpIcon( $i, true, 'corderup', JText::_('COM_VIDEOFLOW_MOVEUP'), true); ?><?php echo $pageNav->orderDownIcon( $i, $n, true, 'corderdown', JText::_('COM_VIDEOFLOW_MOVEDOWN'), true); ?></div>
    <div><input type="text" name="order[]" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="vfinput" /></div>							
    </td>
    <!--    
    <td class="order">
    <?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
    <input type="text" name="order[]" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="vfinput" />
    </td>
    -->
    <td align="center">
    <?php echo JHTML::_('date', $row->date, JText::_('DATE_FORMAT_LC4')); ?>
    </td>
    <td align="center">
    <?php echo $row->id; ?>
    </td>
    </tr>
    <?php
    $k = 1 - $k;
    }
    ?>
    </tbody>
    </table>
    <input type="hidden" name="c" value="media" />
    <input type="hidden" name="option" value="com_videoflow" />
    <input type="hidden" name="task" value="categorylist" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
    <?php echo $this-> printFooter(); ?>
  </div>
  </div>
  </form>
    <?php
    }
  
  
  static function setEditToolbar()
  {
    $task = JRequest::getVar('task');
    if ($task == 'add') $task = JText::_('COM_VIDEOFLOW_ADD_NEW'); else $task = JText::_('COM_VIDEOFLOW_EDIT_ITEM');
    JToolbarHelper::title( JText::_('COM_VIDEOFLOW_MEDIA_MANAGER').': '.$task);
    JToolBarHelper::apply('apply');
    JToolBarHelper::save2new('save2new');
    JToolBarHelper::save( 'save' );
    JToolBarHelper::cancel( 'cancel' );
    $tbar= JToolBar::getInstance( 'toolbar' );
    $tbar->appendButton( 'Help', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=61', 725, 520 );
  }

  
  
  function editMedia( $option, &$row, $mlist ) 
  {
   global $vparams; 
    JHTML::_('behavior.modal', 'a.modal-vfpop');
    JHtml::_('formbehavior.chosen', 'select');
    VideoflowViewMedia::setEditToolbar();    
    if (!empty($row->pixlink)) {
         if (stripos($row->pixlink, 'http://') === FALSE) {  
         $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->pixlink;
         } else {
         $pixpreview = $row->pixlink;
         }
       } else if (empty($row->pixlink) && file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$row->title.'.jpg')){
       $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->title.'.jpg';
       } else if($row->type == 'jpg' || $row->type == 'gif' || $row->type == 'png' ) {
	   $pixpreview = self::getThumb($row);
	   } else {
      $pixpreview = JURI::root().'components/com_videoflow/players/vflow.png';
      }
    $newcat = JText::_('COM_VIDEOFLOW_NEW_CATEGORY');
    if (empty($row->userid)) {
      $user = JFactory::getUser();
      $row->userid = $user->id;
    }
    if ($row->id>0){
    $thumbselect = "<a href=\"index.php?option=com_videoflow&task=vbrowser&tmpl=component&source=link&id=$row->id\" class=\"modal-vfpop\" rel=\"{handler: 'iframe', size: {x: 725, y: 520}}\">";
    $vidselect = "<a href=\"index.php?option=com_videoflow&task=popupload&tmpl=component&source=link&id=$row->id\" class=\"modal-vfpop\" rel=\"{handler: 'iframe', size: {x: 725, y: 520}}\">";
    } else {
      if (version_compare(JVERSION, '1.6.0') < 0) $sbutton = JText::_('Apply'); else $sbutton = JText::_('Save');
    $vnotice = JText::sprintf ('COM_VIDEOFLOW_TITLE_SELECT_IMAGE', $sbutton);
    $vidnotice = JText::sprintf ('COM_VIDEOFLOW_TITLE_UPLOAD', $sbutton);
    $thumbselect = "<a href=\"#\" onClick=\"alert('$vnotice')\">";
    $vidselect = "<a href=\"#\" onClick=\"alert('$vidnotice')\">";			
    }
 ?>

  <script language="javascript" type="text/javascript">
  <!--
  <?php
  if (version_compare(JVERSION, '1.6.0') < 0) {
    echo 'function submitbutton(pressbutton)';
  } else {
    echo 'Joomla.submitbutton = function(pressbutton)';
  }
  ?>
  
  {
   if (pressbutton == "cancel") {
     submitform(pressbutton);
   }
   else {
    var v = document.adminForm;
    if (v.title.value == "") {
    	alert( "<?php echo JText::_( 'COM_VIDEOFLOW_PROVIDE_TITLE', true ); ?>" );
    } 
    else { 
      v.task.value = pressbutton;     
      submitform(pressbutton);
    }
   }
  }
  //-->
  </script>
	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col100 vfbackend">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_VIDEOFLOW_DETAILS' ); ?></legend>
          <table class="admintable vfeditpanel">
            <tr>
            <td class="adminvthumb">
	      <table class="vfctrtable"><tr><td> 
            <img class="vfeditpix" src="<?php echo $pixpreview; ?>" />
	    </td></tr></table>
            </td>
            </tr>
          </table>
          <table class="admintable">
            <tr>	
            <td class="key">
	    <label for="title">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TITLE' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="70" maxsize="90" name="title" value="<?php echo stripslashes($row->title); ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="file">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FILE' ); ?>:
	    </label>
	    </td>
            <td>
	    <div class="vfinleft">
            <input type="text" size="70" maxsize="90" name="file" value="<?php echo $row->file; ?>" />
	    </div>
	    <div class="vfinmiddle">
            <?php echo $vidselect; ?><?php echo JText::_('COM_VIDEOFLOW_UPLOAD'); ?></a>
	    </div>
            <div class="vfinright"><a href="index.php?option=com_videoflow&task=edit&cid[]=<?php echo $row->id; ?>"><?php echo JText::_('VF_REFRESH'); ?></a>
	    <div>
	    </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="type">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TYPE' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="type" value="<?php echo stripslashes($row->type); ?>" /></td>
            </tr>
            <tr>
             <tr>
            <td class="key">
	    <label for="type">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SERVER' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo $mlist['serverlist']; ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="user id">
	    <?php echo JText::_( 'COM_VIDEOFLOW_USERID' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="userid" value="<?php echo $row->userid; ?>">
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="thumbnail">
	    <?php echo JText::_( 'COM_VIDEOFLOW_THUMBNAIL' ); ?>:
	    </label>
	    </td>
            <td>
	      <div class="vfinleft">
            <input type="text" size="70" maxsize="90" name="pixlink" value="<?php echo $row->pixlink; ?>">
	      </div>
	      <div class="vfinmiddle">
            <?php echo $thumbselect; ?><?php echo JText::_('COM_VIDEOFLOW_SELECT'); ?></a>
	      </div>
	      <div class="vfinright">
            <a href="index.php?option=com_videoflow&task=edit&cid[]=<?php echo $row->id; ?>"><?php echo JText::_('VF_REFRESH'); ?></a>
	      </div>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="date">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DATE' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="dateadded" value="<?php echo $row->dateadded; ?>">
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="published">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PUBLISHED' ); ?>:
	    </label>
	    </td>
            <td> 
            <?php echo JHTML::_('select.booleanlist',  'published', '', $row->published ); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="featured">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FEATURED' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.booleanlist',  'recommended', '', $row->recommended ); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="category">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CATEGORY' ); ?>:
	    </label>
	    </td>
            <td>
	    <div class="vfinleft">
            <?php echo $mlist['catlist']; ?>
	    </div>
	    <div style="float:left;">
	    <input type="text" size="30" maxsize="80" class="vfonfocus" name="newcat" value="<?php echo $newcat; ?>" onfocus="if (this.value=='<?php echo $newcat; ?>') this.value = ''" onblur="if (this.value=='') this.value ='<?php echo $newcat; ?>'" />
	    </div>
	    </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="tags">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TAGS' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="70" maxsize="90" name="tags" value="<?php echo stripslashes($row->tags); ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="description">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DESCRIPTION' ); ?>:
	    </label>
	    </td>
            <td>
            <textarea name="details" cols="45" rows="6" value="" wrap="soft" class="vf_input"><?php echo stripslashes($row->details); ?></textarea>
            </td>
            </tr>
          </table> 
          <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
          <input type="hidden" name="option" value="<?php echo $option; ?>" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="helplink" value="54#edit" />
      </fieldset>
  </div>         
 	<?php echo JHTML::_( 'form.token' ); ?>
  </form>
  <div class="clr"></div>
<?php 
}

  static function setEditCatToolbar()
  {
    JToolbarHelper::title( JText::_('COM_VIDEOFLOW_CAT_MANAGER'));
    JToolBarHelper::apply('applycats');
    JToolBarHelper::save2new('save2newcat');
    JToolBarHelper::save( 'savecats' );
    JToolBarHelper::cancel( 'cancelcats' );
    $tbar= JToolBar::getInstance( 'toolbar' );
    $tbar->appendButton( 'Popup', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=61', 725, 520 );
  }

  
  
  
  function editCat( $option, &$row ) 
  {
   global $vparams; 
    JHTML::_('behavior.modal', 'a.modal-vfpop');
    VideoflowViewMedia::setEditCatToolbar();
    if (!empty($row->pixlink)) {
         if (stripos($row->pixlink, 'http://') === FALSE) {  
         $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->pixlink;
         } else {
         $pixpreview = $row->pixlink;
         }
       } else if (empty($row->pixlink) && !empty($row->title) && file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$row->title.'.jpg')){
       $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->title.'.jpg';
       } else {
      $pixpreview = JURI::root().'components/com_videoflow/players/vflow.png';
      }
    $cdate = JFactory::getDate();
    if($row->date == '0000-00-00 00:00:00') $row->date = $cdate->toFormat();
    if ($row->id>0){
    $thumbselect = "<a href=\"index.php?option=com_videoflow&task=vbrowserc&vtask=directc&tmpl=component&source=link&id=$row->id\" class=\"modal-vfpop\" rel=\"{handler: 'iframe', size: {x: 725, y: 520}}\">";
    } else {
      if (version_compare(JVERSION, '1.6.0') < 0) $sbutton = JText::_('COM_VIDEOFLOW_APPLY'); else $sbutton = JText::_('COM_VIDEOFLOW_SAVE');
    $vnotice = JText::sprintf('COM_VIDEOFLOW_CATEGORY_IMAGE', $sbutton);
    $thumbselect = "<a href=\"#\" onClick=\"alert('$vnotice')\">";
    }
 ?>

  <script language="javascript" type="text/javascript">
  <!--
    <?php
  if (version_compare(JVERSION, '1.6.0') < 0) {
    echo 'function submitbutton(pressbutton)';
  } else {
    echo 'Joomla.submitbutton = function(pressbutton)';
  }
  ?>
  {
   if (pressbutton == "cancelcats" || pressbutton == 'cancel') {
     submitform(pressbutton);
   } else {
    var v = document.adminForm;
    if (v.name.value == "") {
    alert( "<?php echo JText::_( 'COM_VIDEOFLOW_PROVIDE_CATEGORY_NAME', true ); ?>" );
    } else { 
      v.task.value = pressbutton;     
      submitform(pressbutton);
    }
   }
  }
  //-->
  </script>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100 vfbackend">
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_DETAILS' ); ?></legend>
<table class="admintable vfeditpanel">
<tr>
<td class="adminvthumb">
<table class="vfctrtable"><tr><td>
<img class="vfeditpix" src="<?php echo $pixpreview; ?>" height=75 />
</td></tr></table>
</td>
</tr>
</table>
<table class="admintable">
<tr>	
<td class="key">
<label for="name">
<?php echo JText::_( 'COM_VIDEOFLOW_NAME' ); ?>:
</label>
</td>
<td>
<input type="text" size="70" maxsize="90" name="name" value="<?php echo stripslashes($row->name); ?>" />
</td>
</tr>
<tr>
<td class="key">
<label for="thumbnail">
<?php echo JText::_( 'COM_VIDEOFLOW_THUMBNAIL' ); ?>:
</label>
</td>
<td>
  <div class="vfinleft">
<input type="text" size="60" maxsize="80" name="pixlink" value="<?php echo $row->pixlink; ?>">
  </div>
  <div class="vfinmiddle">
<?php echo $thumbselect; ?><?php echo JText::_('COM_VIDEOFLOW_SELECT'); ?></a>
  </div>
  <div class="vfinright">
<a href="index.php?option=com_videoflow&task=editcat&cid[]=<?php echo $row->id; ?>"><?php echo JText::_('VF_REFRESH'); ?></a>
  </div>
</td>
</tr>
<tr>
<td class="key">
<label for="desc">
<?php echo JText::_( 'COM_VIDEOFLOW_DESCRIPTION' ); ?>:
</label>
</td>
<td>
<textarea name="desc" cols="45" rows="6" value="" wrap="soft"><?php echo stripslashes($row->desc); ?></textarea>
</td>
</tr>
<tr>
<td class="key">
<label for="date">
<?php echo JText::_( 'COM_VIDEOFLOW_DATE' ); ?>:
</label>
</td>
<td>
<input size="30" type="text" maxsize="80" name="date" value="<?php echo $row->date; ?>">
</td>
</tr>
</table> 
<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
</fieldset>
</div>         
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<div class="clr"></div>
<?php 
}



function popManager(&$row){
 JHTML::_('behavior.modal', 'a.modal-vfpop');
?>
      <fieldset class="adminform">
	  <legend><?php echo JText::_( 'COM_VIDEOFLOW_STATUS' ); ?></legend>
          <table class="admintable" style="width:100%;">
            <tr>
            <td>
            <?php echo $row->message; ?>
            <br /><br />
            </td>
            </tr>
            <tr>
            <td class="adminvthumb">
            <img src="<?php echo $row->pixlink; ?>" height=75 />
            </td>
            </tr>
          </table>
      </fieldset>
      </div>
<?php                
}


function popBrowser ($obj)
{
global $vparams;
$task = JRequest::getCmd('task');
if ($task == 'vbrowserc' || $task == 'directc') {
$vtask = 'directc';
$dtask = 'savecats';
} else {
$vtask = 'direct';
$dtask = 'save';
}

$vcon = mt_rand();
  $vbutton = "<button name='psubmit' onclick='document.adminForm.submit()'>".JText::_( "COM_VIDEOFLOW_APPLY" )."</button>";   

?>
<form action="index.php" method="post" enctype="multipart/form-data" target="upload_target" name="uplform" id="uplform">
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_DESKTOP_UPLOAD' ); ?> </legend>
                     <div id="f1_upload_process" style="text-align:center;"><?php JText::_('COM_VIDEOFLOW_UPLOADING_WAIT') ?><br/><img src="<?php echo JURI::root().'administrator/components/com_videoflow/images/loader.gif'; ?>" /></div>
                     <div id="f1_upload_form" style="text-align:center;"><br/>
                         <label>File Name:  
                              <input name="deskpix" id="deskpix" type="file" size="30" title=".JPG, .PNG, .GIF" accept="jpg, png, gif"/>
                         </label>
                         <label>
                            <!-- <input type="submit" name="submitBtn" class="sbtn" value="Upload" /> -->
                             <button onclick="document.uplform.submit(); startUpload(); return false" name="uplsubmit"><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD' ); ?></button>
                             <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $vparams->maxthumbsize; ?>" />
                             <input type="hidden" name="UPLOAD_FILE_TYPE" value="image" />
                             <input type="hidden" name="id" value="<?php echo $obj->id; ?>" />
                             <input type="hidden" name="option" value="com_videoflow" />
                             <input type="hidden" name="task" value="<?php echo $dtask; ?>" />
                         </label>
                     </div>     
                <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
                 <?php echo JHTML::_( 'form.token' ); ?>
</fieldset>
</form>

<form enctype="multipart/form-data" action='index.php?tmpl=component' method='post' name='adminForm' id="adminForm">
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_SELECT_FROM_SERVER' ); ?> </legend>
<input type="text" readonly="readonly" id="serverpix" name="serverpix" value="" size="70" />	<?php echo "&nbsp&nbsp"; echo $vbutton; ?>
<br /><br />
<?php 
echo $obj->sortby;
echo "&nbsp";
echo $obj->sortdir;
echo "&nbsp";
?>
<input type="submit" name="ssubmit" value="<?php echo JText::_( "COM_VIDEOFLOW_SORT" ); ?>" />
<br /><br />
<table class='adminlist'>
<tr>
<th width='85'>
<?php echo JText::_( 'COM_VIDEOFLOW_IMAGE' ); ?>
</th>
<th>
<?php echo JText::_( 'COM_VIDEOFLOW_FILE' ); ?>
</th>
<th>
<?php echo JText::_( 'COM_VIDEOFLOW_DATE' ); ?>
</th>
<th>
<?php echo JText::_( 'COM_VIDEOFLOW_SIZE' ); ?>
</th>
</tr>
<?php
while($obj->NextFile())
{
$fileurl = JURI::root().$vparams->mediadir.'/_thumbs/'.$obj->FieldName;
$filename = $obj->FieldName;
echo "<tr>";
echo "<td>"; 
echo "<a href='#'><img src='$fileurl' width='80' onClick=\"document.getElementById('serverpix').value='$obj->FieldName'\";  /></a>";
echo '</td>';
echo "<td>";
echo "<a href='#' onClick=\"document.getElementById('serverpix').value='$obj->FieldName'\";>".$obj->FieldName."</a>";
echo "</td>";
echo "<td>".$obj->FieldDate."</td>";
echo "<td>".$obj->FieldSize."</td>";
echo "</tr>";
}
echo "</table>";  
echo '<input type="hidden" name="id" value="'.$obj->id.'" />';
echo '<input type="hidden" name="option" value="com_videoflow" />';
echo '<input type="hidden" name="task" value="'.$vtask.'" />';
echo JHTML::_( 'form.token' );
?>
</form>
</fieldset>
<?php
}


function popVbrowser ($obj=null)
{

global $vparams;
?>
  <script language="javascript" type="text/javascript">
  <!--
  var cmes = "<?php echo JText::_('COM_VIDEOFLOW_CONTINUE2'); ?>";
  function vcheckFile(){
    var v = document.adminForm;
    if (v.myfile.value == "") {
    alert( "<?php echo JText::_( 'COM_VIDEOFLOW_SELECT_THUMB', true ); ?>" );
    } 
  }
  //-->
  </script>

<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_THUMBNAIL_FILE' ); ?> </legend>
<?php echo JText::_( 'COM_VIDEOFLOW_STEP1' ); ?> <br/>
                <form action="index.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload(); vcheckFile();" name="adminForm" id="adminForm" >
                     <div id="f1_upload_process" style="text-align:center;"><?php echo JText::_('COM_VIDEOFLOW_UPLOADING_WAIT'); ?><br/><img src="<?php echo JURI::root().'administrator/components/com_videoflow/images/loader.gif'; ?>" /></div>
                     <div id="f1_upload_form" style="text-align:center;"><br/>
                         <label><?php echo JText::_('COM_VIDEOFLOW_FILE_NAME'); ?>  
                          <input name="myfile" id="myfile" type="file" size="30" title=".JPG, .PNG, .GIF" accept="jpg, png, gif"/>
                         </label>
                             <input type="submit" name="submitBtn" class="sbtn" value="Upload" />
                             <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $vparams->maxthumbsize; ?>" />
                             <input type="hidden" name="UPLOAD_FILE_TYPE" value="image" />
                             <input type="hidden" name="id" value="<?php echo $obj->id; ?>" />
                             <input type="hidden" name="option" value="com_videoflow" />
                             <input type="hidden" name="task" value="save" />
                     </div>     
                <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
                 <?php echo JHTML::_( 'form.token' ); ?>
                 </form>
</fieldset>

<?php
if ($vparams->upsys == 'swfupload') self::swfUploadForm($obj); else self::pluploadForm($obj);
}


function swfUploadForm()
{

$session = JFactory::getSession();
$doc = JFactory::getDocument();
$swfupload = JURI::root().'components/com_videoflow/utilities/js/swfupload.js';
$doc->addScript($swfupload);

$queue = JURI::root().'components/com_videoflow/utilities/js/swfupload.queue.js';
$doc->addScript($queue);

$fileprocess = JURI::root().'components/com_videoflow/utilities/js/fileprogress.js';
$doc->addScript($fileprocess);

$handlers = JURI::root().'components/com_videoflow/utilities/js/handlers.js';
$doc->addScript($handlers);

$flashinitiate = '
	var swfu;

		window.onload = function() {
			var settings = {
				flash_url : "'.JURI::root().'components/com_videoflow/utilities/swf/swfupload.swf",
				upload_url: "index.php?option=com_videoflow&task=save&'.JSession::getFormToken().'=1",
				post_params: {"option" : "com_videoflow", "task" : "save", 
				"media_id" : "'.$obj->id.'", "'.$session->getName().'" : "'.$session->getId().'", "format" : "raw"},
				file_size_limit : "'.$vparams->maxmedsize.'MB",
				file_types : "*.flv; *.mp4; *.swf; *.3g2; *.3gp; *.mov; *.mp3; *.aac; *.jpg; *.gif; *.png; *.ogv; *.webm",
				file_types_description : "Media Files",
				file_upload_limit : 0,
				file_queue_limit : 1,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel",
					vflowMode : "vRefresh",
					mediaId : "'.$obj->id.'"
				},
				debug: false,
				button_image_url: "'.JURI::root().'components/com_videoflow/utilities/images/uploadimage.png",
				button_width: "100",
				button_height: "29",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: "<span class=\"theFont\">Select File</span>",
				button_text_style: ".theFont { font-size: 16; }",
				button_text_left_padding: 12,
				button_text_top_padding: 3,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete
			};

			swfu = new SWFUpload(settings);
	     };
    ';
$doc->addScriptDeclaration($flashinitiate);
?>
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_FILE' ); ?> </legend>
<?php echo JText::_( 'COM_VIDEOFLOW_STEP2' ); ?> <br />
<br />
<div id="scontent">
	<form id="form1" action="index.php" method="post" enctype="multipart/form-data" name="form1">
			<div class="fieldset flash" id="fsUploadProgress">
			<span class="legend"><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_STATUS' ); ?></span>
			</div>
		<div id="divStatus"></div>
			<div>
				<span id="spanButtonPlaceHolder"></span>
				<input id="btnCancel" type="button" value="<?php echo JText::_( 'COM_VIDEOFLOW_CANCEL_UPLOADS' ); ?>" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
			</div>
		 <?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
</fieldset>
<?php
}

function popVupload ()
{
  global $vparams;
  self::formprep(); if ($vparams->upsys == 'swfupload') self::qSwfupload(); else self::qPlupload();
}

function qPlupload ()
{
global $vparams;
$app = JFactory::getApplication();
$session = JFactory::getSession();
$doc = JFactory::getDocument();
JHtml::_('jquery.framework');
$vcss = JURI::root().'components/com_videoflow/utilities/plupload/css/plupload.queue.css';
$doc->addStyleSheet( $vcss, 'text/css', null, array() );
$qcss = '.plupload_scroll .plupload_filelist {
	       height: 300px;
        }';
$doc->addStyleDeclaration($qcss);
$upurl = JURI::root().'administrator/index.php?option=com_videoflow&task=vxupload&'.$session->getName().'='.$session->getId().'&'.JSession::getFormToken().'=1';
$maxmedsize = $vparams->maxmedsize.'mb';
$app->setUserState( "com_videoflow.media.filter_order", "b.dateadded" );
$app->setUserState( "com_videoflow.media.filter_order_Dir", "desc" );
        
?>
<script type="text/javascript" src="<?php echo JURI::root().'components/com_videoflow/utilities/plupload/js/plupload.full.min.js';?>"></script>
<script type="text/javascript" src="<?php echo JURI::root().'components/com_videoflow/utilities/plupload/js/jquery.plupload.queue.min.js';?>"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#vfx_uploader").pluploadQueue({
		// General settings
		runtimes : 'flash,html5,html4',
		url : '<?php echo $upurl; ?>',
		max_file_size : '<?php echo $maxmedsize; ?>',
		chunk_size : '1mb',
		unique_names : false,
		urlstream_upload : true,
		dragdrop : false,

	// Resize images on clientside if we can
	//	resize : {width : 320, height : 240, quality : 90},

		// Specify what files to browse for
		filters : [
			{title : "Media Files", extensions : "jpg,gif,png,mp3,swf,mp4,flv,webm,ogv"}
		],

		// Flash URL
		flash_swf_url : '<?php echo JURI::root()."components/com_videoflow/utilities/plupload/js/plupload.flash.swf"; ?>'

		});

    var uploader = jQuery('#vfx_uploader').pluploadQueue();
  
  // Start upload   
     jQuery('#uploadfiles').click(function(e) {
	        uploader.start();
	        e.preventDefault();   
      });
  
  // Exit
    uploader.bind('FileUploaded', function(up, file, res) {
        if(up.total.queued == 0) {
        parent.location.href="index.php?option=com_videoflow";  
       // self.close(); 
        }
    });
});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" >
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD' ); ?></legend>
<table class="admintable" style="width: 100%">
<tr><td>  
<?php echo JText::_( 'COM_VIDEOFLOW_SELECT_QUEUE_FILES' ); ?> <br />
	<div id="vf_plupload">
        <div id="vfx_uploader">
	<p><?php echo JText::_('COM_VIDEOFLOW_NO_FLASH_HTML5');?></p>
	</div>
	<div style="padding:6px 8px";> <?php echo JText::_('COM_VIDEOFLOW_BE_COOL'); ?></div>
	<br style="clear: both" />
    </div>
</td></tr>
</table>
</fieldset>
<input type="hidden" name="option" value="com_videoflow" />
<input type="hidden" name="task" value="" />
</form>
<?php
}

function qSwfupload()
{
  global $vparams;
  $queuelen = 10;  
  $session = JFactory::getSession();
  $doc = JFactory::getDocument();
  $swfupload = JURI::root().'components/com_videoflow/utilities/js/swfupload.js';
  $doc->addScript($swfupload);

  $queue = JURI::root().'components/com_videoflow/utilities/js/swfupload.queue.js';
  $doc->addScript($queue);

  $fileprocess = JURI::root().'components/com_videoflow/utilities/js/fileprogress.js';
  $doc->addScript($fileprocess);

  $handlers = JURI::root().'components/com_videoflow/utilities/js/handlers.js';
  $doc->addScript($handlers);

$flashinitiate = '
	var swfu;

		window.onload = function() {
			var settings = {
				flash_url : "'.JURI::root().'components/com_videoflow/utilities/swf/swfupload.swf",
				upload_url: "index.php?option=com_videoflow&task=vupload&'.JSession::getFormToken().'=1",
				post_params: {"option" : "com_videoflow", "task" : "vupload", 
				"'.$session->getName().'" : "'.$session->getId().'", "format" : "raw"},
				file_size_limit : "'.$vparams->maxmedsize.'MB",
				file_types : "*.flv; *.mp4; *.swf; *.3g2; *.3gp; *.mov; *.mp3; *.aac; *.jpg; *.gif; *.png; *.ogv; *.webm",
				file_types_description : "Media Files",
				file_upload_limit : 0,
				file_queue_limit : "'.$queuelen.'",
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel",
					vflowMode : "vStatic"
				},
				debug: false,
				button_image_url: "'.JURI::root().'components/com_videoflow/utilities/images/uploadimage.png",
				button_width: "100",
				button_height: "29",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: "<span class=\"theFont\">Select File</span>",
				button_text_style: ".theFont { font-size: 16; }",
				button_text_left_padding: 12,
				button_text_top_padding: 3,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete
			};

			swfu = new SWFUpload(settings);
	     };
    ';
$doc->addScriptDeclaration($flashinitiate);

?>
<form id="adminForm" action="index.php" method="post" enctype="multipart/form-data" name="adminForm">
<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD' ); ?></legend>
<?php echo JText::_( 'COM_VIDEOFLOW_SELECT_QUEUE_FILES' ); ?> 
<br/>
<br/>
<br/>
<div id="scontent">
		<div class="fieldset flash" id="fsUploadProgress">
		<span class="legend"><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_STATUS' ); ?></span>
		</div>
		<div id="divStatus"></div>
		<div>
		<span id="spanButtonPlaceHolder"></span>
		<input id="btnCancel" type="button" value="<?php echo JText::_( 'COM_VIDEOFLOW_CANCEL_UPLOADS' ); ?>" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
		</div>
	      <?php echo JHTML::_( 'form.token' ); ?>
</div>
<input type="hidden" name="option" value="com_videoflow" />
<input type="hidden" name="task" value="" />
</fieldset>
</form>
<?php
  }
 
function pluploadForm ($obj=null)
{
global $vparams; 
$session = JFactory::getSession();
$doc = JFactory::getDocument();
JHtml::_('jquery.framework');
$vcss = JURI::root().'components/com_videoflow/utilities/plupload/css/plupload.queue.css';
$doc->addStyleSheet( $vcss, 'text/css', null, array() );
$upurl = JURI::root().'administrator/index.php?option=com_videoflow&task=saveXpload&user_id='.$obj->userid.'&media_id='.$obj->id.'&'.$session->getName().'='.$session->getId().'&'.JSession::getFormToken().'=1';
$maxmedsize = $vparams->maxmedsize.'mb';       
?>
<script type="text/javascript" src="<?php echo JURI::root().'components/com_videoflow/utilities/plupload/js/plupload.full.min.js';?>"></script>
<script type="text/javascript" src="<?php echo JURI::root().'components/com_videoflow/utilities/plupload/js/jquery.plupload.queue.min.js';?>"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#vfx_uploader").pluploadQueue({
		// General settings
		runtimes : 'flash,html5,html4',
		url : '<?php echo $upurl; ?>',
		max_file_size : '<?php echo $maxmedsize; ?>',
		chunk_size : '1mb',
		unique_names : false,
		urlstream_upload : true,
		dragdrop: false,
		// Specify what files to browse for
		filters : [
			{title : "Media Files", extensions : "jpg,gif,png,mp3,swf,mp4,flv,ogv,webm"}
		],
		// Flash URL
		flash_swf_url : '<?php echo JURI::root()."components/com_videoflow/utilities/plupload/js/plupload.flash.swf"; ?>'
		});

    var uploader = jQuery('#vfx_uploader').pluploadQueue();
    
  // autostart
    uploader.bind('FilesAdded', function(up, files) {
      if (up.files.length > 1) {
      var xrem = up.files.length - 1;
      up.files.splice(0,xrem);
      }
      uploader.start();
    });
  // after upload
    uploader.bind('FileUploaded', function(up, file, res) {
        if(up.total.queued == 0) {
        parent.location.href="index.php?option=com_videoflow&task=getStatus&cid=<?php echo $obj->id;?>&userid=<?php echo $obj->userid; ?>&file=" + file.name;  
        self.close(); 
        }
    });
});
</script>

<fieldset class="vf_forms">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_FILE' ); ?> </legend>
<?php echo JText::_( 'COM_VIDEOFLOW_STEP2' ); ?> <br />
	<div id="vf_plupload">
        <div id="vfx_uploader" style="height: 200px;">
	<p><?php echo JText::_('COM_VIDEOFLOW_NO_FLASH_HTML5');?></p>
	</div>
	<div style="padding:4px 8px";> <?php echo JText::_('COM_VIDEOFLOW_BE_COOL'); ?></div>
	<br style="clear: both" />
    </div>
</fieldset>
<?php
}

 
  
function popVembed ()
{
  self::formprep();
?>

<form id="adminForm" name="adminForm" action="index.php" method="post">
<br/>
<fieldset class="adminform">
<legend><?php echo JText::_('COM_VIDEOFLOW_EMBED'); ?></legend>
<?php echo JText::_( 'COM_VIDEOFLOW_PASTE_URL' ); ?> 
<br/>
<br/>
<div>

 <table class="admintable">
            <tr>	
            <td>
	    <input type="text" class="vfinlong" size="120" maxsize="150" name="embedlink" value="" />	
	    </td>
            <td style="padding-left: 5px;">  
            <button onclick="document.adminForm.submit(); return false" name="vsubmit"><?php echo JText::_( 'COM_VIDEOFLOW_APPLY' ); ?></button>
            <button type="button" name="vcancel" onclick="window.location.replace('index.php?option=com_videoflow'); return false"><?php echo JText::_( 'COM_VIDEOFLOW_CANCEL' ); ?></button>
            </td>
            </tr>
  </table> 
  <input type="hidden" name="option" value="com_videoflow" />
  <input type="hidden" name="task" value="vembed" /> 
  <?php echo JHTML::_( 'form.token' ); ?>
</div>
</fieldset>
</form>
<?php
}  

  
  function saveRemote(&$row) 
  {
    jimport('joomla.application.component.view');
    self::formprep();
    $newcat = JText::_('COM_VIDEOFLOW_NEW_CATEGORY');
    if (version_compare(JVERSION, '3.0.0', 'lt')) {
    $v = new JView;
    } else {
    $v = new JViewLegacy;  
    }
    $pixpreview = stripslashes($row->pixlink);
    if (empty($pixpreview)){
    $pixpreview = JURI::root().'components/com_videoflow/players/vflow.png';
    }
 ?>

	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col100 vfbackend">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_DETAILS' ); ?></legend>
          <table class="admintable" style="width:100%; text-align:center; margin-bottom:5px;">
            <tr>
            <td class="adminvthumb">
	      <table class="vfctrtable"><tr><td>
            <img class="vfeditpix" src="<?php echo $pixpreview; ?>" height=75 />
	      </td></tr></table>
            </td>
            </tr>
          </table>
          <table class="admintable">
            <tr>	
            <td class="key">
	    <label for="title">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TITLE' ); ?>:
	    </label>
	    </td>
             <td>
            <input type="text" size="70" maxsize="90" name="title" value="<?php echo $v->escape(stripslashes($row->title)); ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="file">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FILE' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="70" maxsize="90" name="file" value="<?php echo $row->file; ?>" />
            </td>
            </tr>
             <tr>
            <td class="key">
	    <label for="type">
	    <?php echo JText::_( 'COM_VIDEOFLOW_SERVER' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="server" value="<?php echo $row->server; ?>">
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="thumbnail">
	    <?php echo JText::_( 'COM_VIDEOFLOW_THUMBNAIL' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="60" maxsize="80" name="pixlink" value="<?php echo $row->pixlink; ?>">
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="date">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DATE' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="30" maxsize="80" name="dateadded" value="<?php echo $row->dateadded; ?>">
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="published">
	    <?php echo JText::_( 'COM_VIDEOFLOW_PUBLISHED' ); ?>:
	    </label>
	    </td>
            <td> 
            <?php echo JHTML::_('select.booleanlist',  'published', '', 1 ); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="featured">
	    <?php echo JText::_( 'COM_VIDEOFLOW_FEATURED' ); ?>:
	    </label>
	    </td>
            <td>
            <?php echo JHTML::_('select.booleanlist',  'recommended', '', 0 ); ?>
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="category">
	    <?php echo JText::_( 'COM_VIDEOFLOW_CATEGORY' ); ?>:
	    </label>
	    </td>
            <td>
	    <div class="vfinleft">
            <?php echo $row->catlist; ?>
	    </div>
	    <div style="float:left;">
	    <input type="text" class="vfonfocus" name="newcat" value="<?php echo $newcat; ?>" onfocus="if (this.value=='<?php echo $newcat; ?>') this.value = ''" onblur="if (this.value=='') this.value ='<?php echo $newcat; ?>'" />
	    </div>
	    </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="tags">
	    <?php echo JText::_( 'COM_VIDEOFLOW_TAGS' ); ?>:
	    </label>
	    </td>
            <td>
            <input type="text" size="70" maxsize="90" name="tags" value="<?php echo stripslashes($row->tags); ?>" />
            </td>
            </tr>
            <tr>
            <td class="key">
	    <label for="description">
	    <?php echo JText::_( 'COM_VIDEOFLOW_DESCRIPTION' ); ?>:
	    </label>
	    </td>
            <td>
            <textarea name="details" style="width:320px;" cols="45" rows="6" value="" wrap="soft"><?php echo $v->escape(stripslashes($row->details)); ?></textarea>
            </td>
            </tr>  
          </table> 
          <table class="admintable" style="width:100%;">
            <tr>
            <td class="adminvthumb">
	      <table style="margin:auto;"><tr><td>
            <button onclick="document.adminForm.submit()" name="btnsubmit"><?php echo JText::_( 'COM_VIDEOFLOW_SAVE' ); ?></button>
	    <button type="button" name="vsfcancel" onclick="window.location.replace ('index.php?option=com_videoflow'); return false;"><?php echo JText::_( 'COM_VIDEOFLOW_CANCEL' ); ?></button>
	    </td></tr></table>
	    </td>
            </tr>
          </table>
      
          <input type="hidden" name="medialink" value="<?php echo $row->medialink; ?>" />
          <input type="hidden" name="type" value="<?php echo $row->type; ?>" />
          <input type="hidden" name="userid" value="<?php echo $row->userid; ?>" >
          <input type="hidden" name="id" value="0" />
          <input type="hidden" name="option" value="com_videoflow" />
          <input type="hidden" name="task" value="saveremote" />
      </fieldset>
  </div>          
 	<?php echo JHTML::_( 'form.token' ); ?>
  </form>
	<div class="clr"></div>
<?php 
}

function formprep(){
  ?>
  <script language="javascript" type="text/javascript">
  <!--
  <?php
  if (version_compare(JVERSION, '1.6.0') < 0) {
    echo 'function submitbutton(pressbutton)';
  } else {
    echo 'Joomla.submitbutton = function(pressbutton)';
  }
  ?> 
  {
   if (pressbutton == "cancel") {
     submitform(pressbutton);
   } else {
    document.adminForm.task.value = pressbutton;
    submitform(pressbutton);
    }
   }
  //-->
  </script>
<?php
$task = JRequest::getVar('task');
$tmpl = JRequest::getVar('tmpl');
if ($tmpl == 'component') return;
if ($task == 'vfembed' || $task == 'vembed') $stask = JText::_('COM_VIDEOFLOW_EMBED_ITEM'); elseif ($task == 'vfupload') $stask = JText::_('COM_VIDEOFLOW_UPLOAD_FILES'); else $stask = ''; 
$tbar= JToolBar::getInstance( 'toolbar' );
  JToolbarHelper::title( JText::_('COM_VIDEOFLOW_MEDIA_MANAGER').': '.$stask);
  if ($task == 'vfembed') $tbar->appendButton( 'Standard', 'apply', JText::_( 'COM_VIDEOFLOW_SAVE' ), 'vembed', false);
  if ($task == 'vembed') $tbar->appendButton( 'Standard', 'apply', JText::_( 'COM_VIDEOFLOW_SAVE' ), 'saveremote', false);
  JToolbarHelper::cancel ('cancel');
  $tbar->appendButton( 'Standard', 'home', JText::_( 'COM_VIDEOFLOW_HOME' ), 'com_videoflow.cancel', false);
  $tbar->appendButton( 'Help', 'help', JText::_( 'COM_VIDEOFLOW_HELP' ), 'http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&view=article&id=61', 725, 480 );
}

function getThumb($row){
include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
$p = new videoflowHTMLEXT();
return $p->imgResize($row, 'thumb');
}
}