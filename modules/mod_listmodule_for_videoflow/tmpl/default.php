<?php

// List Module for VideoFlow //
/**
* @ Version 1.2.0 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow List Module is free software
* @ Requires VideoFlow Multimedia Component 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/	

defined('_JEXEC') or die('Access denied.');
JHTML::_('behavior.modal', 'a.modal-vflow');
$doc = JFactory::getDocument();
if (!empty($thumbheight)) {
$ithumbheight =  'height:'.$thumbheight.'px; overflow: hidden;'; 
$mthumbheight =  'max-height:'.$thumbheight.'px; overflow: hidden;';
} else {
$ithumbheight = '';
}
$css = 	'.vf-ithumbheight{'.$ithumbheight.' position:relative; }
		li.thumbfix.span12 + li { margin-left : 0px; }
		li.thumbfix.span6:nth-child(2n + 3) { margin-left : 0px; }
		li.thumbfix.span4:nth-child(3n + 4) { margin-left : 0px; }
		li.thumbfix.span3:nth-child(4n + 5) { margin-left : 0px; }
		li.thumbfix.span2:nth-child(6n + 7) { margin-left : 0px; }
		li.thumbfix.span1:nth-child(12n + 13) { margin-left : 0px; }
    @media (max-width: 479px){ 
      .vf-ithumbheight{'.$mthumbheight.' height: auto; position:relative;}
    }
    ';
$doc->addStyleDeclaration($css);
if ($lightboxsys == 'multibox' && $listtype != 'categories') {
   if($modloc == 'facebook') $mcss = '_fb'; else $mcss = '';
$css2 = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/multibox'.$mcss.'.css';
$doc->addStyleSheet( $css2, 'text/css', null, array() );
  if (!empty($moo)) {
  $moofile=JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/mootools.js';
  $doc->addScript( $moofile );
  }
if ($vparams->mootools12) $mfile = '_legacy'; else $mfile = '';
$overlay = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/overlay'.$mfile.'.js';
$doc->addScript( $overlay );
$multibox = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/multibox'.$mfile.'.js';
$doc->addScript( $multibox );
}
if (!empty($jeffects) && !empty($jeffectsclass) && $modloc == 'joomla') $doc->addScript($jeffects);
$mstyle = 'margin:auto; text-decoration:none;';
if (!empty($bgroundc)) $mstyle .= ' background-color:'.$bgroundc.';';
if (!empty($borderc)) $mstyle .= ' border-color:'.$borderc.';';
if (!empty($borders) || $borders === "0") $mstyle .= ' border-width:'.$borders.'px; border-style:solid;';
if (!empty($texts)) $mstyle .= ' font-size:'.$texts.'px;';

    
$lstyle = 'padding:4px;';
if (!empty($lbgroundc)) $lstyle .= ' background-color:'.$lbgroundc.';';
if (!empty($lborderc)) $lstyle .= ' border-color:'.$lborderc.';';
if (!empty ($lborders)) $lstyle .= ' border-width:'.$lborders.'px; border-style:solid;';
if (!empty($ltextc)) $lstyle .= ' color:'.$ltextc.';';
if (!empty($ltexts)) $lstyle .= ' font-size:'.$ltexts.'px;';
$lstyle .= ' font-weight:bold;';

if (!empty($flowid)) {
$vfid = '&Itemid='.$flowid;
} else {
$vfid = '';
}

$mboxrand = mt_rand(10000, 50000);
$mboxxrand = mt_rand(60000, 99999);
if ($lightboxsys == 'colorbox') $mboxrand = $mboxxrand = '';
$iboxsize = $ithumbheight = '';
if (!empty($boxheight)) $iboxsize .= 'height:'.$boxheight.'px; overflow: hidden;';
if (!empty ($boxmaxwidth)) $iboxsize .= 'max-width:'.$boxmaxwidth.'px;'; 
 if (is_array($data)){  
	$n = count ($data);
    $columni=0;
    $mbox=1;
    $mboxx = 1000; 
    if ($vparams->iconplay) $iconplay = '<div class="vf-playiconwrap"><span class="vf-playicon"><i class="vf-icon-'.$vparams->playicon.'"></i></span></div>'; else $iconplay = '';  
    echo '<div class="container-fluid well">';
    echo '<div class="row-fluid">';
	if ($internaltitle) {
    echo '<div class="span12"><h3 class="page-header '.$ltexta.'">'.$label.'</h3></div>';
	}
    echo '<ul class="thumbnails">';
  	foreach ($data as $media) {
			$vid = $media->id;
			$vhit = $media->views;
			$vtitle = stripslashes($media->title);
			$vdesc = stripslashes ($media->details);
			$vpix = $media->pixlink;
			if (!empty($vpix)) {
         if (stristr($vpix, 'http') === FALSE) {  
         $vpix = JURI::root().$vparams->mediadir.'/_thumbs/'.$vpix;
         }
       } else if (file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$media->title.'.jpg')) {
       $vpix = JURI::root().$vparams->mediadir.'/_thumbs/'.$media->title.'.jpg';
       } else {
       if ($media->type == 'jpg' || $media->type == 'png' || $media->type == 'gif') {
         $mv = new ModVideoflowList;
         $vpix = $mv->imgResize($media, 'thumb');
         }
      }
      if (empty($vpix)) $vpix = JURI::root().'components/com_videoflow/players/vflow.jpg';
			$catid=$media->catname;
			$addeddate=$media->dateadded;
			$addeddate = JHTML::_('date', $addeddate, '%d-%m-%Y');
			if (strlen($vtitle)>$titlelength) {
					$vshorttitle=substr($vtitle,0,$titlelength)."...";
			} else {
					$vshorttitle=$vtitle;
			}
			if (strlen($vdesc)>$desclength) {
					$vdesc=substr($vdesc,0,$desclength)."...";
			}
			
      if ($lightboxsys == 'multibox' || $lightboxsys == 'colorbox'){
      $thumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$media->id).'" rel="width:'.$vboxwidth.',height:'.$vboxheight.'" id="vf_xmbox'.$mbox.'" class="vfmod_xmbox'.$mboxrand.'" title="'.stripslashes($vtitle).'">
                   <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div> 
                   <div class="vflowBoxDesc vf_xmbox'.$mbox.'"></div> </a>';
      $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$media->id).'" rel="width:'.$vboxwidth.',height:'.$vboxheight.'" id="vf_xmboxx'.$mboxx.'" class="vfmod_xmboxx'.$mboxxrand.'" title="'.stripslashes($vtitle).'">'.stripslashes($vshorttitle).'
                   <div class="vflowTboxDesc vf_xmboxx'.$mboxx.'"></div> </a>';
           if ($media->type == 'jpg' || $media->type == 'png' || $media->type == 'gif') {
           if (empty($media->medialink)) $media->medialink = JURI::root().$vparams->mediadir.'/photos/'.$media->file;
           $thumblink = '<a href="'.$media->medialink.'" id="vf_xmbox'.$mbox.'" class="vfmod_xmbox" title="'.stripslashes($vtitle).'">
                   <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div>
                   <div class="vflowBoxDesc vf_xmbox'.$mbox.'"></div> </a>'; 
           $titlelink = '<a href="'.$media->medialink.'" id="vf_xmboxx'.$mboxx.'" class="vfmod_xmboxx" title="'.stripslashes($vtitle).'">'.stripslashes($vshorttitle).'
                   <div class="vflowTboxDesc vf_xmboxx'.$mboxx.'"></div></a>'; 
          }
          if ($lightboxmode == 0){
          $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&id='.$media->id.$vfid).'">'.stripslashes($vshorttitle).'</a>';
          }          
      } elseif ($lightboxsys == 'joomla'){
      $thumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$media->id).'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: '.$vboxwidth.', y: '.$vboxheight.'}}">
                    <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div></a>';
      $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$media->id).'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: '.$vboxwidth.', y: '.$vboxheight.'}}">'.stripslashes($vshorttitle).'</a>';
           if ($media->type == 'jpg' || $media->type == 'png' || $media->type == 'gif') {
           if (empty($media->medialink)) $media->medialink = JURI::root().$vparams->mediadir.'/photos/'.$media->file;
           $thumblink = '<a href="'.$media->medialink.'" id="modal-vflow'.$mbox.'" class="modal-vflow">
                   <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div></a>'; 
          
           $titlelink = '<a href="'.$media->medialink.'" id="modal-vflow'.$mbox.'" class="modal-vflow">'.stripslashes($vshorttitle).'</a>';
          }
          if ($lightboxmode == 0){
          $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&id='.$media->id.$vfid).'">'.stripslashes($vshorttitle).'</a>';
          }    
      } else {
      $thumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&id='.$media->id.$vfid).'">
                    <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div></a>';      
      $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&id='.$media->id.$vfid).'">'.stripslashes($vshorttitle).'</a>';      
      }
      
     if ($modloc == 'facebook') {
      $thumblink = '<a href="'.$vparams->canvasurl.'&task=play&id='.$media->id.'&fb=1'.'" target="_top">
                    <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div></a>';      
      $titlelink = '<a href="'.$vparams->canvasurl.'&task=play&id='.$media->id.'&fb=1'.'" target="_top">'.stripslashes($vshorttitle).'</a>';      
     }
     
     if ($listtype == 'categories') {
      $thumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=cats&cat='.$media->id.$vfid.'&sl=categories').'">
                    <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div></a>';      
      $titlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=cats&cat='.$media->id.$vfid.'&sl=categories').'">'.stripslashes($vshorttitle).'</a>';      
          if ($modloc == 'facebook') {
          $thumblink = '<a href="'.$vparams->canvasurl.'&task=cats&cat='.$media->id.'&vf=1'.'" target="_top">
                    <div class="vf-ithumbheight"><img width="100%" src="'.$vpix.'"/>'.$iconplay.'</div></a>';      
          $titlelink = '<a href="'.$vparams->canvasurl.'&task=cats&cat='.$media->id.'&vf=1'.'" target="_top">'.stripslashes($vshorttitle).'</a>';      
          }
     }

				echo '<li class="thumbfix span'.$bcolumns.'">';
				echo '<div class="thumbnail" style="'.$iboxsize.'">';
				if ($titlepos == "top"){
					echo '<div class="caption">';
					echo '<'.$titlelevel.'>'.$titlelink.'</'.$titlelevel.'>';
					echo '</div>';
				}
				 echo '<div style = "position:relative">'.$thumblink.'</div>';					
				if ($titlepos == "bottom"){
					echo '<div class="caption">';
					echo '<'.$titlelevel.'>'.$titlelink.'</'.$titlelevel.'>';
					if ($showdesc) {
						echo '<p>'.$vdesc.'</p>';
					}
					echo '</div>';
				}
				if ($showdesc && ($titlepos == "top" || $titlepos == "notitle")) {
				echo '<div class="caption">';
				echo '<p>'.$vdesc.'</p>';
				echo '</div>';
				}
				echo '</div>';
				echo '</li>';	
		  $mbox++;
		  $mboxx;
    }
	echo "</ul>";	
  if (!empty($seemore) && $modloc == 'joomla' && $n >= $listlimit) {
    $stask = $listtype;
    if ($listtype == 'weeklyview') $stask = 'popular';
    echo '<div class="'.$stexta.'"><a href="'.JRoute::_('index.php?option=com_videoflow&task='.$stask.$vfid).'">'.JText::_('VF_SEE_MORE').'</a></div>';
    } 
 echo "</div>";
 echo "</div>"; 
}	

if ($lightboxsys == 'multibox' && $modloc == 'joomla' && $listtype != 'categories') {
$vxbox = 'vfmod_xmbox'.$mboxrand;
$vxboxx = 'vfmod_xmbox'.$mboxxrand;
?>
<script type="text/javascript">
						
			var vfmbox = {};
			window.addEvent('domready', function(){
				vfmbox = new MultiBox('<?php echo $vxbox; ?>', {descClassName: 'vflowBoxDesc', useOverlay: true});
			});
			
			
			var vfmboxx = {};
			window.addEvent('domready', function(){
				vfmboxx = new MultiBox('<?php echo $vxboxx; ?>', {descClassName: 'vflowTboxDesc', useOverlay: true});
			});
	
		</script>
<?php
}