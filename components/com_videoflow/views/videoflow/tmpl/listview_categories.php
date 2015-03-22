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

global $vparams, $fxparams;
$context = JRequest::getCmd('c');
$xparams = $this->getXparams();
$ajaxmode = (int) $xparams->get('ajaxmode', 1);
$tmplname = JRequest::getCmd('layout');
if (empty($tmplname)) $tmplname = (string) $xparams->get('tmplname', $vparams->jtemplate);  
$showviews = (bool) $xparams->get('showviews', 1);
$showdate = (bool) $xparams->get('showdate', 1);
$vtask = JRequest::getCmd('task');
if ($vtask == 'mysubs') {
$this->data = $this->tabone;
}

//Load template stylesheet
if ($context == 'fb') {
$cssfile = $tmplname.'_fb';
$target = 'target="_parent"';
} else {
$cssfile = $tmplname;
$target = null;
}
if (file_exists(JPATH_COMPONENT.'/views/videoflow/tmpl/css/'.$cssfile.'.css')) {
$css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/'.$cssfile.'.css';
} else {
$css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/listview.css';
}
$doc = JFactory::getDocument();
$doc->addStyleSheet( $css, 'text/css', null, array() );
$fbuserdata = JRequest::getVar('fbuserdata');
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

/**************************************************************************/
/* 
* You may edit some parts below
*/ 

/* 
* VideoFlow basic layout based on Bootstrap 2.x. Each unit must add up to 12 columns (span12), which is "width:100%" in css terms. 
* Refer to Bootstrap 2.x documentation for details.  
*/ 

//Content area and right side module 
$vfmaincontent = 'span10'; //Main content area
$vfrightmod = 'span2'; // Right hand module

// Make content area full width if right side module is not published
if (ISMOBILE || empty($this->vflow2)) {
$vfmaincontent = 'span12'; $vfrightmod = '';
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
			<nav class="navbar-collapse collapse" role="navigation">
			<?php
            if (!empty($this->cname)) {
            ?>
				<div class="">
					<?php echo $this->cname; ?>
				</div>
				<div class="">
					<?php if ($this->cpix) echo $this->cpix; ?>  
				</div>
            <?php
            }
            if (!empty($this->menu2)){
            ?>
				<div class="">
					<?php echo $this->menu2;?>
				</div>
            <?php
            }
            ?>
        </nav>                 
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
		?>
	
    <!-- End banner section -->
<!-- End VideoFlow header and navigation-->
					               
<?php
/*************** IT IS NOT NECESSARY TO CHANGE THIS PART **************** /
/**************** BE CAREFUL IF YOU CHOOSE TO MODIFY IT ******************/

if (!empty($this->data) && is_array ($this->data)){
  foreach ($this->data as $data){
      if (empty($data->pixlink)) $data->pixlink = JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/playerview/default_thumbnail.gif';
      if ($vtask == 'mysubs'){
      $data->desc = '';
      if ($showviews) {
      $data->desc .= '<div class="vfinfolist muted">'.JText::_('COM_VIDEOFLOW_TVISITORS').' '.$data->visitors.'</div>';
      $data->desc .= '<div class="vfinfolist muted">'.JText::_('COM_VIDEOFLOW_TSUBSCRIBERS').' '.$data->subscribers.'</div>';
      }
      if ($showdate) $data->desc .= '<div class="vfinfolist muted hidden-phone">'.JText::_('COM_VIDEOFLOW_TJOIN_DATE').' '.$data->join_date.'</div>';
      $thumblink = '<a href="'.$this->doRoute ('&task=visit&cid='.$data->joomla_id.'&layout='.$tmplname).'" '.$target.'>
                    <img class="vf_thumb thumbnail" src="'.$data->pixlink.'"/></a>';      
      $titlelink = '<a href="'.$this->doRoute ('&task=visit&cid='.$data->joomla_id.'&layout='.$tmplname).'" '.$target.'>'.$this->escape($data->title).'</a>';
      } else {
      $thumblink = '<a href="'.$this->doRoute ('&task=cats&cat='.$data->id.'&sl=categories&layout='.$tmplname).'" '.$target.'>
                    <img class="vf_thumb thumbnail" src="'.$data->pixlink.'"/></a>';      
      $titlelink = '<a href="'.$this->doRoute ('&task=cats&cat='.$data->id.'&sl=categories&layout='.$tmplname).'" '.$target.'>'.$this->escape($data->name).'</a>';
      }

/*****************************************************************************/

// You may edit the parts below to suit your needs. The corresponding css file is css/simple.css

?>
    <!-- START ITEM BOX -->
					<div class="row-fluid vfitem-margin-b"> 
						<div class="span3 vfmobthumb">
            <div class="">
						 <?php echo $thumblink; ?>
             </div>
						</div>
						<div class="span9">
            <div class="vfpfix">
              <div class="">
              <h4 class="vftopmargin0">
								<?php echo $titlelink; ?>
              </h4> 
							</div>
							<div class="clearfix vfjustify">
                <?php echo nl2br($this->escape($data->desc)); ?>						
              </div>
              
							</div>
						</div>
					</div>	        
         <!--END ITEM BOX-->
	<?php
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
