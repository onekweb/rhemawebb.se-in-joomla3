<?php


//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.2 
* @ Copyright (C) 2008 - 2011 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );
global $vparams, $fxparams;
$doc = JFactory::getDocument();
$vfslide = "
     var vfshare_display = function(vf){
        if ($(vf).style.display == 'none'){
        $(vf).style.display = 'block';
        } else {
        $(vf).style.display = 'none';
        }      
     };
";
$doc->addScriptDeclaration($vfslide);
$context = JRequest::getCmd('c');
$varext = '';

if ($context == 'fb') {
$xparams = $fxparams;
$target = 'target="_parent"';
} else {
$xparams = $this->getXparams();
$target = '';
}
if ($context == 'fb') {
$showuser = $vparams->fbshowuser;
$showcat = $vparams->fbshowcategory;
$showdate = $vparams->fbshowdate;
$showviews = $vparams->fbshowviews;
$showrating = $vparams->fbshowrating;
$showplaylistcount = $vparams->fbshowplaylists;
$showlike = $vparams->likebutton;
$showadd = $vparams->fbshowmylist;
} else {
$showadd = (bool) $xparams->get('showadd', $vparams->showadd);
$showuser = (bool) $xparams->get('showuser', $vparams->showuser);
$showcat = (bool) $xparams->get('showcat', $vparams->showcat);
$showviews = (bool) $xparams->get('showviews', $vparams->showviews);
$showrating = (bool) $xparams->get('showrating', $vparams->showrating);
$showdate = (bool) $xparams->get('showdate', $vparams->showdate);
$showplaylistcount = (bool) $xparams->get('showplaylistcount', $vparams->showplaylistcount);
$showlike = $vparams->likebutton;
}
$showvotes = (bool) $xparams->get('showvotes', $vparams->showvotes);
$showdownloads = (bool) $xparams->get ('showdownloads', $vparams->showdownloads);
$likelayout = (string) $xparams->get('likelayout', 'standard');
$likecolour = (string) $xparams->get('likecolour', 'light');
$likefaces = (bool) $xparams->get('likefaces', true);
$iborderc = (string) $xparams->get('iborderc');
$bgactive = (string) $xparams->get('bgactive');
$bginactive = (string) $xparams->get('bginactive');
$iborders = (int) $xparams->get('iborders', 4);
$borders = (int) $xparams->get('borders', 1);
$playerwidth = (int) $xparams->get('playerwidth', $vparams->playerwidth);
$playerheight = (int) $xparams->get('playerwidth', $vparams->playerheight);
$sharelink = JRoute::_(JURI::root().'index.php?option=com_videoflow&task=play&id='.$this->media->id);
if (!empty($showlike)) {
    $locale = JRequest::getString('locale');
    if(empty($locale)) $locale = 'en_US';
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

$slist = (bool) $xparams->get('slist', 1);
$flowid = JRequest::getInt('Itemid');
if (!empty($flowid)) $flowid = '&Itemid='.$flowid; else $flowid = '';
$type = JRequest::getVar('type');
if (!empty($type)) $type = '&type='.$type; else $type = '';
$tmpl = JRequest::getCmd('tmpl');
if (!empty($tmpl)) $tmpl = '&tmpl='.$tmpl; else $tmpl = '';
$fb = JRequest::getCmd('c');
if ($fb == 'fb') $fb = '&c=fb'; else $fb = ''; 
$frm = JRequest::getBool('fb_sig_in_iframe');
if ($frm) $frm = '&fb_sig_in_iframe=1'; else $frm = '';
$lo = JRequest::getCmd('layout');
if (!empty($lo) && $context != 'fb') $lo = '&layout='.$lo; else $lo = '';
$ls = JRequest::getInt ('limitstart', null);
    if ($ls > 0) $ls = '&limitstart='.$ls;
$cid = JRequest::getInt('cid');
  if (!empty($cid)) $cid = '&cid='.$cid; else $cid = '';
$vtask = JRequest::getCmd('task', 'play');
if ($vtask == 'myvids') $vtask = 'play';
if ($vparams->lightboxfull) $xp = '&xp=1'; else $xp = '';
$list = JRequest::getWord('list');
if (!empty($list)) $list = '&list='.$list; else $list = '';
$catid = null;
if ($vtask == 'cats'){
$catid = JRequest::getInt('cat');
$catid = '&cat='.$catid;
}
if ($vtask == 'search') {
    $searchword = JRequest::getString('searchword');
    if (!empty($searchword)) $varext .= '&searchword='.$searchword;
}

if (!empty ($this->media)) {
    // Set media
    $media = $this->media;
    $altcontent = $this->setMedia();
    
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
  //Default thumbnail
  if (empty($media->pixlink)) $media->pixlink = JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/listview/default_thumbnail.gif';  

  /* 
* Bootstrap-based template settings. 
*/ 
  
//Player and Sidebar 
$vfplayerarea = 'span9'; //Sidebar = span3


//Player without sidebar
if (empty($this->tabone) || empty($slist)) {
$vfplayerarea = 'span12';
} 

?>

<!-- START ITEM BOX -->  
  <div class="row-fluid">
    <div class="<?php echo $vfplayerarea; ?>">    
      <div class="vfplayerwidth">
        <div id="vfmediatitle" class="well">
        <h3>
        <?php echo stripslashes ($media->title); ?>
        </h3>
        </div>
        <div class="clearfix well vfcentre">
        <div id="vfmediaspace">
        <?php echo $altcontent; ?>
        </div>
        </div>
        <div class="clearfix vfcentre">
        <?php
        if (!empty($showlike)){
          echo '<div class="mod_vfshare vfleft">'.$share.'</div>';
        }
        if (!empty($vparams->twitterbutton)){
          echo '<div class="mod_vfshare vfright">'.$twitter.'</div>';
        }
        if (!empty($this->addthis)) {
          echo '<div class="mod_vfshare vfnophone">'.$this->addthis.'</div>';    
        }
        if (!empty($this->rating)) echo '<div class="vf_rating">'.$this->rating.'</div>'; 
        ?>
        </div>
         <?php
        if (is_array($this->tools) && (!empty($this->tools))){ 
        ?>
         <div class="clearfix vfcentre well">
          <font class="vflist6">
            <?php
            foreach ($this->tools as $key=>$value){
              echo '<div class="btn btn-small" style="margin: 0px 2px;"><img class="vf_tools_icons img-rounded" src="'.
              JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/tools/'.
              $vparams->toolcolour.'/'.$key.'.gif" />'.$value.'</div>';
            }
            ?>  
          </font>
        </div>
        <?php
        }
        if (!empty($vparams->showshare)) { 
            echo '<div class="vfinfofx clearfix" style="border: none;">';
            echo '<div id="vfshare" style="display:none; text-align:center;">'.$this->vfshare.'</div>';
            echo '</div>';
        }
        ?>
      </div> 
      <div class="clearfix vfjustify">
      <?php echo $media->details; ?>
      </div>
      <div class="clearfix muted vfmargin10 vfw90">
        <?php
        if ($showuser){
        echo '<div class="vfinfolist">'.JText::_('COM_VIDEOFLOW_TUSER').' <a href="'.$this->doRoute('&task=visit&cid='.$media->userid.$lo).'" $target>'.$media->shortname.'</a></div>';
        }
        if ($showdate){
        echo '<div class="vfinfolist">'.JText::_('COM_VIDEOFLOW_TDATE').' '.date_format(date_create($media->dateadded), 'Y - m - d').'</div>';
        }
        if ($showcat){
        echo '<div class="vfinfolist">'.JText::_('COM_VIDEOFLOW_TCAT').'<a href="'.$vparams->canvasurl.'&task=cats&cat='.$media->cat.'&vf=1"> '.JText::_($media->catname).'</a></div>';
        }
        ?>
        <?php
        if ($showviews){
        echo '<div class="vfinfolist">'.JText::_('COM_VIDEOFLOW_TVIEWS').' '.$media->views.'</div>';
        }
        if ($vparams->showpro && $showplaylistcount){
        echo '<div class="vfinfolist">'. JText::_('COM_VIDEOFLOW_TPLAYLISTS').' '. $media->favoured.'</div>';
        }
        if ($showdownloads){
        echo '<div class="vfinfolist">'.JText::_('COM_VIDEOFLOW_TDOWNLOADS').' '.$media->downloads.'</div>';
        }
        ?>
        </div>
        <div>
      <?php echo $this->vflow5; ?>
      </div>
      <?php
      if (!empty($this->comments)) {
      ?>
      <div class="clearfix">  
      <?php echo $this->comments; ?>
      </div>
      <?php
      }
      ?>
      <div>
      <?php echo $this->vflow6; ?>
      </div>
  </div>
  <?php
  if ($slist && !empty($this->tabone)) {
  if ($vparams->iconplay) $iconplay = '<div class="vf-playiconwrap"><span class="vf-playicon"><i class="vf-icon-'.$vparams->playicon.'"></i></span></div>'; else $iconplay = '';  
  ?>
  <div class="span3 hidden-phone well">  
  <?php
  $mboxs = 1;
  $mbox = 1;
  $mboxxs = 1000;
  if (!empty($this->tabone)) $tabone = $this->tabone; else $tabone = array();
  if (!empty($this->vlist)) $dcount = count($this->vlist); else $dcount = 0;
  if (!empty($this->tabone)) {
    //Determine lightbox popup height. Additionally controlled through css
    $vboxheight = $vparams->lplayerheight + $vparams->lboxh;
    $vboxwidth = $vparams->lplayerwidth + $vparams->lboxw;
    if ($vparams->ratings || (!empty($this->vfshare))) $vboxheight = $vboxheight + 30;
    if (!empty($this->vflow8)) $vboxheight = $vboxheight + 78;
    $swidth = $vparams->thumbwidth + 14;
    echo '<div class="vfcentre">';
    echo '<button class="btn btn-block vrellabel">'.JText::_('COM_VIDEOFLOW_RELATED_MEDIA').'</button>';
    foreach ($this->tabone as $rmedia) {        
      if ($rmedia->type == 'jpg' || $rmedia->type == 'png' || $rmedia->type == 'gif') {
        if (empty($rmedia->pixlink) && !file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$rmedia->title.'.jpg')) {
            $rmedia->pixlink = $this->imgResize($rmedia, 'thumb');
        }
      }
     
     if (!empty($rmedia->pixlink)) {
       if (stripos($rmedia->pixlink, 'http://') === FALSE) {  
         $rpixlink = JURI::root().$vparams->mediadir.'/_thumbs/'.$rmedia->pixlink;
         } else {
         $rpixlink = $rmedia->pixlink;
         }
       } else if (empty($rmedia->pixlink) && file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$rmedia->title.'.jpg')){       
       $rpixlink = JURI::root().$vparams->mediadir.'/_thumbs/'.$rmedia->title.'.jpg';
       } else {
       $rpixlink = JURI::root().'components/com_videoflow/views/videoflow/tmpl/images/listview/default_thumbnail.gif';
      }

  
        // Set thumbnail and title link format for "MultiBox"/"Colorbox" lightbox system
      if ($vparams->lightbox && ($vparams->lightboxsys=='multibox' || $vparams->lightboxsys == 'colorbox')){
        if ($vparams->lightboxsys == 'colorbox') {
            $rel = "vlist1";
            $rel2 = "vlist2";
        } else {
            $rel = $rel2 = "width:$vboxwidth, height:$vboxheight";
        }
      $rthumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$rmedia->id).'" rel="'.$rel.'" id="vf_mbox'.$mbox.'" class="vfs_mbox" title="'.stripslashes($rmedia->title).'">
                   <div class="vf-sbarbox vfcentre"><img class="thumbnail vf-sbarthumb vfcentre" src="'.$rpixlink.'"/>'.$iconplay.'</div>
                   <div class="vflowBoxDesc vf_mboxs'.$mbox.'"></div></a>';
      $rtitlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$rmedia->id.$xp).'" rel="'.$rel2.'" id="vf_mboxx'.$mbox.'" class="vfs_mboxx" title="'.stripslashes($rmedia->title).'">'.stripslashes($rmedia->stitle).'
                   <div class="vflowTboxDesc vf_mboxxs'.$mbox.'"></div> </a>';
           if ($rmedia->type == 'jpg' || $rmedia->type == 'png' || $rmedia->type == 'gif') {
           $rmedia->medialink = $this->imgResize($rmedia, 'pix');
	   if (empty($rmedia->medialink)) $rmedia->medialink = JURI::root().$vparams->mediadir.'/photos/'.$rmedia->file;
           
	   $rthumblink = '<a href="'.$rmedia->medialink.'" id="vf_mboxs'.$mboxs.'" class="vfs_mbox" rel="'.$rel.'" title="'.stripslashes($rmedia->title).'">
                   <div class="vf-sbarbox vfcentre"><img class="thumbnail vf-sbarthumb vfcentre" src="'.$rpixlink.'"/>'.$iconplay.'</div>
                   <div class="vflowBoxDesc vf_mboxs'.$mboxs.'"></div></a>'; 
           $rtitlelink = '<a href="'.$rmedia->medialink.'" id="vf_mboxxs'.$mboxxs.'" class="vfs_mboxxs" rel="'.$rel2.'"title="'.stripslashes($rmedia->title).'">'.stripslashes($rmedia->stitle).'
                   <div class="vflowTboxDesc vf_mboxxs'.$mboxxs.'"></div></a>'; 
          }
          if (!$vparams->lightboxfull){
          $rtitlelink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$rmedia->id.$cid.$catid.$ls.$type.$flowid.$list.$lo.$varext).'" '.$target.'>'.stripslashes($rmedia->stitle).'</a>';
	  }          
      } //End MultiBox link settings
      
      //Set thumbnail and title link formats for Joomla lightbox system
      elseif ($vparams->lightbox && ($vparams->lightboxsys == 'joomlabox')){
      $rthumblink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$rmedia->id).'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: '.$vboxwidth.', y: '.$vboxheight.'}}">
                    <div class="vf-sbarbox vfcentre"><img class="thumbnail vf-sbarthumb vfcentre" src="'.$rpixlink.'"/>'.$iconplay.'</a></div>';
      $rtitlelink = '<a href="'.JRoute::_('index.php?option=com_videoflow&task=play&tmpl=component&layout=lightbox&id='.$rmedia->id).'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: '.$vboxwidth.', y: '.$vboxheight.'}}">'.stripslashes($rmedia->stitle).'</a>';
           if ($rmedia->type == 'jpg' || $rmedia->type == 'png' || $rmedia->type == 'gif') {
           $rmedia->medialink = $this->imgResize($rmedia, 'pix');
           if (empty($rmedia->medialink)) $rmedia->medialink = JURI::root().$vparams->mediadir.'/photos/'.$rmedia->file;
           $rthumblink = '<a href="'.$rmedia->medialink.'" id="modal-vflow'.$mbox.'" class="modal-vflow">
                   <div class="vf-sbarbox vfcentre"><img class="thumbnail vf-sbarthumb vfcentre" src="'.$rmedia->pixlink.'"/>'.$iconplay.'</a></div>'; 
          
           $rtitlelink = '<a href="'.$rmedia->medialink.'" id="modal-vflow'.$mbox.'" class="modal-vflow">'.stripslashes($rmedia->title).'</a>';
          }
          if (!$vparams->lightboxfull){
            $rtitlelink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$rmedia->id.$cid.$catid.$ls.$type.$flowid.$list.$lo.$varext).'" '.$target.'>'.stripslashes($rmedia->stitle).'</a>';
   	      }    
      } // End Joomla lightbox thumbnail links
      
      // Set default thumbnail and title link formats - no lightbox effect
      
      else {
      
       $rthumblink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$rmedia->id.$cid.$catid.$ls.$type.$flowid.$list.$lo.$varext).'" class="vfs_mbox" '.$target.'>
                    <div class="vf-sbarbox vfcentre"><img class="thumbnail vf-sbarthumb vfcentre" src="'.$rpixlink.'"/>'.$iconplay.'</a></div>';      
      
      $rtitlelink = '<a href="'.$this->doRoute('&task='.$vtask.'&id='.$rmedia->id.$cid.$catid.$ls.$type.$flowid.$list.$lo.$varext).'" '.$target.'>'.stripslashes($rmedia->stitle).'</a>';
    
    } //End default thumbnail and title links
/*****************************************************************************/
// You may edit the parts below to suit your needs. The corresponding css file is css/simple.css

    echo '<div>';
    echo '<div>'.$rthumblink.'</div>';
    echo '<div class="vf-sbartitle">'.$rtitlelink.'</div>';
    echo '</div>';
    $mboxs++;
    $mboxxs++;
    }
  echo '</div>';
  }
  ?>
  </div>
 <?php
 }
 ?>
 </div>
<?php    
// Initialise MultiBox/ColorBox
if ($vparams->lightbox) {
    if ($vparams->lightboxsys == 'multibox') {
    if ($context == 'fb') $offsety = -700; else $offsety = 0;
?>
<script type="text/javascript">
								
			var vfmboxs = {};
			window.addEvent('domready', function(){
				vfmboxs = new MultiBox('vfs_mboxs', {descClassName: 'vflowBoxDesc', useOverlay: <?php echo $this->vlay; ?>, multiCount: true, offset: {x:0, y:<?php echo $offsety; ?>}, MbOffset: <?php echo $this->mboffset; ?> });
			});
				
			var vfmboxxs = {};
			window.addEvent('domready', function(){
				vfmboxxs = new MultiBox('vfs_mboxxs', {descClassName: 'vflowTboxDesc', useOverlay: <?php echo $this->vlay; ?>, offset: {x:0, y:<?php echo $offsety; ?>}, MbOffset: <?php echo $this->mboffset; ?>});
			});
	
</script>
<?php
    } elseif ($vparams->lightboxsys == 'colorbox'){
    $prev = JText::_('COM_VIDEOFLOW_PREV');
    $next = JText::_('COM_VIDEOFLOW_NEXT');
    $close = JText::_('COM_VIDEOFLOW_CLOSE');
    $height = $vparams->lplayerheight + $vparams->lboxh;
    $width = $vparams->lplayerwidth + $vparams->lboxw;
    $vfprep = "";
    if ($vparams->iframecentre || $vparams->iframecss) {
      $vfprep .= "vfstylePlugin:true,";  
      if ($vparams->iframecentre) $vfprep .= "applyStyle:true,";
      if ($vparams->iframecss) $vfprep .= " loadCss:true,";
    }
    if ($vparams->ratings || (!empty($this->vfshare))) $height = $height + 30;
    $clrbox = "
      jQuery(document).ready(function() {
      jQuery('a.vfs_mbox').colorbox({rel:'vlist1', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});";
      if ($vparams->lightboxfull) {
      $clrbox .= "
      jQuery('a.vfs_mboxx').colorbox({rel: 'vlist2', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});";  
      }
      $clrbox .= "});";
    $doc->addScriptDeclaration ($clrbox);
    }
  }
}
?>
<div class="row-fluid">
  <div class="span12">
    <div class="vf_hsolid_line"></div>
  </div>
</div>