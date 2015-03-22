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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class videoflowHTMLEXT {
  
  function printMenu(){
  global $vparams;
  $tmpl = JRequest::getCmd('tmpl');
  $context = JRequest::getCmd('c');
  if ($tmpl == 'component' && $context != 'fb') return;
  if ($vparams->responsive) {
  $activemenu = JRequest::getVar('activemenu');
  ?>
        <div class="navbar"> 
          <div class="navbar-inner">
            <div class="container-fluid"> 
                <?php
                if (!empty($activemenu))
                {
                ?>
                <a class="brand hidden-desktop"><?php echo $activemenu; ?></a>
                <?php
                }
                ?>    
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </a>
                <div class="nav-collapse collapse" role="navigation">
				 <?php
                    echo '<ul class="nav">'; 
                    foreach ($this->menu as $menu){
                    echo $menu;
                    }  
                    echo '</ul>';                   
				?>
              </div>
			</div>
		  </div>
		</div>
	<?php
	} else {
	echo '<div class="vfnav-main"><ul class="vfmenulist vfround">';
    foreach ($this->menu as $menu){
    echo $menu;
    }
    echo '</ul></div>';
	}
  }
  
  function printPagination($p){
	 $id = JRequest::getInt('id');	 
	 if (empty($p->pages)) $pages = $p->getPagesLinks(); else $pages = $p->pages;
	 if (!empty($id)) {
	 $pat = '/(\/'.$id.'-[A-Za-z0-9\-]+)[\?|\/\s]/i'; 
	 preg_match($pat, $pages, $res);
	 if (!empty($res[1])) $pages = str_replace($res[1], '', $pages); else $pages = str_replace('/'.$id.'-', '/', $pages);
	 }
	 echo str_replace (array ('&amp;id=', 'task=play', '/play/', '/play?'), array ('&amp;v=', 'task=latest', '/', '?'), $pages);
     echo '&nbsp;&nbsp;';
     echo $p->getPagesCounter(); 
  }


  function imgResize ($vid, $type)
  {
  global $vparams;
  jimport('joomla.filesystem.file');
  jimport('joomla.filesystem.folder');
  if ($type == 'thumb') {
    $folder = '_resizedthumbs';
  } elseif ($type == 'pix') {
    $folder = '_resizedphotos';
  }
  $folderpath = JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$folder;
  if (!JFolder::exists($folderpath)) JFolder::create($folderpath);
  $newimgname = JFile::stripExt($vid->file).'.jpg';
  $newimg = $folderpath.DS.$newimgname;
  if (file_exists($newimg)) return JURI::root().$vparams->mediadir.'/_thumbs/'.$folder.'/'.$newimgname;  
  include_once(JPATH_SITE.DS.'components'.DS.'com_videoflow'.DS.'helpers'.DS.'resize-class.php');
  if (stripos($vid->file, 'http://') === FALSE) {  
    $ifile = JPATH_ROOT.DS.$vparams->mediadir.DS.'photos'.DS.$vid->file;
  } else {
    $ifile = $vid->file;
  }
  list($width,$height) = getimagesize($ifile);
  switch ($type) {
    case 'thumb':
      if ($vparams->thumbwidth == $width && ($vparams->thumbheight == $height)) {
        return JURI::root().$vparams->mediadir.'/photos/'.$vid->file;
      } else {
      $xform = 'crop';
      $xw = $vparams->thumbwidth;
      $xh = $vparams->thumbheight;
      }
      break;
    
    case 'pix':
    default:
      if ($vparams->lplayerwidth < $width) {
        $xform = 'landscape';
      } else {
        return JURI::root().$vparams->mediadir.'/photos/'.$vid->file;
      }
      $xw = $vparams->lplayerwidth;
      $xh = $vparams->lplayerheight;
      break;
  }

  $pix = new resize($ifile);
  $pix->resizeImage($xw, $xh, $xform);
  $pix->saveImage($newimg, 100);
  return JURI::root().$vparams->mediadir.'/_thumbs/'.$folder.'/'.$newimgname;
  }
  
  function setPlayer($media){
  global $vparams;
  $doc = JFactory::getDocument();
  $altcontent = '';  
  if (stripos ($media->embedcode, 'swfobject.embedSWF') !== FALSE){
    //Load swfobject javascript file
    $swfobject = JURI::root().'components/com_videoflow/jscript/swfobject.js';
    $doc->addScript($swfobject); 
    //Load the player using swfobject
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){".$media->embedcode." })");
    $altcontent = '';
    } elseif (stripos ($media->embedcode, "jwplayer('vfmediaspace').setup") !== FALSE){
    //If using JW custom JS   
		$fixjw = 'object{
              position: absolute;
              top: 0px;
              left: 0px;
              }';
    $doc->addStyleDeclaration($fixjw);   
    if (!empty($vparams->jwplayerurl)) {
		//remotely hosted
    $jwjs = $vparams->jwplayerurl;
		} else {
    //locally hosted
		$jwjs = JURI::root().'components/com_videoflow/players/jwplayer/jwplayer.js';
		}  
    $doc->addScript($jwjs);
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){".$media->embedcode." })");
    $altcontent = '';           
    } elseif (stripos ($media->embedcode, "YT.Player") !== FALSE) {
    $yt = JURI::root().'components/com_videoflow/jscript/youtube.js';
    $doc->addScript($yt);
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){".$media->embedcode." })");
    $altcontent = '';  
    } elseif ($vparams->player == 'ME') {
	   $altcontent = $media->embedcode;
	   if ($vparams->jsframework == 'mootools' || version_compare(JVERSION, '3.0', 'lt')) {
		 $doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	}
	$doc->addScript(JURI::root().'components/com_videoflow/players/me/mediaelement-and-player.min.js');
	$doc->addStyleSheet(JURI::root().'components/com_videoflow/players/me/mediaelementplayer.min.css', 'text/css', null, array() );
	$doc->addStyleSheet(JURI::root().'components/com_videoflow/players/me/mejs-skins.css', 'text/css', null, array() );       
	} elseif($vparams->player == 'videojs'){
  $altcontent = $media->embedcode;
	$doc->addScript(JURI::root().'components/com_videoflow/players/videojs/video.js');  
	$doc->addStyleSheet(JURI::root().'components/com_videoflow/players/videojs/video-js.min.css', 'text/css', null, array() ); 
  $vcontrols = '.video-js {padding-top:56.25%;}
              .vjs-fullscreen {padding-top: 0px;}
              ';
  
  if (!ISMOBILE && ($media->type == "mp3" || $media->type == "ogg" || $media->type == 'wav')){
  $vcontrols .= '
  .vjs-control-bar{display:block !important;}
  .vjs-big-play-button{display:none !important;}
  ';
  }
  $doc->addStyleDeclaration($vcontrols);     
  if ($media->type == 'yt') {
  $doc->addScript(JURI::root().'components/com_videoflow/players/videojs/vjs.youtube.js');
  }    
  
  } elseif ($vparams->player == 'projekktor') {
   if ($vparams->jsframework == 'mootools' || !JVERS3) {
		$doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	 }   
  $doc->addScript(JURI::root().'components/com_videoflow/players/projekktor/projekktor-1.3.09.min.js');
  $doc->addScript(JURI::root().'components/com_videoflow/players/projekktor/plugins/logo/projekktor.logo.min.js');
  $doc->addStyleSheet(JURI::root().'components/com_videoflow/players/projekktor/themes/maccaco/projekktor.style.css');
  $doc->addStyleSheet(JURI::root().'components/com_videoflow/players/projekktor/plugins/logo/projekktor.logo.css');
  $projekktor = "jQuery(document).ready(function() {
        projekktor('#vf_fidsPlayer', {
        playerFlashMP4: '".JURI::root().'/components/com_videoflow/players/projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf'."',
        playerFlashMP3: '".JURI::root().'/components/com_videoflow/players/projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf'."',
        addplugins: ['logo'],
        plugin_logo: {
        image: '".$vparams->logo."',
        delay: 10
        }
        });
        });";
    $doc->addScriptDeclaration($projekktor);
    $altcontent = $media->embedcode;
  } else {
    //If using the standard embed method, replace alternative content with the player
     $altcontent = $media->embedcode;
    }
   
   $srcss = '.vfplayerwidth{
            width:100%;
            margin:auto;                                    
            }
            ';
               
    if (!empty($vparams->maxplayerwidth)) {
    $srcss .= '.vfplayerwidth{
                max-width:'.$vparams->maxplayerwidth.'px;
                }';
    }                
    
    $doc->addStyleDeclaration($srcss);

   return $altcontent;
  } 
  
  
  function loadCstyle()
   {
    global $vparams;
	  $tmpl = JRequest::getCmd ('tmpl');
    $doc = JFactory::getDocument();
    $cssshared = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/videoflow.css';
    $doc->addStyleSheet( $cssshared, 'text/css', null, array() );
	  if ($vparams->iconplay) { 
	  $doc->addStyleSheet(JURI::root().'components/com_videoflow/views/videoflow/tmpl/icons/css/fontello.css');
		jimport('joomla.environment.browser');
		$jbrowser = JBrowser::getInstance();
		$browser = $jbrowser->getBrowser();
		if ($browser == 'msie') {
			$msie = $jbrowser->getMajor();
			if ($msie < 8) {
			$doc->addStyleSheet(JURI::root().'components/com_videoflow/views/videoflow/tmpl/icons/css/fontello-ie7.css');
			}
		}
	  }
      if ($tmpl == 'component') {
      $css = JURI::root().'templates/system/css/system.css';
      $doc->addStyleSheet( $css, 'text/css', null, array() );
      }
   }
  
}