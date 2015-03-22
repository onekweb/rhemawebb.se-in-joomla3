<?php
//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.0
* @ Copyright (C) 2008 - 2012 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no liability arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/
 
// No direct access

defined('_JEXEC') or die('Restricted access'); 

global $vparams;

$fbuserdata = JRequest::getVar('fbuserdata');
$media = $this->media;
if (empty($media->shortname)) $media->shortname = JText::_('COM_VIDEOFLOW_GUEST');
$fbuser = JRequest::getVar('fbuser', '');
$juser = JRequest::getVar('juser');
if (empty($juser)) $juser = JFactory::getUser();
$task = JRequest::getCmd ('task');
$lo = '&layout=listview';
$flowid = JRequest::getInt('Itemid', $vparams->flowid);
  if (!empty($flowid)) $flowid = '&Itemid='.$flowid; else $flowid = '';
$xparams = $this->getXparams();
$iborderc = (string) $xparams->get('iborderc', '#EDEDED');
$showuser = (bool) $xparams->get('showuser', 1);
$showcat = (bool) $xparams->get('showcat', 1);
$showviews = (bool) $xparams->get('showviews', 1);
$showrating = (bool) $xparams->get('showrating', 1);
$showadd = (bool) $xparams->get('showadd', 1);
$showlike = (bool) $xparams->get('likebutton', $vparams->likebutton);
$showdate = (bool) $xparams->get('showdate', 1);
$showdownloads = (bool) $xparams->get ('showdownloads', 1);
$showplaylistcount = (bool) $xparams->get('showplaylistcount', 1);
$showcommentcount = (bool) $xparams->get('showcommentcount', 1);
$likecolour = (string) $xparams->get('likecolour', 'light');
if (!empty($showlike)) {
$sharelink = JRoute::_(JURI::root().'index.php?option=com_videoflow&task=play&id='.$media->id);
$share = '<fb:like href="'.$sharelink.'" layout="button_count" show_faces="false" colorscheme="'.$likecolour.'"></fb:like>';
} else {
$share = null;    
}

$doc = JFactory::getDocument();

    //Check is we are using swfobject to load the player. This is the preferred method.
    if (stripos ($media->embedcode, 'swfobject.embedSWF') !== FALSE){
    //Load swfobject javascript file
    $swfobject = JURI::root().'components/com_videoflow/jscript/swfobject.js';
    $doc->addScript($swfobject); 
    //Load the player using swfobject
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){ $media->embedcode })");
    $altcontent = '';
    } elseif (stripos ($media->embedcode, "jwplayer('vfmediaspace').setup") !== FALSE){
    //If using JW custom JS
		if (!empty($vparams->jwplayerurl)) {
		$jwjs = $vparams->jwplayerurl;
		} else {
		$jwjs = JURI::root().'components/com_videoflow/jscript/jwplayer.js';
		}
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){ $media->embedcode })");
    $altcontent = '';           
    } elseif (stripos ($media->embedcode, "YT.Player") !== FALSE) {
    $yt = JURI::root().'components/com_videoflow/jscript/youtube.js';
    $doc->addScript($yt);
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){ $media->embedcode })");
    $altcontent = '';  
    } else {
    //If using the standard embed method, replace alternative content with the player
     $altcontent = $media->embedcode;
    }
    
//Rating system
    if ($vparams -> ratings) {   
    if (($vparams->jsframework == 'auto' && !JVERS3) || $vparams->jsframework == 'mootools') {
    if (!empty($vparams->ratinglegacy)) {
    $vratejs = JURI::root().'components/com_videoflow/extra/votitaly/js/votitaly_legacy.js';
    } else {
    $vratejs = JURI::root().'components/com_videoflow/extra/votitaly/js/votitaly.js?';
    }
    $vratecss = JURI::root().'components/com_videoflow/extra/votitaly/css/votitaly.css';
    $doc->addStyleSheet( $vratecss, 'text/css', null, array() );
    $vrate ='
    '."
	   window.addEvent('domready', function(){
	     var vf_rate = new VotitalyPlugin({
	  	    submiturl: '".JURI::base()."index.php?option=com_videoflow&task=vote&format=raw',
		      loadingimg: '".JURI::base()."components/com_videoflow/extra/votitaly/images/loading.gif',
			    show_stars: ".($vparams->showstars ? 'true' : 'false').",
			    star_description: '".addslashes($vparams->stardesc)."',		
			    language: {
				  updating: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_UPDATING'))."',
				  thanks: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_THANKS'))."',
				  already_vote: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_ALREADY_VOTED'))."',
				  votes: '".addslashes(JText::_( 'COM_VIDEOFLOW_VOTES'))."',
				  vote: '".addslashes(JText::_( 'COM_VIDEOFLOW_VOTE'))."',
				  average: '".addslashes(JText::_( 'COM_VIDEOFLOW_AVERAGE'))."',
				  outof: '".addslashes(JText::_( 'COM_VIDEOFLOW_TOTAL_SCORE'))."',
				  error1: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_ERR'))."',
				  error2: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_ALREADY_VOTED'))."',
				  error3: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_RANGE'))."',
				  error4: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_LOGIN'))."',
				  error5: '".addslashes(JText::_( 'COM_VIDEOFLOW_RATE_ALREADY_SUBMITTED'))."'
			    }
	       });
	     });
      ".'
    ';
    } else {
    $rpath = JURI::root().'components/com_videoflow/extra/vfrating/images';
    $vratejs = JURI::root().'components/com_videoflow/extra/vfrating/jquery.raty.js';
    $vrate = "
    jQuery(document).ready(function(){   
    jQuery('#vfrate').raty({
    path : '$rpath',
    number: 5,
    hints: ['".JText::_('COM_VIDEOFLOW_TERRIBLE')."', '".JText::_('COM_VIDEOFLOW_ORDINARY')."', '".JText::_('COM_VIDEOFLOW_OKAY')."', '".JText::_('COM_VIDEOFLOW_QUITE_GOOD')."', '".JText::_('COM_VIDEOFLOW_BRILLIANT')."']
    });
    jQuery('.vfratebar').tooltip();
    });";		          
  }
    $doc->addScript ($vratejs);
    $doc->addScriptDeclaration ($vrate);
}
// Load VideoFlow Height Control JS  
  
  $switch1 = JText::_('COM_VIDEOFLOW_SHOW_MORE');
  $switch2 = JText::_('COM_VIDEOFLOW_SHOW_LESS');

  $vfswitch = 'function vfswitch(id, txt) {
       var s = document.getElementById(id);
       var x = document.getElementById (txt);
       if(s.style.overflow == "hidden") {
          s.style.overflow = "auto";
          x.innerHTML = "[ '.$switch2.' ]";
       }else{
          s.style.overflow = "hidden";
          x.innerHTML = "[ '.$switch1.' ]";
          }
      }
      function vfshare_display (id){
      var v = document.getElementById(id);
        if (v.style.display == "block") {
        v.style.display = "none";
        } else {
        v.style.display = "block";
        }
      }';

  $doc->addScriptDeclaration ($vfswitch);

//Default thumbnail
if (empty($media->pixlink)) $media->pixlink = JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/listview/default_thumbnail.gif';

?>

<div class="vfbox"> 
<table class="vf_table">
<tbody>
<tr>
<td name="player_AND_comments_and_mods_2_4_6" valign="top">
<table name="player_AND_comments_and_mods_2_4_6" cellspacing="0" cellpadding="0">
<tbody>
<?php
if (!empty($this->vflow3)) { 
echo '<tr><td name="vmodule2" valign="top"><div style="margin-bottom:8px;">'.$this->vflow3.'</div></td></tr>';
}
?>
<tr><td name="player_title" valign="top"><div id="vfmediatitle"><?php echo stripslashes ($media->title); ?></div></td></tr>
<tr><td name="player" valign="top"><div id="vfmediaspace" style="min-height:<?php echo $vparams->playerheight.'px'; ?>;"><?php echo $altcontent; ?></div></td></tr>
<tr><td name="vf_share" valign="top">
<div style="width:<?php echo $vparams->playerwidth;?>px; margin:auto;">
<?php
if ($showlike){
echo '<div class="mod_vfshare">'.$share.'</div>';
}
if (!empty($this->rating)) echo '<div class="vf_rating">'.$this->rating.'</div>'; 
?>
</div>
</td></tr>
<tr><td name="vmodule4" valign="top"><?php echo $this->vflow5; ?></td></tr>
<tr><td name="comments" valign="top"><div style="margin: 10px 0px"><?php if (!empty($this->comments)) echo $this->comments; ?></div></td></tr>
<tr><td name="vmodule6" valign="top"><?php echo $this->vflow6; ?></td></tr>
</tbody>
</table>
</td>
<td name="infoside_rel_mods_3_5" valign="top">
<div id="vf_infoblock">
<table cellspacing="0" cellpadding="0" width="100%">
<tbody>
<tr><td><?php echo $this -> vflow4; ?></td></tr>

<?php
$fbuser = JRequest::getVar('fbuser');
if (!empty($fbuser) || !empty($juser->id)){
echo '<tr><td>';
echo '<div class="vf_bgactive" style="overflow:hidden; margin-bottom:5px;">';
if ($fbuser) {
    $logouturl = JRequest::getString('logouturl');
          if (!empty($logouturl)) {  
            $logout = '<a href="'.$logouturl.'" target="_parent">'.JText::_('COM_VIDEOFLOW_LOGOUT').'</a>';
          } else {
            $logout = '';
          }  
     ?>
     <div class="vf_bgactive" style="position:relative; height:50px; overflow:hidden;">
     <div style="margin-top:20px; float:right; padding-right:10px;">
     <?php echo $logout; ?>
     </div>
     <div style="margin: 0px 5px; float:right;">
        <?php echo '<a href="'.$fbuserdata['link'].'"><img src="http://graph.facebook.com/'.$fbuser.'/picture" alt="'.$fbuserdata['name'].'"/></a>'; ?>     
     </div>
     <div style="margin-top:20px; float:right;"><?php echo '<a href="'.$fbuserdata['link'].'">'.$fbuserdata['first_name'].'</a>'; ?></div>
     </div>
     <?php
     }
?>
<?php 
if (!empty($vparams->showpro)) {
?>
<font class="vflist6">
<div class="vf_borderc" style="overflow:hidden; margin:5px; padding:0px 4px; border-width: 1px; border-style:solid;">
<div class="vf_tools"><img class="vf_tools_icons" src="<?php echo JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/tools/'.
  $vparams->toolcolour.'/channel.gif'; ?>" /><a href="<?php echo JRoute::_('index.php?option=com_videoflow&task=myvids'.$lo.$flowid); ?>" ><?php echo JText::_('My Channel'); ?></a></div>
<div class="vf_tools"><img class="vf_tools_icons" src="<?php echo JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/tools/'.
  $vparams->toolcolour.'/subs.gif'; ?>" /><a href="<?php echo JRoute::_('index.php?option=com_videoflow&task=mysubs'.$lo.$flowid); ?>" ><?php echo JText::_('My Subscriptions'); ?></a></div>
</div>
</font>
<?php
}
?>
<?php
echo '</div>';     
echo '</td></tr>';
}
if (isset($this->owner)) {
$owner = $this->owner;
$owner_text = sprintf(JText::_('COM_VIDEOFLOW_NAME_CHANNEL'), $owner->name);
    if ($owner->id == $juser->id) {
    $owner_text = JText::_('COM_VIDEOFLOW_MY_CHANNEL');
    }
?>
<tr><td>
<div class="vftextbox" style="margin:0px 0px 4px 0px; padding:4px 12px;">
<table name="channel_info" cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr>
<td>
<div class="vf_bgactive" style="overflow:hidden; padding:4px;">
<div style="float:left;"><?php echo $owner_text; ?></div>
<?php
  if (isset($this->subaction) && !empty($vparams->showpro)) {
  echo $this->subaction;
  } 
?>  
</div>
<table name="channel_stats" cellspacing="0" cellpadding="0" border="0" width="100%">
<tbody>
<tr><td>
<div class="vf_cstats1">
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_UPLOAD_COUNT') ?></font>
<font class="vflist5"><?php echo $this->uploadcount; ?></font>
</div>
</td>
<td>
<div class="vf_cstats2"> 
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_FAV_COUNT') ?></font>
<font class="vflist5"><?php echo $this->favcount; ?></font>
</div>
</td></tr>
<tr><td>
<div class="vf_cstats1">
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_TSUBSCRIBERS') ?></font>
<font class="vflist5"><?php echo $this->subcount; ?></font>
</div>
</td>
<td>
<div class="vf_cstats2"> 
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_HIT_COUNT') ?></font>
<font class="vflist5"><?php echo $this->visitcount; ?></font>
</div>
</td></tr>
</tbody>
</table>
</td>
<td valign="top">
<?php
  if (!empty($vparams->facebook) && !empty($this->fbowner)) {
  ?>
<div style="float:right; margin-left:4px;">
 <?php  echo '<a href="'.$this->fbownerdata['link'].'"><img src="http://graph.facebook.com/'.$this->fbowner.'/picture" alt="'.$this->fbownerdata['name'].'"/></a>'; ?>     
</div>
  <?php
  }
  ?>
</td>
</tr></tbody></table>
</div>
</td></tr>
<?php
} 

?>

<tr><td>
<div class="vftextbox" style="margin:0px;">
<table name="media_info" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr><td>
<div id="vf_vidinfo" style="overflow:hidden; margin:0px; padding: 0px 4px;">
<div class="vf_bgactive" style="overflow:hidden; padding:4px;"><?php echo '<b>'.JText::_('COM_VIDEOFLOW_PLAYING').'</b> '.stripslashes($media->shorttitle); ?></div>
<a href="#" id="vf_switch" onclick="vfswitch('vf_vidinfo', 'vf_switch'); return false;"><?php echo '[ '.$switch1.' ]'; ?></a>
<table name="details_photo" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr><td>
<div style="margin-right:5px; min-width:100px; word-wrap:break-word; overflow:hidden;"><?php echo nl2br($this->escape($media -> details)); ?></div>

<?php 
if ($showviews) {
?>
<div class="vf_infostats"> 
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_TVIEWS'); ?></font>
<font class="vflist5"><?php echo $media->views + 1; ?></font>
</div>
<?php
}
if ($showplaylistcount && $vparams->showpro) { 
echo '<div class="vf_infostats"><font class="vflist6">'.JText::_('COM_VIDEOFLOW_TPLAYLISTS').'</font>
      <font class="vflist5">'. $media->favoured .'</font></div>';
}
if ($showdownloads){
?>
<div class="vf_infostats"> 
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_TDOWNLOADS'); ?></font>
<font class="vflist5"><?php echo $media->downloads; ?></font>
</div>
<?php
}
if ($showcat){
?>
<div class="vf_infostats">
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_TCAT'); ?></font>
<font class="vflist5"><a href="<?php echo JRoute::_('index.php?option=com_videoflow&task=cats&cat='.$media->cat.'&sl=categories'.$lo.$flowid); ?>"><?php echo $this->escape($media->catname); ?></a></font>
</div>
<?php
}
?>
<div class="vf_infostats">
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_INFO_TAGS'); ?></font>
<font class="vflist5"><?php echo $media->autotags; ?></font>
</div>
<?php
if ($showdate) {
?>
<div class="vf_infostats">
<font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_TDATE'); ?></font>
<font class="vflist5"><?php echo $media->dateadded; ?></font>
</div>
<?php
}
?>
</td>
<td valign="top">
<div style="max-width:80px">
<a href="<?php echo JRoute::_('index.php?option=com_videoflow&task=visit&cid='.$media->userid.$lo.$flowid); ?>">
<img src="<?php echo $media->pixlink; ?>" style="width:60px; margin-left:6px; border:1px solid <?php echo $iborderc; ?>;" /></a>
</div> 
<span class="vflist6">
<table class="vf_table"><tbody>
<tr><td valign="middle">
<div class="vf_tools">
<img class="vf_tools_icons" src="<?php echo JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/tools/'.
  $vparams->toolcolour.'/contact.gif'; ?>" />
<a href="<?php echo JRoute::_('index.php?option=com_videoflow&task=visit&cid='.$media->userid.$lo.$flowid); ?>"><?php echo $this->escape($media->shortname); ?></a>
</div></td></tr>
</tbody></table>
</span>

</td></tr>
</tbody>
</table>
</div>
</td></tr>
<tr><td>
<?php 

if (is_array($this->tools) && (!empty($this->tools))){ 
echo '<table class="vf_table"><tbody><tr><td valign="top"><font class="vflist5">'.JText::_('COM_VIDEOFLOW_TOOLS').'</font></td>';
echo '<td valign="middle"><font class="vflist6">';
  foreach ($this->tools as $key=>$value){
  echo '<div class="vf_tools"><img class="vf_tools_icons" src="'.
  JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/tools/'.
  $vparams->toolcolour.'/'.$key.'.gif" />'.$value.'</div>';
  } 
echo '</font></td>';
echo '</tr></tbody></table>';
}
?>
</td></tr>
<tr><td><div id="vfshare" style="display:none;"><?php echo $this->vfshare; ?></div></td></tr>
<tr><td>
<div id="linkform"><form id="linkform" name="linkform" action="">
<label for="media_link"><font class="vflist6"><?php echo JText::_('COM_VIDEOFLOW_LINK'); ?></font></label>
<input type="text" name="media_link" readonly="1" onClick="javascript:document.linkform.media_link.focus();document.linkform.media_link.select();" value="<?php echo JRoute::_(JURI::root().'index.php?option=com_videoflow&task=play&id='.$media->id); ?>" />
<label for="media_embed"></label>
</form></div>
</td></tr>
<tr><td></td></tr>
</tbody>
</table>
</div>
</td></tr>
<tr><td></td></tr>
<tr><td>
<!-- START VIDOFLOW TABS -->
<?php
if ($vparams->showtabs){
  echo $this->loadTemplate('tabs');
}
?>
<!-- END VIDEOFLOW TABS -->
</td></tr>
<tr><td><?php echo $this->vflow7; ?></td></tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>