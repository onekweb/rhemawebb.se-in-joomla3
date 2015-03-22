<?php

//VideoFlow - Joomla Multimedia System for Facebook//
/**
* @ Version 1.2.2 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// No direct access
defined ('_JEXEC') or die('Restricted access'); 
global $vparams;

//Load template stylesheet

$context = JRequest::getCmd('c');
$iframe = JRequest::getBool('iframe');
$varext = '';
$doc = JFactory::getDocument();
$css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/simple.css';
$doc->addStyleSheet( $css, 'text/css', null, array() );
$xparams = $this->getXparams();
$iborderc = (string) $xparams->get('iborderc');
$bgactive = (string) $xparams->get('bgactive');
$bginactive = (string) $xparams->get('bginactive');
$vflabel = (string) $xparams->get('vflabel');
$vflabelfont = (string) $xparams->get('vflabelfont');
$flowid = JRequest::getInt('Itemid');
if (!empty($flowid)) $flowid = '&Itemid='.$flowid; else $flowid = '';
$type = JRequest::getVar('type');
if (!empty($type)) $type = '&type='.$type; else $type = '';
$tmpl = JRequest::getCmd('tmpl');
if (!empty($tmpl)) $tmpl = '&tmpl='.$tmpl; else $tmpl = '';
$css2 = '';
$fb = '';
if ($context == 'fb') {
//$fb = '&c=fb'; 
$css2 .= '.container-fluid {
		 width: 100%;
		 height: 100%;
		 margin: 0;
		 padding: 0;
		 border: 0;
		}';
} 

$css2 .= '.vf_thumb, .vf_dthumb {
         width:'.$vparams->thumbwidth.'px;
         height:'.$vparams->thumbheight.'px; 
         position:relative;         
        }';

//Mobile devices specific styles to override global styles
$css2 .= '
@media (min-width: 480px) and (max-width: 767px) {
   .vfmobtitle {
   width: 60% !important;
   float:left !important;
   }
   
   .vfmobthumb {
   width: 30% !important;
   float:left !important;
   margin-right: 20px
   }
   
  .vf_thumb {
   width: 100%;
   max-width:120px;
   height:auto;
   float:left; 
   position:relative;   
  }
  
  .vfinfolist{
  width:50%;
  float:left;
  }
}
  
@media (max-width: 479px){
  
  .vfmess{
  width: 80%;
  }
  
  .vf_thumb {
   width:100%;
   max-width:240px;
   height:auto; 
   align:left; 
   position:relative;   
  }
  
  .vf_rating{
  clear:both;
  float:left;
  margin:auto;
  text-align:center;
  width:100%;
  }
  
  .mod_vfshare{
  text-align:center;
  width:48%
  margin: 0px;
  }
  
  .vfinfolist{
  width:50%;
  float:left;
  }
  
  .vfleft{
  float:left;
  margin-left:0px;
  }
  
  .vfright{
  float:right;
  margin-right: 0px;
  }
  
  #vfrate{
  float:left;
  }
  
  #vfresp{
  float:right;
  }
  
  .vfnophone, .vjs-volume-control, .vjs-mute-control, .vjs-fullscreen-control {
  display: none;
  width: 0px !important;
  }
  
  .vfw90{
  width:100%;
  }
}';

$doc->addStyleDeclaration($css2);


$list = JRequest::getWord ('list');
if (!empty($list)) $list = '&list='.$list; else $list = '';
// Get parameters
$iborders = (string) $xparams->get('iborders');
$borders = (int) $xparams->get('borders', 1);
if ($context == 'fb') {
$showuser = $vparams->fbshowuser;
$showcat = $vparams->fbshowcategory;
$showdate = $vparams->fbshowdate;
$showviews = $vparams->fbshowviews;
$showrating = $vparams->fbshowrating;
$showplaylistcount = $vparams->fbshowplaylists;
$showlike = 0;
$showadd = $vparams->fbshowmylist;
} else {
$showadd = (bool) $xparams->get('showadd', $vparams->showadd);
$showuser = (bool) $xparams->get('showuser', $vparams->showuser);
$showcat = (bool) $xparams->get('showcat', $vparams->showcat);
$showviews = (bool) $xparams->get('showviews', $vparams->showviews);
$showrating = (bool) $xparams->get('showrating', $vparams->showrating);
$showdate = (bool) $xparams->get('showdate', $vparams->showdate);
$showplaylistcount = (bool) $xparams->get('showplaylistcount', $vparams->showplaylistcount);
$showlike = (bool) $xparams->get('listlikebutton', 0);
}
$showvotes = (bool) $xparams->get('showvotes', $vparams->showvotes);
$showdownloads = (bool) $xparams->get ('showdownloads', $vparams->showdownloads);
$likelayout = (string) $xparams->get('likelayout', 'standard');
$likecolour = (string) $xparams->get('likecolour', 'light');
$likefaces = (bool) $xparams->get('likefaces', true);
$fbuserdata = JRequest::getVar('fbuserdata');

/********* DON'T EDIT THIS PART UNLESS YOU KNOW WHAT YOUR DOING **********/
                    
          // Controls link parameters  
  
  $vtask = JRequest::getCmd('task', 'latest');
  $stask = 'play';
  $sl = '';
  if (!empty ($vtask) && $vtask != 'visit') $sl = '&sl='.$vtask;
  $ls = JRequest::getInt ('limitstart', null);
  if ($ls > 0) $ls = '&limitstart='.$ls;
  $cid = JRequest::getInt('cid');
  if (!empty($cid)) $cid = '&cid='.$cid; else $cid = '';
  if ($vparams->lightboxfull) $xp = '&xp=1'; else $xp = '';
  $lo = JRequest::getCmd('layout');
  if (!empty($lo)) $lo = '&layout='.$lo; else $lo = ''; 
  if ($context == 'fb') {
  $target = 'target="_parent"';
  $lo = '';
  } else {
  $target = '';  
  }

  // status messages
        
  if ($vtask == 'search') {
    $smessage = '';
    $vs = JRequest::getString('vs');
    $searchword = JRequest::getString('searchword');
    if (!empty($this->vlist)) {
      $varext .= '&searchword='.$searchword;
      if ($vs == 'rel' ) {
      $reltitle = JRequest::getString('title');
      $smessage = '<div class="alert alert-success vmess">'. JText::_('COM_VIDEOFLOW_RELATED_TO').' <b>'.$reltitle.'</b></div>';
      } else {
      $smessage ='<div class="alert alert-success vmess">'. JText::_('COM_VIDEOFLOW_RESULTS_FOR').' <b>'.$searchword.'</b></div>';  
      }
    } else if (empty($this->vlist) && empty($this->media) && (!empty ($searchword))){
    $smessage = '<div class="alert vmess">'. JText::_('No results found for').' <b>'.$searchword.'</b>.'.JText::_('COM_VIDEOFLOW_TRY_NEW_TERM').'</div>';
    } else if (empty($this->vlist) && empty ($searchword)){
    $smessage = null;
    } else {
    $smessage ='<div class="alert alert-success vmess">'. JText::_('COM_VIDEOFLOW_RESULTS_FOR').' <b>'.$searchword.'</b></div>';  
    }
  }


// Display "videos by user" message if necessary
if ($vtask == 'uservids'){
$pid = JRequest::getInt('usrid');
$stask = 'visit&pid='.$pid;
$vuser = JRequest::getString('usrname', 'Guest');
$smessage = '<div class="alert alert-info vmess">'. JText::_('COM_VIDEOFLOW_MEDIA_FROM').' <b>'.$vuser.'</b></div>';
}
//Display "videos liked by user" message if necessary
if ($vtask == 'userfavs'){
$pid = JRequest::getInt('usrid');
$stask = 'visit&pid='.$pid.'&tab=two';
$vuser = JRequest::getString('usrname', 'Guest');
$smessage = '<div class="alert alert-info vmess">'. JText::_('COM_VIDEOFLOW_LIKED_BY').' <b>'.$vuser.'</b></div>';
}
// Display "videos in category" if necessary
if ($vtask == 'cmed'){
$cname = JRequest::getString('cname');
$smessage = '<div class="alert alert-info vmess">'. JText::_('COM_VIDEOFLOW_TCAT').' <b>'.JText::_($cname).'</b></div>';
}
// Flat category view
$catid = null;
if ($vtask == 'cats'){
$smessage = '<div class="alert alert-info vmess">'.JText::_('COM_VIDEOFLOW_TCAT').' <b>'.JText::_($this->catname).'</b></div>';
$catid = JRequest::getInt('cat');
$catid = '&cat='.$catid;
}

/**************************************************************************/
/* 
* You may edit some parts below
*/ 

/* 
* VideoFlow basic layout based on Bootstrap 2.x. Each unit must add up to 12 columns (span12), which is "width:100%" in css terms. 
* Refer to Bootstrap 2.x documentation for details.  Related settings for the player area are contained in simple_play.php 
*/ 

/* 
* First, we set the default settings, and then vary them according to your VideoFlow template and features configuration. 
*/ 

//Content area and right side module 
$vfmaincontent = 'span10'; //Main content area
$vfrightmod = 'span2'; // Right hand module

//Medialist area
$vfthumbspan = 'span3'; //Item thumbnail
$vftitlespan = 'span7'; // Item title and description 
$vfsideinfo = 'span2'; // Item data

// Make content area full width if right side module is not published
if (ISMOBILE || empty($this->vflow2)) {
$vfmaincontent = 'span12'; $vfrightmod = '';
}

//Extend item title and description area if displaying item data is disabled

if (!$showuser && !$showviews && !$showplaylistcount && !$showdownloads && !$showrating && !$showcat ) {
$vftitlespan = 'span9';
}


?>
<!-- VideoFlow Main Container-->
<div class="container-fluid">
  <div class="row-fluid">
      <!-- VideoFlow content area -->  
		<div class="<?php echo $vfmaincontent;?>">  
         <div id="vmess" class="vfmess"></div>     
        <!-- VideoFlow header and navigation -->  
    <!-- Start menu 1 -->	    
     <?php $this->dispMenu(); ?>  
		<!-- End menu 1 -->
		<!-- Start Menu 2 --> 
		<?php 
        if (!empty ($this->menu2) || !empty($this->cname) || !empty($this->cpix)){
        ?>
		<div class="row-fluid">
			<?php
            if (!empty($this->cname)) {
            ?>
				<div class="pull-left">
				<span class="label">
					<?php echo $this->cname; ?>
				</span>
				</div>
				<div class="pull-left">
					<?php if ($this->cpix) echo $this->cpix; ?>  
				</div>
            <?php
            }
            if (!empty($this->menu2)){
            ?>
				<div class="pull-right">
					<?php echo $this->menu2;?>
				</div>
            <?php
            }
            ?>
        </div>                 
        <?php
        }
        ?>
    <!-- End Menu2 --> 
 
		<!-- Start banner section-->
		<?php
		$fbuser = JRequest::getVar ('fbuser');
		if (!empty($this->promptperm)) {
		?>
			<div class="">
				<?php echo $this->promptperm;?>
			</div>
		<?php
		}                  
		if (!empty($this->notice)) {
		?>
			<div class="">
				<?php echo $this->notice; ?>
			</div>              
		<?php
		}
		if ($this->vflow1) {
		?>
			<div class="">
				<?php echo $this->vflow1; ?>
			</div>
		<?php
		}
		if ($fbuser) {
		?>
			<div class="">
				<?php echo '<a href="'.$fbuserdata['link'].'">'.$fbuserdata['first_name'].'</a>'; ?>
			</div>
			<div class="">
				<?php echo '<a href="'.$fbuserdata['link'].'"><img src="http://graph.facebook.com/'.$fbuser.'/picture" alt="'.$fbuserdata['name'].'"/></a>'; ?> 
			</div>
			<?php
			$logouturl = JRequest::getString('logouturl');
			if (!empty($logouturl)) {
			?>
				<div class="">
					<?php echo '<a href="'.$logouturl.'" target="_parent">'.JText::_('COM_VIDEOFLOW_LOGOUT').'</a>'; ?>
				</div>                
			<?php					
			}
		}                  
		if (!empty($smessage)) {
			echo $smessage;
		}
		?>
	
    <!-- End banner section -->
<!-- End VideoFlow header and navigation-->
					               
<?php
/*************** IT IS NOT NECESSARY TO CHANGE THIS PART ****************
**************** BE CAREFUL IF YOU CHOOSE TO MODIFY IT ******************/

// Load search form 
if ($vtask == 'search') {
  $vs = JRequest::getString('vs');
  $id = JRequest::getInt('id');
  if ($vs != 'rel' && empty($id)) {
    if (!JVERS3) $stemp = new JView; else $stemp = new JViewLegacy;
    $stemp->_layout = 'listview';
    $stemp->_addPath( 'template', JPATH_COMPONENT_SITE . DS . 'views' . DS . 'videoflow' . DS . 'tmpl' );
    echo $stemp->loadTemplate('search');
  }
}

//Display login form if necessary
if ($vtask == 'login'){
echo $this->loadTemplate('login');
}
// Or logout form
if ($vtask == 'logout'){
echo $this->loadTemplate ('login');
}

if (isset($this->media)) echo $this->loadTemplate('play');

$dcount = 0;
if (!empty($this->vlist) && is_array ($this->vlist)){
  
  $dcount = count($this->vlist);
  if (!empty($this->tabone)) $tabone = $this->tabone; else $tabone = array();
  $mbox = 1000000; 
  $mboxx = 1;
  if ($vparams->iconplay) $iconplay = '<div class="vf-playiconwrap"><span class="vf-playicon"><i class="vf-icon-'.$vparams->playicon.'"></i></span></div>'; else $iconplay = '';  
  foreach ($this->vlist as $vid){
      
      //Set sharelink
      $sharelink = JRoute::_(JURI::root().'index.php?option=com_videoflow&task=play&id='.$vid->id);
  
      // Set thumbnail link
      
      if ($vid->type == 'jpg' || $vid->type == 'png' || $vid->type == 'gif') {
         if (empty($vid->pixlink) && !file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$vid->title.'.jpg')) {
         $vid->pixlink = $this->imgResize($vid, 'thumb');
         }
        }
      
      if (!empty($vid->pixlink)) {
         if (stripos($vid->pixlink, 'http://') === FALSE) {  
         $vid->pixlink = JURI::root().$vparams->mediadir.'/_thumbs/'.$vid->pixlink;
         }
       } else if (empty($vid->pixlink) && file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$vid->title.'.jpg')){       
       $vid->pixlink = JURI::root().$vparams->mediadir.'/_thumbs/'.$vid->title.'.jpg';
       } else { 
      $vid->pixlink = JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/listview/default_thumbnail.gif';
      }
      
      $dispcat = stripslashes(JText::_($this->cats[$vid->cat]->name));
      
      if (empty ($dispcat)) $dispcat = JText::_('COM_VIDEOFLOW_CAT_NONE');
      
      // Determine rating
      if ($vid->rating > 0 && $vid->votes > 0) {
      $vid->rating = round($vid->rating / $vid->votes, 2).JText::_('COM_VIDEOFLOW_PER_FIVE'); 
      } else {
      $vid->rating = JText::_('COM_VIDEOFLOW_RATE_NONE');
      }
      
      //Determine lightbox popup height. Additionally controlled through css
      $vboxheight = $vparams->lplayerheight + $vparams->lboxh;
      $vboxwidth = $vparams->lplayerwidth + $vparams->lboxw;
      if ($vparams->ratings || (!empty($this->vfshare))) $vboxheight = $vboxheight + 30;
      if (!empty($this->vflow8)) $vboxheight = $vboxheight + 78;
      // Set thumbnail and title link format for "MultiBox" lightbox system
      if ($vparams->lightbox && ($vparams->lightboxsys=='multibox' || $vparams->lightboxsys == 'colorbox')){
      $thumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$vid->id).'" rel="width:'.$vboxwidth.',height:'.$vboxheight.'" id="vf_mbox'.$mbox.'" class="vf_mbox" title="'.stripslashes($vid->title).'">
                   <div class="vf_thumb"><img class="vf_thumb thumbnail" src="'.$vid->pixlink.'"/>'.$iconplay.'</div>
                   <div class="vflowBoxDesc vf_mbox'.$mbox.'"></div></a>';
      $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$vid->id.$xp).'" rel="width:'.$vboxwidth.',height:'.$vboxheight.'" id="vf_mboxx'.$mbox.'" class="vf_mboxx" title="'.stripslashes($vid->title).'">'.stripslashes($vid->title).'
                   <div class="vflowTboxDesc vf_mboxx'.$mbox.'"></div> </a>';
           if ($vid->type == 'jpg' || $vid->type == 'png' || $vid->type == 'gif') {
         //  $vid->medialink = $this->imgResize($vid, 'pix');
           $thumblink = '<a href="'.$vid->medialink.'" id="vf_mbox'.$mbox.'" class="vf_mbox" title="'.stripslashes($vid->title).'">
                   <div class="vf_thumb"><img class="vf_thumb thumbnail" src="'.$vid->pixlink.'"/>'.$iconplay.'</div>
                   <div class="vflowBoxDesc vf_mbox'.$mbox.'"></div></a>'; 
           $titlelink = '<a href="'.$vid->medialink.'" id="vf_mboxx'.$mboxx.'" class="vf_mboxx" title="'.stripslashes($vid->title).'">'.stripslashes($vid->title).'
                   <div class="vflowTboxDesc vf_mboxx'.$mboxx.'"></div></a>'; 
          }
          if (!$vparams->lightboxfull){
          $titlelink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$vid->id.$cid.$catid.$sl.$ls.$type.$flowid.$list.$lo.$varext).'" '.$target.'>'.stripslashes($vid->title).'</a>';
          }          
      } //End MultiBox link settings
      
      //Set thumbnail and title link formats for Joomla lightbox system
      elseif ($vparams->lightbox && ($vparams->lightboxsys == 'joomlabox')){
      $thumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$vid->id).'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: '.$vboxwidth.', y: '.$vboxheight.'}}">
                    <div class="vf_thumb"><img class="vf_thumb thumbnail" src="'.$vid->pixlink.'"/>'.$iconplay.'</a></div>';      
      $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$vid->id).'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: '.$vboxwidth.', y: '.$vboxheight.'}}">'.stripslashes($vid->title).'</a>';
           
           if ($vid->type == 'jpg' || $vid->type == 'png' || $vid->type == 'gif') {
              $vid->medialink = $this->imgResize($vid, 'pix');
              $thumblink = '<a href="'.$vid->medialink.'" id="modal-vflow'.$mbox.'" class="modal-vflow">
                   <div class="vf_thumb"><img class="vf_thumb thumbnail" src="'.$vid->pixlink.'"/>'.$iconplay.'</a></div>';           
           $titlelink = '<a href="'.$vid->medialink.'" id="modal-vflow'.$mbox.'" class="modal-vflow">'.stripslashes($vid->title).'</a>';
          }
          if (!$vparams->lightboxfull){
        //  $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task='.$stask.'&id='.$vid->id.$sl.$ls.$flowid.$tmpl.'&layout=simple').'">'.stripslashes($vid->title).'</a>';
          $titlelink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$vid->id.$cid.$catid.$sl.$ls.$type.$flowid.$list.$lo.$varext).'" '.$target.'>'.stripslashes($vid->title).'</a>';
          }    
      } // End Joomla lightbox thumbnail links
      
      // Set default thumbnail and title link formats - no lightbox effect
      
      else {
      $thumblink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$vid->id.$cid.$catid.$sl.$ls.$type.$flowid.$list.$lo.$varext).'" class="vf_mbox" '.$target.'>
                    <div class="vf_thumb"><img class="vf_thumb thumbnail" src="'.$vid->pixlink.'"/>'.$iconplay.'</a></div>';      
      
      $titlelink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$vid->id.$cid.$catid.$sl.$ls.$type.$flowid.$list.$lo.$varext).'" '.$target.'>'.stripslashes($vid->title).'</a>';
      
     // $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task='.$stask.'&id='.$vid->id.$sl.$ls.$type.$flowid.$tmpl.'&layout=simple').'">'.stripslashes($vid->title).'</a>';
      } //End default thumbnail and title links
/*****************************************************************************/
// You may edit the parts below to suit your needs. The corresponding css file is css/simple.css
?>
    <!-- START ITEM BOX -->
		<div class="row-fluid vfitem-margin-b"> 
			<div class="span3 vfmobthumb" style="position:relative;">
					 <?php echo $thumblink; ?>
			</div>
			<div class="span7">
				<div class="vfpfix">
						<div>
							<h4 class="vftopmargin0">
								<?php echo $titlelink; ?>
							</h4> 
						</div>
					<div class="clearfix vfjustify">
						<?php echo nl2br($this->escape($vid->sdetails)); ?>						
					</div>
					<div class="row-fluid visible-phone muted">
					<?php 
					if ($showuser) {
						if ($context == 'fb') {
							$href = 'href="'.$vparams->canvasurl.'&task=visit&cid='.$vid->userid.'" target="_parent"'; 
						} else {
							$href = 'href="'.JRoute::_('index.php?option=com_videoflow&task=visit&cid='.$vid->userid.'&layout=simple'.$flowid).'"'; 
						}
					?>
					<div class="vfhalf pull-left">
						<?php echo JText::_('COM_VIDEOFLOW_TUSER'); ?>
						<a <?php echo $href; ?> >
						<?php echo stripslashes($vid->usrname); ?>
						</a>  
					</div>
					<?php 
					}
					if ($showviews) {
					?>
						<div class="vfhalf pull-left">
							<?php echo JText::_('COM_VIDEOFLOW_TVIEWS'); ?>
							<?php echo $vid->views; ?>
						</div>
					<?php
					}
					?>
					</div>
					<div class="row-fluid muted">
					<?php
					if ($showdate){
						$date = JFactory::getDate($vid->dateadded);
						if (!JVERS3) $vdate = $date->toFormat('d M Y'); else $vdate = $date->format('d M Y');
						?>
						<div class="vfhalf pull-left"> 
							<?php echo $vdate; ?>
						</div>
					<?php
					}
					if ($showadd && !empty($vparams->showpro)) {
                    $alist = JRequest::getWord('list');
                    if ($alist == 'favs' && $vtask != 'visit') $add = 'remove'; else $add = 'add';                                
                        if ($vparams->lightbox || !JVERS3) {
                           $nclass = 'modal-vflow';
                           $rels = 'rel="{handler:\'iframe\', size: {x:600, y:400}}"';
                               if ((JVERS3 && $vparams->lightboxsys == 'colorbox')) {
                                   $nclass = 'vmodal-vflow';
                                   $rels = '"data-width="600" data-height="400"';
                                } 
                        } else {
                            $nclass = 'ajax-vflow';
                            $rels = '';
                        }
						?>
						<div class="vfhalf pull-left">
							<img class="vf_tools_icons" src="<?php echo JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/tools/'.$vparams->toolcolour.'/'.$add.'.gif'; ?>" />
							<a href="<?php echo JRoute::_('index.php?option=com_videoflow&task='.$add.'&id='.$vid->id); ?>" class="<?php echo $nclass; ?>" <?php echo $rels; ?> >
							<?php echo JText::_('COM_VIDEOFLOW_MYLIST'); ?>
							</a>
						</div>
					<?php
					} 
					?>
					</div>
				</div>
			</div>
			<?php 
            if ($showuser || $showviews || $showplaylistcount || $showdownloads || $showrating || $showcat ){
            ?> 
				<div class="span2 hidden-phone muted">
					<?php
					if ($showuser) {
					?>
						<div class="clearfix">
							<font class="vflist6">
							<?php echo JText::_('COM_VIDEOFLOW_TUSER'); ?>
							</font>
							<a class="vflist6" <?php echo $href; ?> >
							<?php echo stripslashes($vid->usrname); ?>
							</a>
						</div>
					<?php
					}
					if ($showviews) {
					?>
						<div class="clearfix">
							<font class="vflist6">
							<?php echo JText::_('COM_VIDEOFLOW_TVIEWS'); ?>
							</font>
							<font class="vflist5">
							<?php echo $vid->views; ?>
							</font>
						</div>
					<?php
					}
					if ($showvotes) {
					?>
						<div class="clearfix">
							<font class="vflist6">
							<?php echo JText::_('COM_VIDEOFLOW_TVOTES'); ?>
							</font>
							<font class="vflist5">
							<?php echo $vid->votes; ?>
							</font>
						</div>
					<?php                
					}  
					if ($showplaylistcount && $vparams->showpro){
					?>
						<div class="clearfix">
							<font class="vflist6">
						    <?php echo JText::_('COM_VIDEOFLOW_TPLAYLISTS'); ?>
							</font>
							<font class="vflist5">
							<?php echo $vid->favoured; ?>
							</font>
						</div>
					<?php
					}
					if ($showdownloads && $vparams->showpro){
					?>
						<div class="clearfix">
							<font class="vflist6">
							<?php echo JText::_('COM_VIDEOFLOW_TDOWNLOADS'); ?>
							</font>
							<font class="vflist5">
							<?php echo $vid->downloads; ?>
							</font>
						</div>
					<?php
					}
					if ($showrating) {
					?>
						<div class="clearfix">
							<font class="vflist6">
							 <?php echo JText::_('COM_VIDEOFLOW_TRATING'); ?>
							</font>
							<font class="vflist5">
							 <?php echo $vid->rating; ?> 
							</font>
						</div>
					<?php
					}
					if ($showcat) {
						if ($context == 'fb') {
							$catlink = '<a href="'.$vparams->canvasurl.'&task=cats&cat='.$vid->cat.'&sl=categories" target="_parent">'.$dispcat.'</a>'; 
						} else {
						$catlink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=cats&cat='.$vid->cat.'&sl=categories&layout=simple'.$flowid).'">'.$dispcat.'</a>'; 
						}
						?>
						<div class="clearfix">	
						<?php echo $catlink; ?>
						</div>
					<?php
					}
				?>
				</div>
			<?php
			}
			?>
		</div>	        
         <!--END ITEM BOX-->
	<?php
	$mbox++;
	$mboxx++;
	}
}
?>



</div>
<?php
if (!empty($this->vflow2)){
?>
<div class="<?php echo $vfrightmod;?> visible-desktop">
<?php echo $this->vflow2; ?>
</div>
<?php
}
?>
</div>

<div class="row-fluid">
    <div id="vffooter" class="span12">
    <?php
    $this->printPagination();
    if (!empty($this->credit)) echo '<div class="vfcredit muted">'.$this->credit.'</div>';
    ?>
    </div>
  </div>
</div>

<?php
// Initialise MultiBox
if ($vparams->lightbox && empty($tabone) &&  $vparams->lightboxsys == 'multibox') {
    if ($context == 'fb') $offsety = -700; else $offsety = 0;
        ?>
<script type="text/javascript">
						
                        var vfmbox = {};
			window.addEvent('domready', function(){
				vfmbox = new MultiBox('vf_mbox', {descClassName: 'vflowBoxDesc', useOverlay: <?php echo $this->vlay; ?>, tabCount : <?php echo count($tabone); ?>, tabCountExtra : <?php echo $dcount; ?>, MbOffset: <?php echo $this->mboffset; ?>, offset: {x:0, y:<?php echo $offsety; ?>}, MbIndex: false});
			});
                        
                        var vfmboxx = {};
			window.addEvent('domready', function(){
				vfmboxx = new MultiBox('vf_mboxx', {descClassName: 'vflowTboxDesc', useOverlay: <?php echo $this->vlay; ?>, tabCount : <?php echo count($tabone); ?>, tabCountExtra : <?php echo $dcount; ?>, MbOffset: <?php echo $this->mboffset; ?>, offset: {x:0, y:<?php echo $offsety; ?>}, MbIndex: false});
			});
			
</script>
<?php
}
?>

<?php
if ($context == 'fb') $this->canvasFix();