<?php

//VideoFlow - Joomla Multimedia System for Facebook//
/**
* @ Version 1.2.1
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no liability arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class VideoflowHTML {

function uploadForm($obj=null)
{
global $vparams;
$doc = JFactory::getDocument();
$fcss = JURI::root().'components/com_videoflow/utilities/css/vf_forms.css';
$doc->addStyleSheet( $fcss, 'text/css', null, array() );
?>
<script language="javascript" type="text/javascript">
  <!--
  var cmes = "<?php echo JText::_('COM_VIDEOFLOW_CONT_STEP2'); ?>";
  function vcheckFile(){
    var v = document.vf_forms;
    if (v.myfile.value == "") {
    alert( "<?php echo JText::_( 'COM_VIDEOFLOW_SELECT_THUMB', true ); ?>" );
    } 
  }
  //-->
</script>
<div class="row-fluid">
<div class="span12">
<?php
JRequest::setVar('activemenu', JText::_('COM_VIDEOFLOW_ADD_MEDIA')); 
echo $this->printMenu();
?>
<form action="index.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();vcheckFile();" name="vf_forms" id="vf_forms">
<fieldset class="input">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_THUMB_FILE' ); ?> </legend>
<div><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_STEP1' ); ?></div> 
<div id="f1_upload_process" style="text-align:center;"><?php echo JText::_('COM_VIDEOFLOW_UPLOADING'); ?><br /><img src="<?php echo JURI::root().'components/com_videoflow/utilities/images/loader.gif'; ?>" /></div>
<div id="f1_upload_form" style="text-align:center;"><br />
  <label class="vflabel"><?php echo JText::_('COM_VIDEOFLOW_FILENAME'); ?></label>  
  <input name="myfile" id="myfile" type="file" class="input" title=".JPG, .PNG, .GIF" accept="jpg, png, gif"/>
    <input type="submit" name="submitBtn" class="btn" value="<?php echo JText::_('COM_VIDEOFLOW_UPLOAD'); ?>" />
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $vparams->maxthumbsize; ?>" />
    <input type="hidden" name="UPLOAD_FILE_TYPE" value="image" />
    <input type="hidden" name="id" value="<?php echo $obj->id; ?>" />
    <input type="hidden" name="option" value="com_videoflow" />
    <input type="hidden" name="task" value="saveThumb" />
</div>
<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
</fieldset>
</form>
<?php
if ($vparams->upsys == 'swfupload') self::swfUploadForm($obj); else self::pluploadForm($obj);
}


function swfUploadForm ($obj=null)
{
global $vparams; 
if ($vparams->lightbox) $lb = '&tmpl=component'; else $lb = '';
$session = JFactory::getSession();
if (version_compare(JVERSION, '2.5.0', 'lt')) {
  jimport( 'joomla.utilities.utility' );
  $sess = JUtility::getToken();
} else {
  $sess = $session->getName().'='.$session->getId().'&'.$session->getFormToken();
}
$doc = JFactory::getDocument();
$vcss = JURI::root().'components/com_videoflow/utilities/css/vf_upload.css';
$doc->addStyleSheet( $vcss, 'text/css', null, array() );
$fupload = JURI::root().'components/com_videoflow/utilities/js/fupload.js';
$doc->addScript($fupload);
$swfupload = JURI::root().'components/com_videoflow/utilities/js/swfupload.js';
$doc->addScript($swfupload);
$queue = JURI::root().'components/com_videoflow/utilities/js/swfupload.queue.js';
$doc->addScript($queue);
$fileprocess = JURI::root().'components/com_videoflow/utilities/js/fileprogress.js';
$doc->addScript($fileprocess);
$handlers = JURI::root().'components/com_videoflow/utilities/js/handlers.js';
$doc->addScript($handlers);
$fbuser = JRequest::getVar('fbuser');
$flashinitiate = '
var swfu;
window.onload = function() {
  var settings = {
  flash_url : "'.JURI::root().'components/com_videoflow/utilities/swf/swfupload.swf",
  upload_url: "index.php?option=com_videoflow&task=saveUpload&'.$sess.'=1'.$lb.'",
  post_params: {"option" : "com_videoflow", "task" : "saveUpload", "user_id" : "'.$obj->userid.'", "fb_user" : "'.$fbuser.'",
  "media_id" : "'.$obj->id.'", "'.$session->getName().'" : "'.$session->getId().'", "format" : "raw"},
  file_size_limit : "'.$vparams->maxmedsize.'MB",
  file_types : "*.flv; *.mp4; *.swf; *.3g2; *.3gp; *.mov; *.mp3; *.aac; *.jpg; *.gif; *.png; *.webm; *.ogv",
  file_types_description : "Media Files",
  file_upload_limit : 0,
  file_queue_limit : 1,
  custom_settings : {
  progressTarget : "fsUploadProgress",
  cancelButtonId : "btnCancel",
  vflowMode : "fRefresh",
  userId : "'.$obj->userid.'",
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


<div id="scontent">
    <form id="form1" action="index.php" method="post" enctype="multipart/form-data">
	<fieldset class="input">
	<legend><?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_FILE' ); ?> </legend>
	<br />
<?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_STEP2' ); ?> <br />
<br />
      <div class="fieldset flash" id="fsUploadProgress">
      <span class="legend"><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_STATUS' ); ?></span>
      </div>
      <div id="divStatus"></div>
      <div>
      <span id="spanButtonPlaceHolder"></span>
      <input id="btnCancel" type="button" value="<?php echo JText::_( 'COM_VIDEOFLOW_CANCEL_UPLOADS' ); ?>" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
      </div>
	  </fieldset>
    </form>
</div>
</div>
</div>

<?php
}

function pluploadForm ($obj=null)
{
global $vparams;
$session = JFactory::getSession();
$doc = JFactory::getDocument();
$vcss = JURI::root().'components/com_videoflow/utilities/plupload/css/plupload.queue.css';
$doc->addStyleSheet( $vcss, 'text/css', null, array() );
if ($vparams->jsframework == 'mootools' || !JVERS3) {
$gooq = 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js';
//$gooq = 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js';
$doc->addScript($gooq);
}
$plufull = JURI::root().'components/com_videoflow/utilities/plupload/js/plupload.full.min.js';
$doc->addScript($plufull);
$jq = JURI::root().'components/com_videoflow/utilities/plupload/js/jquery.plupload.queue.min.js';
$doc->addScript($jq);
$fbuser = JRequest::getVar('fbuser');
if ($vparams->lightbox) $lb = '&tmpl=component'; else $lb = '';
$upurl = JURI::root().'index.php?option=com_videoflow&task=saveXpload&user_id='.$obj->userid.'&media_id='.$obj->id.'&'.$session->getName().'='.$session->getId().'&'.JSession::getFormToken().'=1'.$lb;
$maxmedsize = $vparams->maxmedsize.'mb';
$redir = JRoute::_('index.php?option=com_videoflow&task=getStatus&cid='.$obj->id.'&userid='.$obj->userid.'&file=');
$plpd = "
jQuery(document).ready(function() {
	jQuery('#vfx_uploader').pluploadQueue({
		// General settings
		runtimes : 'html5, flash, silverlight, html4',
		url : '$upurl',
		max_file_size : '$maxmedsize',
		chunk_size : '1mb',
		unique_names : false,
		dragdrop : true,
		dropelement : 'vfdropzone',
		// Specify what files to browse for
		filters : [
		{title : 'Media Files', extensions : 'jpg,gif,png,mp3,swf,mp4,flv,webm,ogg,3gp'}
		],
		// Flash and Silverlight URL
		flash_swf_url : '".JURI::root().'components/com_videoflow/utilities/plupload/js/plupload.flash.swf'."',
		silverlight_xap_url : '".JURI::root().'components/com_videoflow/utilities/plupload/js/Moxie.xap'."'
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
        window.location = '$redir' + file.name; 
        }
    });
});
";
$doc->addScriptDeclaration($plpd);
?>
<div id="vfdropzone">
<fieldset class="input">
<legend><?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_FILE' ); ?> </legend>
<?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_STEP2' ); ?> <br />
 <div id="vf_plupload">
    <div id="vfx_uploader" style="height:auto; max-height:400px;">
	<p><?php echo JText::_('COM_VIDEOFLOW_NO_HTML5');?></p>
	</div>
	<div style="padding:4px 8px;"> <?php echo JText::_('COM_VIDEOFLOW_BE_COOL'); ?></div>
	<br style="clear: both" />
 </div>
</fieldset>
</div>
</div>
</div>
<?php
}



function addForm()
{   
$doc = JFactory::getDocument();
$css = JURI::root().'components/com_videoflow/utilities/css/vf_forms.css';
//$doc->addStyleSheet( $css, 'text/css', null, array() );
global $vparams;
$user = JFactory::getUser();
if ($vparams->lightbox) $lb = '?tmpl=component'; else $lb = '';
?>

  <script language="javascript" type="text/javascript">
  <!--
  function fieldcheck(myform) {
  var v = document.forms[myform];
  if (myform == "addForm") {    
    var myfield = v.embedlink.value;
    var alerttext = "<?php echo JText::_( 'COM_VIDEOFLOW_PROVIDE_URL'); ?>";
  } else if (myform == "uploadForm") {
    var myfield = v.title.value;
    var alerttext = "<?php echo JText::_( 'COM_VIDEOFLOW_PROVIDE_TITLE'); ?>";
  }
  if (myfield == ""){
  alert( alerttext );
  } else { 
      document.forms[myform].submit();
    }
  }
  //-->
  </script>

<div class="row-fluid">
<div class="span12">
 <form id="addForm" name="addForm" action="<?php JRoute::_('index.php'.$lb); ?>" method="post">
 <fieldset class="input">
 <legend><?php echo JText::_( 'COM_VIDEOFLOW_EMBED_OPTION' ); ?> </legend>
 <?php
 if (!$vparams->useradd && $usertype != 'Super Administrator' && $usertype != 'Administrator') {
 echo '<div class="alert alert-error vmess">'.JText::_( 'COM_VIDEOFLOW_FEATURE_DISABLED' ). '</div>';
 } else {
 ?>
 <label><?php echo JText::_( 'COM_VIDEOFLOW_EMBED_URL' ); ?></label> 	
   <input type="text" class="input-large" name="embedlink" value="" />	
   <button type="button" onclick="fieldcheck('addForm')" name="upsubmit" class="btn"><?php echo JText::_( 'COM_VIDEOFLOW_APPLY' ); ?></button>
   <?php echo self::genClose(null, JText::_('COM_VIDEOFLOW_CANCEL')); ?>
  <input type="hidden" name="option" value="com_videoflow" />
  <input type="hidden" name="task" value="addmedia" /> 
  <?php
  }
  ?>
  </fieldset>
  </form>
 <form id="uploadForm" name="uploadForm" action="<?php JRoute::_('index.php'.$lb); ?>" method="post">
 <fieldset class="input">
 <legend><?php echo JText::_( 'COM_VIDEOFLOW_UPLOAD_OPTION' ); ?> </legend>
 <?php
 if (!$vparams->showpro || !$vparams->userupload && ($usertype != 'Super Administrator' && $usertype != 'Administrator')) {
 echo '<div class="alert alert-error vmess">'.JText::_( 'COM_VIDEOFLOW_FEATURE_DISABLED' ). '</div>';
 } else {
 ?>
 <label><?php echo JText::_( 'COM_VIDEOFLOW_TITLE_CONT' ); ?></label>
 <input type="text" class="input-large" name="title" value="" />	
 <button type="button" class="btn" onclick="fieldcheck('uploadForm'); return false" name="upsubmit"><?php echo JText::_( 'COM_VIDEOFLOW_APPLY' ); ?></button>
 <?php echo self::genClose(null, JText::_('COM_VIDEOFLOW_CANCEL')); ?>
 <input type="hidden" name="option" value="com_videoflow" />
 <input type="hidden" name="task" value="uploadmedia" /> 
 <?php
 }
 ?>
 </fieldset> 
 </form>   
 </div>
</div>
<?php
}

function editForm($row) {
global $vparams;
$this->loadCss();
JRequest::setVar('activemenu', JText::_('COM_VIDEOFLOW_ADD_MEDIA'));
$pixpreview = $row->pixlink;
    if (!empty($pixpreview)) {
         if ($row -> server == 'local' && stripos($row->pixlink, 'http') === FALSE) {  
         $pixpreview = JURI::root().$vparams->mediadir.'/_thumbs/'.$row->pixlink;
         } else {
         $pixpreview = $row->pixlink;
         }
       } else {
	   if (($row->type == 'jpg' || $row->type == 'png' || $row->type == gif) && !empty($row->file)) {
	   include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
       $p = new videoflowHTMLEXT();
	   $pixpreview = $p->imgResize($row, 'thumb');
	   }
	   if (empty ($pixpreview)) $pixpreview = JURI::root().'components/com_videoflow/players/vflow.gif';
	}

$user = JFactory::getUser();
if (version_compare(JVERSION, '1.6.0', 'ge')) {
    $auth = $user->getAuthorisedGroups();
    if (in_array(8, $auth) || in_array(7, $auth)) $usertype = 'Administrator';
    } else {
    $usertype = $user->usertype;    
    }
$frontside = JRequest::getBool('fr'); 
$auto = JRequest::getBool ('auto'); 
if ($frontside) {
  $vtask = 'saveEdit';
} else if ($auto) {
  $vtask = 'saveFlash';
} else {
  $vtask = 'saveRemote';
}
if (empty($row->id)) $row->id = '';
$action = 'index.php';
if ($vparams->lightbox) $action .= '?tmpl=component';

 ?>

  <div class="row-fluid">
  <div class="span12">
  <?php $this->printMenu();?>
	<form class="form-horizontal" enctype="multipart/form-data" action="<?php echo $action; ?>" method="post" name="adminForm" style="padding:5px;">
	  <fieldset class="input">
	  <legend><?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_DETAILS' ); ?></legend>
    <div class="clearfix well">
    <img src="<?php echo $pixpreview; ?>" class="thumbnail vf_thumb vfcentre" />
    </div>      
	  <div class="control-group">
    <label class="control-label" for="title">
	  <?php echo JText::_( 'COM_VIDEOFLOW_MEDIA_TITLE' ); ?>:
	  </label>
    <div class="controls">
    <input type="text" name="title" value="<?php echo $row->title; ?>" />
    </div>
    </div>
    <div class="control-group">
	  <label class="control-label" for="published">
	  <?php echo JText::_( 'COM_VIDEOFLOW_PUBLISHED' ); ?>:
	  </label>
    <div class="controls">
	  <?php echo JHTML::_('select.genericlist', $row->bselect, 'published', null, 'value', 'text', '1'); ?>
    </div>
    </div>
    <?php
    if ($usertype == 'Super Administrator' || $usertype == 'Administrator') {
    ?>
	  <div class="control-group">
    <label class="control-label" for="featured">
	  <?php echo JText::_( 'COM_VIDEOFLOW_FEATURED' ); ?>:
	  </label>
    <div class="controls">
    <?php 
    echo JHTML::_('select.genericlist', $row->bselect, 'recommended', null, 'value', 'text', '0'); 
    ?>
    </div>
    </div>
    <div class="control-group">
    <label class="control-label" for="date">
	  <?php echo JText::_( 'COM_VIDEOFLOW_DATE' ); ?>:
	  </label>
    <div class="controls">
	  <input type="text" name="dateadded" value="<?php echo $row->dateadded; ?>" />
	  </div>
    </div>
    <div class="control-group">
    <label class="control-label" for="userid">
	  <?php echo JText::_( 'COM_VIDEOFLOW_USERID' ); ?>:
	  </label>
    <div class="controls">
	 <input type="text" class="input-small" name="userid" value="<?php echo $row->userid; ?>" />
   </div>
   </div>
    <?php
    } else {
    ?>
    <input type="hidden" name="userid" value="<?php echo $row->userid; ?>" >
    <input type="hidden" name="dateadded" value="<?php echo $row->dateadded; ?>">
    <?php
    }
    ?>
    <div class="control-group">
    <label class="control-label" for="category">
	  <?php echo JText::_( 'COM_VIDEOFLOW_CAT' ); ?>:
	  </label>
    <div class="controls">
	  <?php echo JHTML::_('select.genericlist', $row->catlist, 'cat', null, 'catid', 'name', $row->selcat); ?>
	  </div>
    </div>
    <div class="control-group">
    <label class="control-label" for="tags">
	  <?php echo JText::_( 'COM_VIDEOFLOW_TAGS' ); ?>:
	  </label>
    <div class="controls">
    <input type="text" name="tags" value="<?php echo $row->tags; ?>" />
	  </div>
    </div>
    <div class="control-group">
    <label class="control-label" for="description">
	  <?php echo JText::_( 'COM_VIDEOFLOW_DESC' ); ?>:
	  </label>
    <div class="controls">
	  <textarea name="details" rows="4" value="" wrap="soft"><?php echo stripslashes($row->details); ?></textarea>
    </div>
    </div>
    <div class="control-group">
    <div class="controls">
    <button onclick="submit()" name="submit_button" class="btn" type="button"><?php echo JText::_( 'COM_VIDEOFLOW_SAVE' ); ?></button> 
    <?php echo self::genClose(null, JText::_('COM_VIDEOFLOW_CANCEL'));?>
    </div>
    </div>
    <input type="hidden" name="medialink" value="<?php echo $row->medialink; ?>" />
    <input type="hidden" name="file" value="<?php echo $row->file; ?>" />
    <input type="hidden" name="pixlink" value="<?php echo $row->pixlink; ?>">
    <input type="hidden" name="type" value="<?php echo $row->type; ?>" />
    <input type="hidden" name="server" value="<?php echo $row->server; ?>">
    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="option" value="com_videoflow" />
    <input type="hidden" name="task" value="<?php echo $vtask; ?>" />
    </fieldset>    
 	<?php echo JHTML::_( 'form.token' ); ?>
  </form>
</div> 
</div> 
<?php	
}    

function emailForm($media)
{
global $vparams;
$app = JFactory::getApplication();
if (empty($vparams->adminmail)) $vparams->adminmail = $app->getCfg ('mailfrom'); 
$enotice =  JText::_('COM_VIDEOFLOW_EMAIL_FRIEND');
$action = JText::_('SEND');
$vtask = JRequest::getCmd('task');
if ($vtask == 'report'){
$enotice = JText::_('COM_VIDEOFLOW_REPORT_THIS_TO_ADMIN');
$action = JText::_('COM_VIDEOFLOW_EREPORT');
}
if ($vtask == 'eshare'){
$enotice = JText::_('COM_VIDEOFLOW_INVITE_FRIEND');
$action = JText::_('COM_VIDEOFLOW_EINVITE');
}
$doc = JFactory::getDocument();
$doc->setTitle($media->title);
if ($vtask == 'email') {
$elink = $media->elink;
} else {     
$elink = base64_encode ($media->elink);
}
    if (!empty($media->pixlink)) {
         if (stripos($media->pixlink, 'http://') === FALSE) {  
         $media->pixlink = JURI::root().$vparams->mediadir.'/_thumbs/'.$media->pixlink;
         } 
      } else {
    $media->pixlink = JURI::root().'components/com_videoflow/players/vflow.gif';
    }

    ?>
  <script language="javascript" type="text/javascript">
    function submitbutton() {
	var form = document.frontendForm;
	if (form.email.value == "" || form.youremail.value == "") {
	  alert( '<?php echo JText::_("COM_VIDEOFLOW_WARN_EADDRESSES"); ?>' );
	  return false;
	}
      return true;
    }
  </script>		 
  <?php 
  if (JVERS3 && !$vparams->lightbox) {
  $this->printMenu();
  }
  ?>
  <div class="row-fluid">
  <div class="span12">
  <title><?php echo $media->title; ?> </title>
  <form action="index.php?option=com_videoflow&task=emailsend&tmpl=component" name="frontendForm" method="post" onSubmit="return submitbutton();">
  <div style="margin:auto; text-align:center;"><img class="thumbnail vf_thumb well vfcentre" src="<?php echo $media->pixlink; ?>">
  </div>
	<h4><?php echo $media->title; ?></h4>
	<i><?php echo $enotice; ?></i>  <br /> <br />
	<?php 
	if ($vtask != 'report'){
	?>
  <fieldset class="input">
  <label><?php echo JText::_("COM_VIDEOFLOW_FRIEND_NAME"); ?></label>
	<input type="text" name="friendname" class="input-medium">
	<label><?php echo JText::_("COM_VIDEOFLOW_FRIEND_EMAIL"); ?></label>
	<input type="text" name="email" class="input-medium" />
	<?php
	}
	?>
	<label><?php echo JText::_('COM_VIDEOFLOW_YOUR_NAME'); ?></label>
  <input type="text" name="yourname" class="input-medium" />
  <label><?php echo JText::_('COM_VIDEOFLOW_YOUR_EMAIL'); ?></label>
	<input type="text" name="youremail" class="input-medium" />
	<label><?php echo JText::_('COM_VIDEOFLOW_EMESSAGE_TITLE'); ?> </label>
	<input type="text" name="subject" class="input-medium" />
  <label><?php echo JText::_('COM_VIDEOFLOW_YOUR_MESSAGE'); ?></label>
	<textarea name="personalmessage" rows="3"></textarea>
  <div style="clear:both;"><input type="submit" name="submit" class="btn" value="<?php echo $action; ?>" />&nbsp;&nbsp;
	<?php echo self::genClose(null, JText::_('COM_VIDEOFLOW_ECANCEL'));?>
	</div>
  </fieldset>
  <input type="hidden" name="id" value="<?php echo $media->id; ?>" />
  <input type="hidden" name="elink" value="<?php echo $elink; ?>" />
  <input type="hidden" name="title" value="<?php echo $media->title; ?>">
	<?php 
  echo JHTML::_( 'form.token' ); 
  if ($vtask == 'report'){
	echo '<input type="hidden" name="friendname" value="Admin" />';
	echo '<input type="hidden" name="email" value="'.$vparams->adminmail.'" />';
  }
      ?>
    </form>
  </div>
  </div>
  <?php
  }

  function printMenu(){
	if (is_array($this->menu)) {
          include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
          $m = new videoflowHTMLEXT();
          $m->menu = $this->menu;
          $m->printMenu();
        } 
    }
  
   
  function genClose($url = null, $name = null){
    global $vparams;
    if (!$url) $url = JRoute::_('index.php?option=com_videoflow');
    if (!$name) $name = JText::_('COM_VIDEOFLOW_CLOSE');
    $tmpl = JRequest::getVar('tmpl');
    if ($tmpl == 'component' && !empty($vparams->lightbox)) {
      if ($vparams->lightbox && $vparams->lightboxsys == 'colorbox' && JVERS3) {
	     $butt = '<button type="button" name="cancel" class="btn" onclick="parent.jQuery.colorbox.close();">'.$name.'</button>';
       } elseif (version_compare(JVERSION, '1.6.0', 'ge')) {
	     $butt = '<button type="button" name="cancel" class="btn" onclick="window.parent.SqueezeBox.close();">'.$name.'</button>';
      } elseif (version_compare(JVERSION, '1.6.0', 'lt')) {
	     $butt = '<button type="button" name="cancel" class="btn" onclick="window.parent.document.getElementById(\'sbox-window\').close();"'.$name.'</button>';
      } else {
	     $butt = '<button type="button" name="cancel" class="btn" onClick="window.parent.closeVfmBox();">'.$name.'</button>';
      }
    } else {
      $butt = '<button type="button" name="cancel" class="btn" onClick="window.location.href=\''.$url.'\';">'.$name.'</button>';
    } 
    return $butt;
  }
  
  function loadCss(){
  $doc = JFactory::getDocument();
  $css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/vfresp.css';
  $doc->addStyleSheet( $css, 'text/css', null, array() );
  }
  
}