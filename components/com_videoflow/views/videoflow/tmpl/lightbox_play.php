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
//Get settings
global $vparams;
//Load Stylesheet
$doc = JFactory::getDocument();
$css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/lightbox_play.css';
$doc->addStyleSheet( $css, 'text/css', null, array() );
$context = JRequest::getCmd('c');
$xparams = $this->getXparams();
$likecolour = (string) $xparams->get('likecolour', 'light');
if ($vparams->lightboxsys == 'multibox' && $context != 'fb' || $vparams->lightboxsys == 'colorbox' && $vparams->cboxtheme == 'dark') {
    $classvfbg = 'vf_bgdark';
    $likecolour = 'dark';
    $css2 = 'body {background-color: #000000 !important; color:#FFFFFF !important;}';
    $doc->addStyleDeclaration($css2);
} else {
    $classvfbg = 'vf_bglight';
} 
$xp = JRequest::getInt('xp');
if ($context == 'fb') {
$showshare = $vparams->fbshare;
} else {
$showshare = (bool) $xparams->get('showshare', $vparams->likebutton);
}
$media = $this->media;  
$mspaceclass = ''; 
if (!empty($showshare)) {
$locale = JRequest::getString('locale');
if(empty($locale)) $locale = 'en_US';
$sharelink = JRoute::_(JURI::root().'index.php?option=com_videoflow&task=play&id='.$media->id);
    if ($vparams->fbsharecode == 'iframe') {
    $sharelink = urlencode($sharelink);   
    $share = '<iframe src="//www.facebook.com/plugins/like.php?href='.$sharelink.'&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;locale='.$locale.'&amp;height=21&amp;width=90" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px; width:90px;" allowTransparency="true"></iframe>';
    } elseif ($vparams->fbsharecode == 'xfbml') {
    $share = '<fb:like href="'.$sharelink.'" width="90" layout="button_count" action="like" colorscheme="'.$likecolour.'" show_faces="false" share="false"></fb:like>';
    } else { 
    $share = '<div class="fb-like" data-href="'.$sharelink.'" data-layout="button_count" data-colorscheme="'.$likecolour.'" data-action="like" data-show-faces="false" data-width="90" data-send="false"></div>';
    }
} else {
$share = null;    
}
if($vparams->twitterbutton) {    
$twitter = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$sharelink.'"';
if(!empty($vparams->twitterhandle)) $twitter .=' data-via="'.$vparams->twitterhandle.'"';
if (!empty($vparams->hashtags)) $twitter .=' data-hashtags="'.$vparams->hashtags.'"';
$twitter .='>Tweet</a>';
$twitter .="<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
}

$emethod = 'html4'; 

$altcontent = $this->setMedia();

if ($emethod == 'jwjs') $playeroid = 'vfmediaspace'; else $playeroid = 'vf_fidsPlayer';

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
    
if ($vparams->playall && $vparams->prostatus) {
$lboxsys = $vparams->lightboxsys;    
$vfbox = "
  var vf_player;
  var vfState = 'fideri';
  
  function computeEnd(){
    myVfbox(0);
  }
			
  function playerReady(obj) {
  vf_player = document.getElementById('$playeroid');  
  vf_player.addModelListener('STATE', 'myVfbox');
  };
  
  function onYouTubePlayerReady(obj) {
  vf_player = document.getElementById ('vf_fidsPlayer');
  vf_player.addEventListener('onStateChange', 'myVfbox');
  }
  
  function onDailymotionPlayerReady(obj){
  vf_player = document.getElementById ('vf_fidsPlayer');
  vf_player.addEventListener('onStateChange', 'myVfbox');
  }
  
  function myVfbox(obj){
  var	currentState = obj.newstate; 
  var vfState = obj;
    if (currentState === 'COMPLETED' || vfState === 0){
    if ('$lboxsys' == 'multibox') {
    parent.document.getElementById('MultiBoxNext').fireEvent('dblclick');
    } else {
    parent.jQuery.colorbox.next();
    }
    }
  }
"; 
  if (empty($xp)) {
  $doc->addScriptDeclaration ($vfbox);		   
  }
}
           
?>

<div id="vf_multibox" class="<?php echo $classvfbg; ?>" style="z-index:10; overflow:hidden;">
<table class="vftable">
<tbody>
<tr><td style="text-align:center;" align="center">
<div id="vfmediaspace" class="<?php echo $mspaceclass;?>" style="min-height:<?php echo $vparams->lplayerheight.'px; width:'.$vparams->lplayerwidth.'px; z-index:100';?>"><?php echo $altcontent; ?></div>
</td></tr>
<tr><td>
<div style="width:<?php echo $vparams->lplayerwidth;?>px; margin:auto;">
<?php
if (!empty($showshare)){
echo '<div class="mod_vfshare">'.$share.'</div>';
}
if (!empty($vparams->twitterbutton)){
echo '<div class="mod_vfshare">'.$twitter.'</div>';
}
if (!empty($this->addthis)) {
echo '<div class="mod_vfshare">'.$this->addthis.'</div>';    
}
if (!empty($this->rating)) echo '<div class="vf_rating">'.$this->rating.'</div>'; 
?>
</div>
</td></tr>
<tr><td align="center">
<div id="mod_vflow5"><?php echo $this->vflow5; ?></div>
</td></tr>
</tbody>
</table>
</div>