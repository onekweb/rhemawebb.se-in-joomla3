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
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
  
if(class_exists('JViewLegacy')) {
    class ViewCat extends JViewLegacy {}
} else {
    class ViewCat extends JView {}
}
class VideoflowViewCategory extends ViewCat
{

  function __construct()
    {
      parent::__construct();
               
      global $vparams;  
      $this->loadVflowModules();
      $tmpl = JRequest::getVar('tmpl');
      $this->loadCstyle();
      if ($vparams->facebook) $this->checkPerms();
      if ($vparams->showcredit) $this->createCredit();
    }

   function display($jtmpl = null)
   {
      $this->addTemplatePath(JPATH_ROOT.DS.'components'.DS.'com_videoflow'.DS.'views'.DS.'videoflow'.DS.'tmpl');
      $model = $this->getModel();
      $cats = $model ->getCatList(); 
      $xparams = $this->getXparams();
      $shorttitle = (int) $xparams->get('shorttitle');
      $shortdetails = (int) $xparams->get('shortdetails');
      if (!empty($shorttitle) || !empty($shortdetails)) {
        if (!empty($shorttitle)) $fields['name'] = $shorttitle;
        if (!empty($shortdetails)) $fields['desc'] = $shortdetails; 
        if (!empty($cats)) $cats = $model->xterWrap($cats, $fields);
      }      
      $menu = $model ->getMenu();
      $pagination = $model-> getPagination ();
      $this->assignRef ('data', $cats);
      $this->assignRef ('menu', $menu);
      $this->assignRef ('pagination', $pagination);
      $this->sendHeadersAlt();
      parent::display();
   }
      
   function sendHeadersAlt()
   {
    $vtask = JRequest::getCmd('task');
    switch ($vtask)
    {
      case 'latest':
      $htitle = 'COM_VIDEOFLOW_LATEST_MEDIA';
      $hdesc = 'COM_VIDEOFLOW_LATEST_MEDIA_DESC';
      $hkeys = 'COM_VIDEOFLOW_LATEST_MEDIA_KEYS';
      break;
      
      case 'featured':
      $htitle = 'COM_VIDEOFLOW_FEATURED_MEDIA';
      $hdesc = 'COM_VIDEOFLOW_FEATURED_MEDIA_DESC';
      $hkeys = 'COM_VIDEOFLOW_FEATURED_MEDIA_KEYS';
      break;
      
      case 'popular':
      $htitle = 'COM_VIDEOFLOW_POPULAR_MEDIA';
      $hdesc = 'COM_VIDEOFLOW_POPULAR_MEDIA_DESC';
      $hkeys = 'COM_VIDEOFLOW_POPULAR_MEDIA_KEYS';
      break;
      
      case 'hirated':
      $htitle = 'COM_VIDEOFLOW_HIRATED_MEDIA';
      $hdesc = 'COM_VIDEOFLOW_HIRATED_MEDIA_DESC';
      $hkeys = 'COM_VIDEOFLOW_HIRATED_MEDIA_KEYS';
      break;
      
      case 'myvids':
      $htitle = 'COM_VIDEOFLOW_MY_MEDIA_CHANNEL';
      $hdesc = 'COM_VIDEOFLOW_MY_MEDIA_CHANNEL_DESC';
      $hkeys = 'COM_VIDEOFLOW_MY_MEDIA_CHANNEL_KEYS';
      break;
      
      case 'search':
      case 'dosearch':
      $htitle = 'COM_VIDEOFLOW_SEARCH_MEDIA';
      $hdesc = 'COM_VIDEOFLOW_SEARCH_MEDIA_DESC';
      $hkeys = 'COM_VIDEOFLOW_SEARCH_MEDIA_KEYS';
      break;
      
      case 'categories':
      $htitle = $hdesc = 'COM_VIDEOFLOW_MEDIA_CATS';
      $hdesc = 'COM_VIDEOFLOW_MEDIA_CATS';
      $hkeys = 'COM_VIDEOFLOW_MEDIA_CATS_KEYS';
      break;
      
      default:
      $htitle = 'COM_VIDEOFLOW_MULTIMEDIA';
      $hdesc = 'COM_VIDEOFLOW_MULTIMEDIA_DESC';
      $hkeys = 'COM_VIDEOFLOW_MULTIMEDIA_KEYS';
      break;
    }
      
        $doc = JFactory::getDocument();
        $doc->setGenerator('VideoFlow Multimedia System V.1.1.4h');
        $doc->setTitle(JText::_($htitle));
        $doc->setDescription (JText::_($hdesc));
        $doc->setMetaData('keywords', JText::_($hkeys));
   }
 
   function getThumb($media)
   {
     global $vparams;
     if (file_exists(JPATH_ROOT.DS.$vparams->mediadir.DS.'_thumbs'.DS.$media->title.'.jpg')){
       $thumb = JURI::root().$vparams->mediadir.'/_thumbs/'.$media->title.'.jpg';
      } else if ($media->type == 'jpg' || $media->type == 'png' || $media->type == 'gif') {
        $thumb = $media->file;
        if (stripos($thumb, 'http') === FALSE) {
        $thumb = JURI::root().$vparams->mediadir.'/photos/'.$thumb;
        }
      } else {
      $thumb = JURI::root().'components/com_videoflow/players/preview.jpg';
      }
   return $thumb;
   } 
   
   
   function loadCstyle()
   {
      $tmpl = JRequest::getCmd ('tmpl');
      $doc = JFactory::getDocument();
      $cssshared = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/videoflow.css';
      $doc->addStyleSheet( $cssshared, 'text/css', null, array() );
      if ($tmpl == 'component') {
      $css = JURI::root().'templates/system/css/system.css';
      $doc->addStyleSheet( $css, 'text/css', null, array() );
      }
   }
   
 
  function checkPerms()
  {
    global $vparams;
    $fbuser = JRequest::getVar('fbuser');
    $lo = JRequest::getVar('layout');
    if (!empty($lo)) $lo = '&layout='.$lo; else $lo = '';
    $c = JRequest::getCmd('c');
    if ($c == 'fb') {
      $target = 'target="_parent"';
      $redir = $vparams->canvasurl;
    } else {
      $target = '';
      $redir = JURI::root().'index.php?option=com_videoflow'.$lo;
    }
    if (!empty($fbuser)) {
      $perms = JRequest::getVar('perms');
      if (empty($perms[0]['publish_stream'])) {
      if (!empty($lo)) $lo = '&layout='.$lo; else $lo = '';
      $promptperm = '<div><div style="width:97%; padding: 4px 8px; margin: 4px 0px; background-color:#ffebe8; border: 1px solid #dd3c10;"><a href="http://www.facebook.com/dialog/oauth/?scope=publish_stream&client_id='.$vparams->appid.'&redirect_uri='.$redir.'&response_type=token" '.$target.' >'.JText::_('Click here to allow us to create updates for your Facebook News Feed (e.g. when you upload a file)').'</a></div></div>';
      $this->assignRef ('promptperm', $promptperm);
      } 
    }
  }
 
   
   function loadVflowModules()
   {
     global $vparams;         
     jimport('joomla.application.module.helper');
      $vmods = array('vflow1', 'vflow2', 'vflow3', 'vflow4', 'vflow5', 'vflow6', 'vflow7', 'vflow8', 'vfshare', 'vflike', 'vflowx', 'vflowpv', 'vflowx2');
      $pos = 1;
      foreach ($vmods as $vmods){
        $vmodule = JModuleHelper::getModules($vmods);
        if (!empty($vmodule)){
        $vmodule = JModuleHelper::renderModule ($vmodule[0], array('style'=>'table'));
        } else {
        $vmodule = '';
        if ($vmods == 'vfshare') $vmodule = JText::_('COM_VIDEOFLOW_NOTICE_SHARE_MOD');
        }
        $vfpos = 'vflow'.$pos; 
        if ($vmods == 'vfshare') $vfpos = $vmods;  
        if ($vmods == 'vflike') $vfpos = $vmods; 
        if ($vmods == 'vflowx') $vfpos = $vmods;   
        if ($vmods == 'vflowpv') $vfpos = $vmods;   
        if ($vmods == 'vflowx2') $vfpos = $vmods;                         
        if ($vparams->findvmods) $vmodule = '<h2>'.$vfpos.'</h2>';
        $this->assignRef ($vfpos, $vmodule);
        $pos++;
      } 
   } 
    
  function doPagination ()
  {
    global $vparams;
    $model = $this->getModel();
    $pages = $model->getPagination();
    $pages->pages = $pages->getPagesLinks();
    $context = JRequest::getCmd('c');
    if ($context == 'fb') {
      $regex = "/(title=.+?)+[>]/i";
      $pages->pages = preg_replace ($regex, '$1 target="_top">', $pages->pages);
      $pages->pages = str_replace(array(rtrim (JURI::root(), '/'), '/index.php?option=com_videoflow', '&amp;tmpl=component', '&amp;c=fb'), array ('', $vparams->canvasurl, '', ''), $pages->pages);
  }
  return $pages;  
  }
  
  function printPagination(){
   if (!empty($this->pagination)) {
	include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
	$p = new videoflowHTMLEXT();
	$p->printPagination($this->pagination);
   } 
  }
  
  function doRoute($lnk)
  {
    global $vparams;
    $c = JRequest::getCmd('c');
    if ($c == 'fb') {
      $lnk = $vparams->canvasurl.$lnk;
    } else {
      $lnk = JRoute::_('index.php?option=com_videoflow'.$lnk);
    }
    return $lnk;
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
  include_once(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'resize-class.php');
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
  
  function getXparams()
  {
    global $fxparams, $vparams;
    $context = JRequest::getCmd('c');
    if ($context == 'fb') {
    $xparams = $fxparams;    
    } else {
    if (empty($vparams->flowid)) {
    $model = $this->getModel();
    $menuid = $model->getMenuID();
    } else {
    $menuid = $vparams->flowid;
    }
    if (empty($menuid)) $menuid = 0;
    $menu = JMenu::getInstance('site');
    $xparams = $menu->getParams($menuid);
    }
    return $xparams;
  }
  
    function dispMenu(){
	if (is_array($this->menu)) {
          include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
          $m = new videoflowHTMLEXT();
          $m->menu = $this->menu;
          $m->printMenu();
        } 
    }
   
  function createCredit()
  {
    global $vparams;
    $app = JFactory::getApplication();
    $c = JRequest::getCmd ('c');
    $credit = JText::_('COM_VIDEOFLOW_PWD_BY').' '.'<a href="http://www.videoflow.tv">VideoFlow</a>';
    if ($c == 'fb') {
      $site = $app->getCfg('sitename');
      if (empty($site)) $site = rtrim('/', JURI::root());
      $credit .= ' '.JText::_('COM_VIDEOFLOW_AND').' '.'<a href="'.JURI::root().'">'.$site.'</a>';
    }
    $this->assignRef('credit', $credit);
  }
}