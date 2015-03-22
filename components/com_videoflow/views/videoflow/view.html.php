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
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
if(class_exists('JViewLegacy')) {
    class ViewSelector extends JViewLegacy {}
} else {
    class ViewSelector extends JView {}
}
class VideoflowViewVideoflow extends ViewSelector
{

  function __construct()
    {      
      global $vparams;
      parent::__construct();
      $this->loadVflowModules();
      switch ($vparams->jsframework) {
      case 'mootools':
        $prep = $this->loadVFMoo(true);
        $sel = "$";
        break;
      case 'jquery':
        $prep = $this->loadVFJQ(true);
        $sel = 'jQuery';
        break;
      case 'auto':
      default:
        if(!JVERS3) {
          $prep = $this->loadVFMoo(true);
          $sel = '$';
        } else {
          $prep = $this->loadVFJQ(true);
          $sel = 'jQuery';
        }
      }
      
      if (JVERS3 && $vparams->lightbox && ($vparams->lightboxsys == 'joomlabox' || $vparams->lightboxsys == 'multibox')) $this->loadVFMoo(true); 
      
      $this->assignRef('loadjsprepend', $prep);
      $this->assignRef ('selector', $sel);
      $vtask = JRequest::getCmd ('task');
      $tmpl = JRequest::getVar('tmpl');
      $this->loadCstyle();
      if ($vtask == 'myvids' || $vtask == 'subscribe' || $vtask == 'visit' || $vtask == 'mysubs') $this->setOwner();      
      if ($vparams->lightbox) if ($vparams->lightboxsys=='multibox' || $vparams->lightboxsys == 'colorbox') $this->loadLightbox();
      if ($vparams->facebook) $this->checkPerms();
      if ($vparams->showcredit) $this->createCredit();
    }
    
    function display ($jtmpl = null)
    {
        global $vparams;
        $model = $this->getModel();
        $xparams = $this->getXparams();
        $vlist = $model->getData ();
        $clist = $model->getCategories();
        $menu = $model->getMenu();
        $slist = (bool) $xparams->get('slist', $vparams->slist);
        $slimit = (int) $xparams->get('slistlimit', $vparams->slistlimit);
        if(!empty ($vlist)) $vlist = $model->updateData($vlist);
        $pagination = $this->doPagination ();
        $id = JRequest::getInt('id');
        if (!empty($id)) {
          $media = $model->getMedia($id, 1);
          if ($vparams->ratings) {
            $vrating = $model->createRating ($id);
            $this->assignRef ('rating', $vrating);
          }
          if ($vparams->addthis) {
          $addthis = $model->createAddThis($id);
          $this->assignRef ('addthis', $addthis);
          }
          if (!empty($vparams->commentsys)) {
            $comments = $model->getComments($media);
            $this->assignRef ('comments', $comments);
          }
          $add = JRequest::getInt('add', 1);
          if ($add == 0) $add = 'remove'; else $add = 'add';
          $tools = $model->getTools($media, $add);
          if ($slist) {
            $related = $model->getRelated ($media, $slimit);
            if (!empty($related)) $related = $model->updateData($related);
            $this->assignRef('tabone', $related);
          }
          $this->assignRef('media', $media);
          $this->assignRef ('tools', $tools);
        }
        $this->assignRef ('vlist', $vlist);
        $this->assignRef ('menu', $menu); 
        $this->assignRef ( 'pagination', $pagination);   
        $this->assignRef ('cats', $clist);
        if (!empty($media)) $this->sendHeaders($media); else $this->sendHeadersAlt();
        parent::display();
    }

   
   function displayMedia($id)
   {
        global $vparams;
        $model = $this->getModel();
        $lo = JRequest::getCmd('layout', $vparams->jtemplate);
        $c = JRequest::getCmd ('c');
        $xparams = $this->getXparams();
        $tabtitlelength = (int) $xparams->get('tabtitlelength', $vparams->shorttitle);
        $xlimit = (int) $xparams->get('limit', $vparams->limit);
        $slimit = (int) $xparams->get('slistlimit', $vparams->slistlimit);
        $slist = (int) $xparams->get('slist', $vparams->slist);  
        $media = $model->getMedia($id);
        if ($lo == 'playerview') {
        $related = $model->getRelated($media, $xlimit);
        $pagination = $model->getPagination();
        } elseif ($lo == 'simple'){
        if ($slist) {
        $related = $model->getRelated($media, $slimit);
        }
        $vlist = $model->getData();
        if(!empty ($vlist)) $vlist = $model->updateData($vlist);
        $clist = $model->getCategories();
        $pagination = $model->getPagination();
        } else {
        $related = $model->getRelated($media);
        } 
        $byuser = $model -> getByuser ($media->userid, $id);
        if (!empty($tabtitlelength)) {
        if (!empty($related)) $related = $model->xterWrap($related, array("title"=>$tabtitlelength));
        if (!empty($byuser)) $byuser = $model->xterWrap($byuser, array("title"=>$tabtitlelength));
        }
        if (!empty($vparams->commentsys)) {
          $comments = $model->getComments($media);
          $this->assignRef ('comments', $comments);
        }
        $menu = $model->getMenu();
        if ($vparams->ratings) {
          $vrating = $model->createRating ($id);
          $this->assignRef ('rating', $vrating);
        }
        if ($vparams->addthis) {
          $addthis = $model->createAddThis($id);
          $this->assignRef ('addthis', $addthis);
        }
        $add = JRequest::getInt('add', 1);
        if ($add == 0) $add = 'remove'; else $add = 'add';
        $tools = $model->getTools($media, $add);
        if (!empty($vlist))$this->assignRef('vlist', $vlist);
        $this->assignRef('media', $media);
        $this->assignRef ('tools', $tools);
        $this->assignRef('tabone', $related);
        if (!empty($clist)) $this->assignRef ('cats', $clist);
        $vcount = JRequest::getInt('vcount', '');
        $ucount = JRequest::getInt('ucount', '');
        $this->assignRef ('tab1count', $vcount);
        $this->assignRef ('tabtwo', $byuser);
        $this->assignRef ('tab2count', $ucount);
        $this->assignRef ('menu', $menu); 
        if (!empty($pagination)) $this->assignRef('pagination', $pagination);
        $this->sendHeaders($media);   
        parent::display(); 
   }
   
   
   function setXplay($id)
   {
        global $vparams;
        $model = $this->getModel();
        $lo = JRequest::getCmd('layout', $vparams->jtemplate);
        $xparams = $this->getXparams();
        $tabtitlelength = (int) $xparams->get('tabtitlelength', $vparams->shorttitle);
        $slimit = (int) $xparams->get('slistlimit', $vparams->sidebarlimit);
        $slist = (int) $xparams->get('slist', 1);
        $media = $model->getMedia($id, 1);
        if ($lo == 'simple'){
          if ($slist) {
          $related = $model->getRelated($media, $slimit);
          }
        } else {
        $related = $model->getRelated($media);
        } 
        if (!empty($tabtitlelength)) {
        if (!empty($related)) $related = $model->xterWrap($related, array("title"=>$tabtitlelength));
        }
        if (!empty($vparams->commentsys)) {
          $comments = $model->getComments($media);
          $this->assignRef ('comments', $comments);
        }
        if ($vparams->ratings) {
          $vrating = $model->createRating ($id);
          $this->assignRef ('rating', $vrating);
        }
        if ($vparams->addthis) {
          $addthis = $model->createAddThis($id);
          $this->assignRef ('addthis', $addthis);
        }
        $add = JRequest::getInt('add', 1);
        if ($add == 0) $add = 'remove'; else $add = 'add';
        $tools = $model->getTools($media, $add);
        $this->assignRef('media', $media);
        $this->assignRef ('tools', $tools);
        $this->assignRef('tabone', $related);
        if (!empty($clist)) $this->assignRef ('cats', $clist);
        $this->sendHeaders($media);   
   }

   
  
   function displayMyvids($myid)
   {
        global $vparams;
        $task = JRequest::getCmd ('task');
        $model = $this->getModel();
        $id = $uid = $fid = JRequest::getInt('id', null);
        $lo = JRequest::getCmd ('layout', $vparams->jtemplate);
        if ($lo == 'playerview' || $lo == 'ajaxlist' || $lo == 'simple'){
        $this->pvDisplayMyvids($myid);
        return;
        }
        if ($id) $media = $model->getMedia($id);
        $tab = JRequest::getWord ('tab');
        if ($tab == 'one') {
        $fid = null;
        } else if ($tab == 'two') {
        $uid = null;
        } else {
        $uid = $fid = null;
        }
        $uploads = $model->getByuser($myid, $uid);
        $favourites = $model->getFavourites($myid, $fid);
        $xparams = $this->getXparams();
        $tabtitlelength = (int) $xparams->get('tabtitlelength',10);
        if (!empty($tabtitlelength)) {
        if (!empty($uploads)) $uploads = $model->xterWrap($uploads, array("title"=>$tabtitlelength));
        if (!empty($favourites)) $favourites = $model->xterWrap($favourites, array("title"=>$tabtitlelength));
        }

        $fcount = JRequest::getInt('fcount', 0);
        $ucount = JRequest::getInt('ucount', 0);
        $add = JRequest::getInt('add', 1);
        if ($add) $add = 'add'; else $add = 'remove';
        if (empty($media) && $lo != 'playerview' && $lo != 'ajaxlist') {
            if (!empty ($uploads)){
            $media = $uploads [0];
            unset ($uploads[0]);
            $ucount = $ucount - 1;
            } 
            elseif (!empty($favourites)){
            $media = $favourites[0];
            unset ($favourites[0]);
            $fcount = $fcount - 1;
            $add = 'remove';
            } else {
            $media = $model->getMedia(null);
            $add = 'add';
            if (empty($vparams->help)) {
            $help = JRoute::_('http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&id=58#playlist');
            } else {
            $help = JRoute::_($vparams->help);
            }
            JError::raiseWarning(400, JText::_('COM_VIDEOFLOW_WARN_MEDIA_PLAYLIST').' '.
            '<a href="'.$help.'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: \'600\', y: \'480\'}}">'.
            JText::_('COM_VIDEOFLOW_CLICK_HELP').'</a>');
            }
                if ($media){
                $media->usrname = $model->getUsername ($media->userid);
                $media->shortname = $model->runTool('xterWrap', $media->usrname, $vparams->shortname);
                $media->embedcode = $model->_buildCode($media);
                $media->shorttitle = $model->runTool('xterWrap', $media->title, $vparams->shorttitle);
                $media->catname = $model->getCatName ($media->cat);
                if (!empty($media->tags)) $media->autotags = $model->processTags ($media->tags); else $media->autotags = '';
                } 
        }
        if (!empty($media)) {
            if ($vparams->ratings) {
              $vrating = $model ->createRating ($media->id);
              $this->assignRef ('rating', $vrating);
            }
            if ($vparams->addthis) {
              $addthis = $model->createAddThis($id);
              $this->assignRef ('addthis', $addthis);
            }
            if (!empty($vparams->commentsys)) {
              $comments = $model->getComments($media);
              $this->assignRef ('comments', $comments);
            }
            $tools = $model->getTools ($media, $add);
            $this->assignRef('media', $media);
            $this->assignRef ('tools', $tools);
        }
        
        if ($task == 'myvids') {
        $juser_id = $myid; 
        } else {
        $juser = JRequest::getVar('juser');
          if (empty($juser)) $juser =  JFactory::getUser();
        $juser_id = $juser->id;
        }
        $subaction = $model->setSubAction($juser_id);
        if ($subaction) $this->assignRef('subaction', $subaction);
        $menu = $model->getMenu();
        $subcount = $model->countSubscribers ($myid);
        if (!$subcount) $subcount = 0;
        $this->assignRef ('menu', $menu); 
        $this->assignRef ('favcount', JRequest::getInt('f_count', 0));
        $this->assignRef ('uploadcount', JRequest::getInt('u_count', 0));
        $this->assignRef ('visitcount', $model->setVisitors($myid));
        $this->assignRef ('subcount', $subcount);
        $this->assignRef('tabone', $uploads); 
        $this->assignRef ('tabtwo', $favourites);
        $this->assignRef ('tab1count', $ucount);
        $this->assignRef ('tab2count', $fcount);
        if (!empty($media)) $this->sendHeaders($media); else $this->sendHeadersAlt();
        parent::display(); 
   }

    function pvDisplayMyvids($myid)
    {
        global $vparams;
        $cname = JRequest::getString('cname');
        $task = JRequest::getCmd ('task');
        $list = JRequest::getWord ('list'); 
        $model = $this->getModel();
        $id = JRequest::getInt('id');
        $add = JRequest::getInt('add', 1);
        $lo = JRequest::getCmd ('layout', $vparams->jtemplate);
        $xparams = $this->getXparams();
        if (empty($add)) $add = 'remove'; else $add = 'add';
        if ($id) $media = $model->getMedia($id);
        if (empty($list) || $list == 'ups') {
        $vlist = $model->getByuser($myid, $id);
        $mylist = 'uploads';
        $favcount = $model->countFavs($myid);
        }
        if (empty($vlist) && (empty($list) || $list == 'favs')) {  
        $vlist = $model->getFavourites ($myid, $id);
        $mylist = 'favourites';
        $upcount = $model->countUploads($myid);
        }
        if (empty($media) && empty($vlist)) {
            $media = $model->getMedia(null);
            if (empty($vparams->help)) {
            $help = JRoute::_('http://videoflow.fidsoft.com/index.php?option=com_content&tmpl=component&id=58#playlist');
            } else {
            $help = JRoute::_($vparams->help);
            }
            if ($list == 'favs') {
            $errmes = 'COM_VIDEOFLOW_NO_MEDIA_PLAYLIST';
            if ($task == 'visit') $errmes = 'COM_VIDEOFLOW_CHANNEL_NO_FAVOURITES';
            } else if ($list = 'ups') {
            $errmes = 'COM_VIDEOFLOW_NO_UPLOADS';
            if ($task == 'visit') $errmes = 'COM_VIDEOFLOW_NO_CHANNEL_UPLOADS';
            } else {
            $errmes = 'COM_VIDEOFLOW_WARN_MEDIA_PLAYLIST';
            if ($task == 'visit') $errmes = 'COM_VIDEOFLOW_CHANNEL_NO_MEDIA';
            }
            if ($task == 'visit') {
            JError::raiseWarning(400, JText::_($errmes));
            } else {
            JError::raiseWarning(400, JText::_($errmes).' '.
            '<a href="'.$help.'" class="modal-vflow" rel="{handler: \'iframe\', size: {x: \'600\', y: \'480\'}}">'.
            JText::_('COM_VIDEOFLOW_CLICK_HELP').'</a>');
            }              
        }        
        $xparams = $this->getXparams();
        $ajaxmode = (int) $xparams->get('ajaxmode');
        if (!empty($media)) {
            if($mylist == 'favourites' && $task != 'visit') {
              if (!empty ($vlist) || !empty($id)) $add = 'remove'; else $add = 'add';
            } else {
              $add = 'add';
            }
            if ($vparams->ratings) {
              $vrating = $model ->createRating ($media->id);
              $this->assignRef ('rating', $vrating);
            }
            if ($vparams->addthis) {
              $addthis = $model->createAddThis($id);
              $this->assignRef ('addthis', $addthis);
            }
            if (!empty($vparams->commentsys)) {
              $comments = $model->getComments($media);
              $this->assignRef ('comments', $comments);
            }
            $tools = $model->getTools ($media, $add);
            $this->assignRef('media', $media);
            $this->assignRef ('tools', $tools);
            }
        
        if ($task == 'myvids') {
        $juser_id = $myid; 
        } else {
        $juser = $this->owner;
        $juser_id = $juser->id;
        }
        
       $fbuser = JRequest::getVar('fbuser');
       if (!empty($fbuser)) {
        include_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_user_manager.php');
        $um = new VideoFlowUserManager;
        $userobj = $um->getVFuserObj ($fbuser);
        if (!empty($userobj->joomla_id)) $inuser = $userobj->joomla_id;
       } else {
        $userobj =  JFactory::getUser();
        if (!empty($userobj->id)) $inuser = $userobj->id; 
       }
        if (!empty($inuser)) {
        $subaction = $model->setSubAction($inuser);
        if ($subaction) $this->assignRef('subaction', $subaction);
        }
        $menu = $model->getMenu();
        $subcount = $model->countSubscribers ($myid);
        if (!$subcount) $subcount = 0;
        if (empty($favcount)) $favcount = JRequest::getInt('f_count', 0);
        if (empty($upcount)) $upcount = JRequest::getInt('u_count', 0);
        $vcount = $model->setVisitors($myid);
        $this->assignRef ('menu', $menu); 
        $this->assignRef ('mylist', $mylist);
        $this->assignRef ('myid', $myid);
        $this->assignRef ('favcount', $favcount);
        $this->assignRef ('uploadcount', $upcount);
        $this->assignRef ('visitcount', $vcount);
        $this->assignRef ('subcount', $subcount);
        if (!empty($vlist)) {
        $vlist = $model->updateData($vlist);
        $this->assignRef ('vlist', $vlist);
        $pagination = $this->doPagination ();
        $this->assignRef('pagination', $pagination);
        }
        if (empty($ajaxmode)) {
           if (!empty($media)) $this->sendHeaders($media); else $this->sendHeadersAlt();
        }
        
        if ($lo == 'simple') { 
          $clist = $model->getCategories();
          $this->assignRef('cats', $clist);
          $slist = (bool) $xparams->get('slist', 1);
          $slimit = (int) $xparams->get('slistlimit', $vparams->sidebarlimit);
          if ($slist && !empty($media)) {
            $related = $model->getRelated ($media, $slimit);
            if (!empty($related)) {
            $related = $model->updateData($related);
            $this->assignRef('tabone', $related);
            }
          }
          if (empty($cname)) {
            if (!empty($this->fbowner)) {
            include_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_user_manager.php');
            $um = new VideoFlowUserManager;
            $fuinfo = $um->getFBuserInfo($this->fbowner);
            if (isset($fuinfo['first_name'])) $cname = $fuinfo['first_name']; else $cname = '';
            } else {
            if ($vparams->displayname) $cname = $this->owner->name; else $cname = $this->owner->username;  
            }
        }
        if (!empty($this->fbowner)) {
        if (isset($fuinfo['link'])) {
        $this->cpix = '<div><a href="'.$fuinfo['link'].'"><img src="http://graph.facebook.com/'.$this->fbowner.'/picture" alt="'.$fuinfo['name'].'" width="32" height="32"/></a>';     
        } else {
        $this->cpix = '<fb:profile-pic uid="'.$this->fbowner.'" width="32" height="32" linked="true"></fb:profile-pic>';
        }
        $this->cname = '<div style="float:left; margin: 2px; padding: 0px 5px; border-left: 1px dotted #CCCCCC; border-right: 1px dotted #CCCCCC;"><fb:name uid="'.$this->fbowner.'" possessive="true" useyou="true" capitalize="true" linked="true"></fb:name> '.JText::_('Channel').'</div>';
        } else {
        $this->cname = '<div style="float:left; margin: 2px; padding: 0px 5px; border-left: 1px dotted #CCCCCC; border-right: 1px dotted #CCCCCC;">'.$cname.' '.JText::_('Channel').'</div>';  
        $this->cpix = null;
        }
        if ($vparams->mode == 'videoflow') {
        $menu2 = array('myvids&list=ups'=>JText::_('COM_VIDEOFLOW_UPLOADS'), 'myvids&list=favs'=>JText::_('COM_VIDEOFLOW_FAVOURITES'));
          if ($task == 'visit' || $task == 'ups' || $task == 'favs') {
           $menu2 = array('visit&list=ups'=>JText::_('COM_VIDEOFLOW_UPLOADS'), 'visit&list=favs'=>JText::_('COM_VIDEOFLOW_FAVOURITES'));
          }
        } else {
        $menu2 = array();
        }
          $menu2 = $this->createMenu2($menu2, $juser_id, $cname);
          $this->assignRef('menu2', $menu2);
        }

        parent::display(); 
  }


  
  function displayUserfavs()
  {
    $usrid = JRequest::getInt ('usrid');
    $model = $this->getModel();
    $menu = $model->getMenu();
    $favourites = $model->getFavourites ($usrid);
    $clist = $model->getCategories();
    if (!empty($favourites)) {
            $favourites = $model->updateData($favourites);
            $pagination = $model-> getPagination ();
            $this->assignRef ('vlist', $favourites);
            $this->assignRef ( 'pagination', $pagination); 
            }     
    $this->assignRef ('cats', $clist);
    $this->assignRef ('menu', $menu);
    parent::display();
  }
  
  function displayMysubs($myid)
  {
    global $vparams;
    $id = JRequest::getInt('id');
    $pid = JRequest::getInt('pid');
    $model = $this->getModel();
    $menu = $model->getMenu();
    $subs = $model->getMysubs ($myid);
    if (!empty($subs)) {
    $subs = $model->getCname ($subs);
      if (!empty($pid)) {
      $vids = $model->getByuser ($pid);
      if (empty($vids)) $vids = $model->getFavourites($pid);
      } else {
      $vids = $model->findMedia ($subs, 'joomla_id', 'uploads');   
      $utext = JText::_('COM_VIDEOFLOW_UPLOADS');
          if (empty($vids)) {
          $vids = $model->findMedia($subs, 'joomla_id', 'favourites');
          $utext = JText::_('COM_VIDEOFLOW_FAVOURITES');
          }
      }
      if (empty($vids)) $utext = JText::_('COM_VIDEOFLOW_CHANNEL_HAS_NO_MEDIA');
    } else {
    JError::raiseWarning(400, JText::_('COM_VIDEOFLOW_NO_SUBS'));
    }
    if (!empty($vids)) {
        $xparams = $this->getXparams();
        $tabtitlelength = (int) $xparams->get('tabtitlelength',10);
        $vids = $model->xterWrap($vids, array("title"=>$tabtitlelength));
    }
    if (!empty($id)) {
    $media = $model->getMedia($id);
    } else if (!empty($vids)) {
    $id = $vids ['0']->id;
    unset ($vids [0]);
    $media = $model->getMedia($id);
    }
    
    if (!empty($media)) {
        $tools = $model->getTools($media, 'add');
        if ($vparams->ratings) {
          $vrating = $model ->createRating ($media->id);
          $this->assignRef ('rating', $vrating);
        }
        if ($vparams->addthis) {
            $addthis = $model->createAddThis($id);
            $this->assignRef ('addthis', $addthis);
        }
        if (!empty($vparams->commentsys)) {
          $comments = $model->getComments($media);
          $this->assignRef ('comments', $comments);
        }
        $this->assignRef('media', $media);
        $this->assignRef ('tools', $tools);
        }
    $ucount = JRequest::getInt ('ucount');
    $fcount = JRequest::getInt ('fcount');
    $subcount = $model->countSubscribers ($myid);
    $favcount = $model->countFavs($myid);
    $uploadcount = $model->countUploads ($myid);

    $this->assignRef ('tabone', $subs);
    $this->assignRef ('tabtwo', $vids);
    $this->assignRef ('tab2count', $ucount);
    $this->assignRef ('tab1count', $fcount);
    $this->assignRef ('menu', $menu);
    $this->assignRef ('utext', $utext);
    $this->assignRef ('favcount', $favcount);
    $this->assignRef ('uploadcount', $uploadcount);
    $this->assignRef ('visitcount', $model->setVisitors($myid));
    $this->assignRef ('subcount', $subcount);
    if (!empty($media)) $this->sendHeaders($media); else $this->sendHeadersAlt();
    parent::display();
  }

   
   function displaySearch()
   {
        global $vparams;
        $lo = JRequest::getCmd ('layout');
        $c = JRequest::getCmd ('c');
        if (empty($lo) && $c == 'fb') $lo = $vparams->ftemplate;
        $model = $this->getModel();
        $menu = $model->getMenu();
        $clist = $model->getCategories();
        $this->assignRef ('menu', $menu);
        $this->assignRef ('cats', $clist);
        $vs = JRequest::getString ('vs');
        $searchword = JRequest::getString('searchword');
        if (!empty($vs) || ($lo == 'simple' && !empty($searchword))){
            $vlist = $model->doSearch();
            if (!empty($vlist)) {
            $vlist = $model->updateData($vlist);
            $pagination = $model-> getPagination ();
            $this->assignRef ( 'pagination', $pagination); 
            $this->assignRef ('vlist', $vlist);
            }
        } 
        if ($lo == 'simple') {
          $id = JRequest::getInt('id');
          if (!empty($id)) $this->setXplay($id);  
        } else {
        $this->sendHeadersAlt();
        }
        parent::display();
   } 
   
   function displaySearchPlay()
   {
        global $vparams;
        $model = $this->getModel();
        $id = JRequest::getInt('id');
        $related = null;
        $xparams = $this->getXparams();
        $xlimit = (int) $xparams->get('limit', $vparams->limit);   
        $menu = $model->getMenu();
        if (!empty($id)) {
        $media = $model->getMedia($id);
        $related = $model->getRelated($media, $xlimit);
        if (!empty($vparams->commentsys)) {
          $comments = $model->getComments($media);
          $this->assignRef ('comments', $comments);
        }
        if ($vparams->ratings) {
          $vrating = $model->createRating ($id);
          $this->assignRef ('rating', $vrating);
        }
        if ($vparams->addthis) {
          $addthis = $model->createAddThis($id);
          $this->assignRef ('addthis', $addthis);
        }
        $add = JRequest::getInt('add', 1);
        if ($add == 0) $add = 'remove'; else $add = 'add';
        $tools = $model->getTools($media, $add);
        $this->assignRef('media', $media);
        $this->assignRef ('tools', $tools);
        if (!empty($media)) $this->sendHeaders($media); else $this->sendHeadersAlt(); 
        $sw = $media->tags;
        if (empty($sw)) $sw = $media->title;
        JRequest::setVar('searchword', $sw); 
        } else {
        $related = $model->doSearch();
        }
        if (!empty($related)) {
        $related = $model->updateData($related);
        }
        $vcount = JRequest::getInt('vcount', 0);
        $model->_total = $vcount;
        $pagination = $model->getPagination();
        $this->assignRef('vlist', $related); 
        $this->assignRef ('menu', $menu); 
        if (!empty($pagination)) $this->assignRef('pagination', $pagination);
        parent::display(); 
   }

   
   function displayAdd()
   {
    $c = JRequest::getCmd('c');
    include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_html.php');
     $model = $this->getModel();
     $this->menu = $model->getMenu();
     $this->dispMenu();
     $html = new videoflowHTML;
     $html->addForm();
   } 
   
   function displayEmbedForm($data)
   {
    $bselect = array(
    JHTML::_('select.option', '0', JText::_('COM_VIDEOFLOW_YES') ),
    JHTML::_('select.option', '1', JText::_('COM_VIDEOFLOW_NO') )
    );
    
    $lselect = array (
    JHTML::_('select.option', 'music', JText::_('COM_VIDEOFLOW_MUSIC') ),
    JHTML::_('select.option', 'video', JText::_('COM_VIDEOFLOW_VIDEO') ),
    JHTML::_('select.option', 'games', JText::_('COM_VIDEOFLOW_GAMES') )
    );
    
    $this->assignRef ('data', $data);
    $this->assignRef ('bselect', $bselect);
    $this->assignRef ('lselect', $lselect);
    parent::display();
   }
   
   function displayUploadForm($data)
   {
    $tmpl = JRequest::getCmd('tmpl');	
	include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_html.php');
    $html = new videoflowHTML;
	if ($tmpl != "component") {
	$model = $this->getModel();
	$html->menu = $model->getMenu();
	}
    $html->uploadForm($data);
   }
   
   function displayEditForm ($data)
   {
    $model = $this->getModel();
    $bselect = array(
    JHTML::_('select.option', '0', JText::_('COM_VIDEOFLOW_NO') ),
    JHTML::_('select.option', '1', JText::_('COM_VIDEOFLOW_YES') )
    );
    $data->bselect = $bselect;
    $data->catlist = $model->getCategories();
    if (empty($data->selcat)) $data->selcat = $data->cat;
    include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_html.php');
   $html = new videoflowHTML;
   $html->menu = $model->getMenu();
   $html->editForm($data);
  }
   
   
   function displayChannel()
   {
    $model = $this->getModel();
    parent::display();   
   }
   
   function displayCshare()
   {
      $model = $this->getModel();
      $data = $model->fbInvite(); 
      $this->assignRef ('data', $data);
      parent::display();   
   }
   
   function displayCategories()
   {
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
   
   
   function displayByCat ($vlist) 
   {
      $model = $this->getModel();
      $cat = JRequest::getInt ('cat');
      if (empty ($cat)) $catname = JText::_('COM_VIDEOFLOW_CAT_NONE'); else $catname = $model->getCatName($cat);
      $vlist = $model->updateData($vlist);
      $clist = $model->getCategories();
      $vcount = count($vlist);
      $pagination = $model-> getPagination ();
      $this->assignRef ( 'pagination', $pagination); 
      $menu = $model ->getMenu();
      $this->assignRef ('cats', $clist);
      $this->assignRef ('vlist', $vlist);
      $this->assignRef ('menu', $menu);
      $this->assignRef ('catname', $catname);
      parent::display();
   }
   
   function displayCats($vlist)
   {
      global $vparams;
      $model = $this->getModel();
      $lo = JRequest::getCmd('layout');
      $c = JRequest::getCmd('c');
      if (empty($lo) && $c == 'fb') $lo = $vparams->ftemplate; 
      $cat = JRequest::getInt ('cat');
      if (empty ($cat)) $catname = JText::_('COM_VIDEOFLOW_CAT_NONE'); else $catname = $model->getCatName($cat);
      $id = JRequest::getInt('id');
      $media = '';
      if ($id) $media = $model->getMedia($id);
      $xparams = $this->getXparams();
      
      $slist = (bool) $xparams->get('slist', 1);
      $slimit = (int) $xparams->get('slistlimit', $vparams->sidebarlimit);
      $vcount = count($vlist);
      if ($vparams->catplay && ($lo != 'simple')) {
      if (!$id && (!empty($vlist))) {
      $id = $vlist [0]->id;
      unset ($vlist[0]);
      $vcount = $vcount - 1;
      }
      }
      
      if (!empty($id)) {
        $media = $model->getMedia($id, 1);
        if ($vparams->ratings) {
          $vrating = $model->createRating ($id);
          $this->assignRef ('rating', $vrating);
        }
        if ($vparams->addthis) {
          $addthis = $model->createAddThis($id);
          $this->assignRef ('addthis', $addthis);
        }
        if (!empty($vparams->commentsys)) {
          $comments = $model->getComments($media);
          $this->assignRef ('comments', $comments);
        }
        $add = JRequest::getInt('add', 1);
        if ($add == 0) $add = 'remove'; else $add = 'add';
        $tools = $model->getTools($media, $add);
        $this->assignRef('media', $media);
        $this->assignRef ('tools', $tools);
        if ($slist && ($lo == 'simple')) {
          $related = $model->getRelated ($media, $slimit);
          if (!empty($related)) $related = $model->updateData($related);
          $this->assignRef('tabone', $related);
        }
      }
      if ($lo == 'simple') {
        if (!empty($vlist)) {
          $vlist = $model->updateData($vlist);
          $pagination = $this->doPagination ();
          $clist = $model->getCategories();
          $this->assignRef('pagination', $pagination);
          $this->assignRef('vlist', $vlist);
          $this->assignRef('cats', $clist);
        }
      } else {
      $cats = $model ->getCatList($vparams->sidebarlimit); 
      $tabdetailslength = (int) $xparams->get('tabdetailslength', 40);
        if (!empty($tabtitlelength) || !empty($tabdetailslength)) {
        if (!empty($tabtitlelength)) $fields2['name'] = $tabtitlelength;
        if (!empty($tabdetailslength)) $fields2['desc'] = $tabdetailslength; 
        if (!empty($cats)) $cats = $model->xterWrap($cats, $fields2);
        }
      $this->assignRef ('tabtwo', $cats);
      $tcount = JRequest::getInt('count', 0);
      $this->assignRef ('tab2count', $tcount);
      if (count($vlist) > $vparams->sidebarlimit) $vlist = array_slice($vlist, 0, $vparams->sidebarlimit); 
      $this->assignRef ('tabone', $vlist);
      $this->assignRef ('tab1count', $vcount);
      }
      $menu = $model ->getMenu();
      
      $this->assignRef ('menu', $menu);
      $this->assignRef ('catname', $catname);
      if (!empty($media)) $this->sendHeaders($media); else $this->sendHeadersAlt();
      parent::display();
   }
   
   function displayEmail($id)
   {
    global $vparams;
    $model = $this->getModel();
    $eshare = JRequest::getCmd('task');
    if ($eshare == 'eshare'){
      $media = $model->getChannel($id);
    } else {
      $elink = JRequest::getVar('link', '', 'get', 'base64');
      $media = $model ->getMedia($id); 
      $media->elink = $elink;
    }
    include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_html.php');
    $html = new videoflowHTML;
    $html->menu = $model->getMenu();
    $html->emailForm($media);
   } 
   
   function displayStatus()
   {
    parent::display();
   } 
   

   function displayLogin ()
   {
    global $vparams;   
    $model = $this->getModel();
    $menu = $model->getMenu();
    $this->assignRef ('menu', $menu);   
    parent::display();
   }

  function displayLogout ()
  {
    $model = $this->getModel();
    $menu = $model->getMenu();
    $user = JFactory::getUser();
    if (!$user->guest){
    $name = $user->name;
    $this->assignRef ('name', $name);
    }
    $this->assignRef ('menu', $menu);
    parent::display();
  }
     
   function sendHeaders($media)
   {
    global $vparams;   
    $fmt = JRequest::getCmd('format');
    if ($fmt == 'raw') return;
    $app = JFactory::getApplication();
    if ($media->server == 'local' && stristr($media->file, 'http') === FALSE) {
        include_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_file_manager.php');
        $fm = new VideoflowFileManager;
        $subdir = $fm->getFileInfo ($media->type);
        $media->file = JURI::root().$vparams->mediadir.'/'.$subdir['dir'].'/'.$media->file;
    }
      if (!empty($media->pixlink)) {
         if ($media->server == 'local' && stripos($media->pixlink, 'http') === FALSE) {  
         $media->pixlink = JURI::root().$vparams->mediadir.'/_thumbs/'.$media->pixlink;
         }
       }
     if (empty($media->pixlink)) $media->pixlink = $this->getThumb($media);    
         if($vparams->player == 'neolao'){
         $vfcon = mt_rand();
         $vfplayer = JURI::root().'components/com_videoflow/players/neolao.swf';
         $mtype = 'flv';
         if ($media->type == 'mp3') $mtype = 'mp3';
         $vlink = $vfplayer.'?'.$mtype.'='.$media->file.'&v='.$vfcon.'&width='.$vparams->metapwidth.'&height='.$vparams->metapheight.'&autoplay=1&top1='.$vparams->logo.'|-15|15&startimage='.$media->pixlink;
         } else if ($vparams->player == 'JW'){
         $vfplayer = JURI::root().'components/com_videoflow/players/player.swf';
         $vlink = $vfplayer.'?file='.$media->file.'&width='.$vparams->metapwidth.'&height='.$vparams->metapheight.'&autostart=true&logo='.$vparams->logo.'&skin='.$vparams->skin.'&image='.$media->pixlink;
         } else {
         $vfplayer = JURI::root().'components/com_videoflow/players/NonverBlaster.swf'; 
         $autoval = 'true';
         if ($media->type == 'mp3') $autoval = 'false'; 
         $vlink = $vfplayer.'?mediaURL='.$media->file.'&width='.$vparams->metapwidth.'&height='.$vparams->metapheight.'&autoPlay=true&indentImageURL='.$vparams->logo.'&teaserURL='.$media->pixlink.'&showTimecode=true&crop=false';
         } 
         if (!empty($media->metaplay) && is_array($media->metaplay)) {
         if (stripos($media->metaplay['player'], '?') === false) $q = '?'; else $q = '&';
         if (empty($media->metaplay['flashvars'])) $q = '';
         $vlink = $media->metaplay['player'].$q.ltrim($media->metaplay['flashvars'], '&').'&width='.$vparams->metapwidth.'&height='.$vparams->metapheight;
         }         
        $doc = JFactory::getDocument();
        $doc->setGenerator('VideoFlow Multimedia System V.1.2.2');
        $doc->setTitle($media->title);
        $doc->setDescription ($media->details);
        $doc->setMetaData('keywords', $media->tags);
        if ($media->type != 'jpg' && $media->type != 'gif' && $media->type != 'png') {
        $doc->setMetaData ('video_width', $vparams->metapwidth);
        $doc->setMetaData ('video_height', $vparams->metapheight);
        $doc->setMetaData ('video_type', 'application/x-shockwave-flash');
        $doc->addCustomTag( '<link rel="video_src" href="'.$vlink.'" />');
        }
        $doc->addCustomTag( '<link rel="image_src" href="'.$media->pixlink.'" />');
        $doc->addCustomTag ('<link rel="canonical" href="/index.php?option=com_videoflow&task=play&id='.$media->id.'" />');
        $doc->addCustomTag ('<meta property="og:title" content="'.$media->title.'"/>');
        $doc->addCustomTag ('<meta property="og:site_name" content="'.$app->getCfg('sitename').'"/>');
        $doc->addCustomTag ('<meta property="og:description" content="'.$media->details.'"/>');
        $doc->addCustomTag ('<meta property="og:url" content="'.JURI::root().'index.php?option=com_videoflow&task=play&id='.$media->id.'"/>');
        if ($media->type != 'jpg' && $media->type != 'gif' && $media->type != 'png') {
        $doc->addCustomTag ('<meta property="og:video" content="'.$vlink.'"/>');
        $doc->addCustomTag ('<meta property="og:video:height" content="'.$vparams->metapheight.'"/>');
        $doc->addCustomTag ('<meta property="og:video:width" content="'.$vparams->metapwidth.'"/>');
        $doc->addCustomTag ('<meta property="og:video:type" content="application/x-shockwave-flash"/>');
        }
        if (!empty($vparams->appid)) {
        $doc->addCustomTag ('<meta property="fb:app_id" content="'.$vparams->appid.'"/>');
        }
        $doc->addCustomTag ('<meta property="og:image" content="'.$media->pixlink.'"/>');
        $doc->addCustomTag ('<meta property="og:type" content="article"/>');
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
        $doc->setGenerator('VideoFlow Multimedia System V.1.2.2');
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
   
   function loadLightbox()
   {
   global $vparams;
      $doc = JFactory::getDocument();
      $c = JRequest::getCmd('c');
      $layout = JRequest::getVar ('layout');
      if (empty($layout)) {
      $layout = $vparams->jtemplate;
      if ($c == 'fb') $layout = $vparams->ftemplate;
      }
      if ($vparams->lightboxsys == 'multibox') {
      $css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/multibox.css';
      $this->vlay = 1;
      if ($c == 'fb') {
        $css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/multibox_fb.css';
        $this->vlay = 0;
      }
      $doc->addStyleSheet( $css, 'text/css', null, array() );
      if ($vparams->mootools12) $mfile = '_legacy'; else $mfile = '';  
      if (!$vparams->vmode) {
      $this->mboffset = 0;
      } else {
      $this->mboffset = 1;
      }
      $overlay = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/overlay'.$mfile.'.js';
      $doc->addScript( $overlay );
      $box = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/multibox'.$mfile.'.js';
      $doc->addScript( $box );
      } elseif ($vparams->lightboxsys == 'colorbox') {
      $css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/colorbox/'.$vparams->cboxtheme.'/colorbox.css';
      $doc->addStyleSheet( $css, 'text/css', null, array() );     
      $vfprep = "";
      if ($vparams->iframecentre || $vparams->iframecss) {
      $vfprep .= "vfstylePlugin:true,";  
      $vfplugin = JURI::root().'components/com_videoflow/views/videoflow/tmpl/colorbox/jquery.vfstyle.js';
      if ($vparams->iframecentre) $vfprep .= "applyStyle:true,";
      if ($vparams->iframecss) $vfprep .= " loadCss:true,";
      $doc->addScript($vfplugin);
      }
      $box = JURI::root().'components/com_videoflow/views/videoflow/tmpl/colorbox/jquery.colorbox.js';
      $doc->addScript( $box );
      $prev = JText::_('COM_VIDEOFLOW_PREV');
      $next = JText::_('COM_VIDEOFLOW_NEXT');
      $close = JText::_('COM_VIDEOFLOW_CLOSE');
      $height = $vparams->lplayerheight + $vparams->lboxh;
      $width = $vparams->lplayerwidth + $vparams->lboxw;
      if ($vparams->ratings || (!empty($this->vfshare))) $height = $height + 30;
      $clrbox = "
      jQuery(document).ready(function() {
      jQuery('a.vf_mbox').colorbox({rel:'vf_mbox', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});
      jQuery('a.vmodal-vflow').colorbox({current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:true, iframe:true, innerWidth:$width, innerHeight:$height});";      
      if ($vparams->showtabs && ($layout == 'listview' || $layout == 'grid')) {
      $clrbox .= "jQuery('a.vf_mbox_cb').colorbox({rel:'vf_mbox_cb', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});";
      }
      if ($vparams->lightboxfull) {
      $clrbox .= "jQuery('a.vf_mboxx').colorbox({rel:'vf_mboxx', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});";  
      if ($vparams->showtabs && ($layout == 'listview' || $layout == 'grid')) {
      $clrbox .= "jQuery('a.vf_mboxx_cb').colorbox({rel:'vf_mbox_cb', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});";
      }
      }
      $clrbox .= "});";
      $doc->addScriptDeclaration($clrbox);  
      }
   }
   
  function loadCstyle()
  {
    include_once(JPATH_SITE.DS.'components'.DS.'com_videoflow'.DS.'html'.DS.'videoflow_htmlext.php');
    $cs = new videoflowHTMLEXT();
	  $cs->loadCstyle();
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
        $vmodule = &JModuleHelper::getModules($vmods);
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
   
   function setOwner()
   {
   global $vparams;
   include_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_user_manager.php');
   $um = new VideoflowUserManager;
   $task = JRequest::getCmd ('task');  
   if ($task == 'myvids' || $task == 'mysubs'){
    $owner = JRequest::getVar ('juser');
    if (empty($owner)) $owner = JFactory::getUser();
    if (!empty($owner->id) && $vparams->facebook) {
     $fbowner =  $um->getFBuserObj($owner->id);
    }
    } else if ($task == 'visit' || $task == 'subscribe'){
     $cid = JRequest::getInt('pid', JRequest::getInt('cid'));
     if (!empty($cid) && $vparams->facebook) {
     $fbowner =  $um->getFBuserObj($cid);
    }    
    $owner = JFactory::getUser($cid);
    if (!$owner) return;
    if (!$owner->name) $owner->name = JText::_('COM_VIDEOFLOW_GUEST');
    } else {
    return;
    }
    $this->assignRef('owner', $owner);
    if ($vparams->facebook && (!empty($fbowner->fb_id))) {
    $this->assignRef('fbowner', $fbowner->fb_id);
    $fbownerdata = $um->getFBuserInfo($fbowner->fb_id);
    if (!empty($fbownerdata)) $this->assignRef ('fbownerdata', $fbownerdata);
    }
  }
    
  function createMenu2($arr_task, $cid = null, $cname = null)
  {  
  global $vparams;
  $vmedia = JRequest::getWord('media');
  $ftask = JRequest::getCmd ('task');
  $context = JRequest::getCmd('c');
  $lo = JRequest::getVar('layout');
  $itemid = JRequest::getInt('Itemid');
  $list = JRequest::getWord ('list', null);
  if (!empty($itemid)) $itemid = '&Itemid='.$itemid; else $itemid = '';
  if (!empty($lo)) $lo = '&layout='.$lo; else $lo = '';
  if ($context == 'fb') {
    $root = $vparams->canvasurl;
    $target = 'target="_parent"';
  } else {
    $root = JURI::root().'index.php?option=com_videoflow'.$lo;
    $target = '';
  }
  if (!empty($vmedia)) $media = '&media='.$vmedia; else $media = '';
  if (!empty($cid)) $c_id = '&cid='.$cid; else $c_id = '';
  if (!empty($cname)) $c_name = '&cname='.$cname; else $c_name = ''; 
      $fbtabs = '<ul class="nav nav-pills">';
      foreach ($arr_task as $vtask=>$name) {
        $name = 'COM_VIDEOFLOW_'.strtoupper($name);
        if (strstr($vtask, $list) !== false) {
        $fbtabs .= '<li class="active"><a href="'.$root.'&task='.$vtask.$c_id.$c_name.$media.$itemid.'"'.$target.'>'.JText::_($name).'</a></li>';  
        } else {
        $fbtabs .= '<li><a href="'.$root.'&task='.$vtask.$c_id.$c_name.$media.$itemid.'"'.$target.'>'.JText::_($name).'</a></li>';
        }
      }
      if ($ftask == 'myvids' || $ftask == 'myups' || $ftask == 'myfavs' || $ftask == 'visit' || $ftask == 'ups' || $ftask == 'favs' ) {
      if (!empty($cid) && $vparams->showpro){
        $sclass = $this->genClass('normal');
        $wclass = $this->genClass('wide', 'noclass');
        if ($ftask == 'myvids' || $ftask == 'myups' || $ftask == 'myfavs') {
        $fbtabs .= '<li><a href="'.$root.'&task=mysubs&cid='.$cid.$itemid.'"'.$target.'>'.JText::_('COM_VIDEOFLOW_SUBS').'</a></li>';     
        } else if (!empty ($this->subaction) && (strpos ($this->subaction, 'unsubscribe') !== false)) {
        $fbtabs .= '<li><a href="'.JURI::root().'index.php?option=com_videoflow&task=unsubscribe&cid='.$cid.'&cname='.$cname.$itemid.'&tmpl=component" class="'.$sclass['class'].'" '.$sclass['rel'].'>'.JText::_('Unsubscribe').'</a></li>';       
        } else {   
        $fbtabs .= '<li><a href="'.JURI::root().'index.php?option=com_videoflow&task=subscribe&cid='.$cid.'&cname='.$cname.$itemid.'&tmpl=component" class="'.$sclass['class'].'" '.$sclass['rel'].'>'.JText::_('Subscribe').'</a></li>';       
        }
        if ($context == 'fb') {
        $fbtabs .= '<li><a href="'.$root.'&task=cshare&cid='.$cid.'&cname='.$cname.$itemid.'&tmpl=component" target="_parent">'.JText::_('COM_VIDEOFLOW_SHARE_IT').'</a></li>';       
        } else {
        $fbtabs .= '<li><a href="'.$root.'&task=cshare&cid='.$cid.'&cname='.$cname.$itemid.$wclass['tmpl'].'" class="'.$wclass['class'].'" '.$wclass['rel'].'>'.JText::_('COM_VIDEOFLOW_SHARE_IT').'</a></li>';     
        }
        }
      }      
      $fbtabs .= '</ul>';
  return $fbtabs;
}


function genClass($size, $nclass = null) {
global $vparams;
if ($size == 'wide') {
$w = '780';
$h = '480';
} else {
$w = '600';
$h = '400';
}
$tmpl = '&tmpl="component"';
if ($vparams->lightbox || !JVERS3) {
  $class = 'modal-vflow';
  $rel = 'rel="{handler:\'iframe\', size: {x:600, y:400}}"';
  if ((JVERS3 && $vparams->lightboxsys == 'colorbox')) {
      $class = 'vmodal-vflow';
      $rels = '"data-width="600" data-height="400"';
  } 
} else {
      $class = 'ajax-vflow';
      $rel = '';
      if ($nclass == 'noclass') {
      $class = 'noclass';
      $tmpl = '';
      }
}
$ret = array('class'=>$class, 'rel'=>$rel, 'tmpl'=>$tmpl);
return $ret;
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
  
  function printPagination(){
   if (!empty($this->pagination)) {
	include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
	$p = new videoflowHTMLEXT();
	$p->printPagination($this->pagination);
   } 
  }
  
  function canvasFix()
  {
  global $fxparams;  
  $c = JRequest::getCmd('c');
  if ($c == 'fb') {
  $canvasheight = (int) $fxparams->get('canvasheight');
  if(!empty($canvasheight)) $canvasfix = '{height: "'.$canvasheight.'px"}'; else $canvasfix = 250;
  jimport('joomla.environment.browser');
  $jbrowser =  JBrowser::getInstance();
  $browser = $jbrowser->getBrowser();
  $canvas = '<script>
            window.fbAsyncInit = function() {';
            if ($browser == 'msie' && empty ($canvasheight)) {
            $canvas .= ' FB.Canvas.setAutoResize();';
            }
            $canvas .= ' FB.XFBML.parse();
            FB.Canvas.setSize('.$canvasfix.'); 
            }
            </script>';
  echo $canvas;
  }
  }
    
  function imgResize ($vid, $type)
  {
	include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
	$p = new videoflowHTMLEXT();
	return $p->imgResize($vid, $type);
   }
	     
  function getXparams()
  {
    global $fxparams;
    $context = JRequest::getCmd('c');
    if ($context == 'fb') {
    $xparams = $fxparams;    
    } else {
    $app = JFactory::getApplication();
    $xparams = $app->getParams('com_videoflow');
    }   
    $tmpl = (string) $xparams->get('tmplname');
    if (empty($tmpl)) {
      if (empty($vparams->flowid)) {
        $model = $this->getModel('videoflow');
        $menuid = $model->get('getMenuId'); 
      } else {
      $menuid = $vparams->flowid;
      }
    $menu = JMenu::getInstance('site');
    $mparams = $menu->getParams($menuid);
    if (is_object($mparams)) $xparams = $mparams;
    }
    return $xparams;
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
  
  function loadVFMoo($fw = true){
      if ($fw) {
      if (version_compare(JVERSION, '2.5', 'lt')) {
      JHTML::_('behavior.mootools');
      } else {
      JHTML::_('behavior.framework', true);
      }
      }      
      JHTML::_('behavior.modal', 'a.modal-vflow');
      return "window.addEvent('domready',";
  }
  
  function loadVFJQ($fw = true){
    global $vparams;
    if ($fw) JHtml::_('bootstrap.framework');
    if ($vparams->loadbootstyle) JHtmlBootstrap::loadCss();
    if (!$vparams->lightbox) $this->setNotify();
    return  "jQuery(document).ready(";
  }
    
  function setNotify(){
  $doc = JFactory::getDocument();
  $notify = 'jQuery(document).ready(function(){
  jQuery("a.ajax-vflow").on("click", function(e) {
    e.preventDefault();
    var $this = jQuery("div#vmess");
    var url = jQuery(this).attr( "href" );
    $this.css("display", "block").load (url, function(){$this.delay(4000).fadeOut("slow", function(){$this.empty();});});
    });
  });';
   $doc->addScriptDeclaration($notify); 
  }
  
  function genClose($url = null, $name = null){
    global $vparams;
    $tmpl = JRequest::getVar('tmpl');
    if (!$url) $url = JRoute::_('index.php?option=com_videoflow');
    if (!$name) {
      if ($tmpl == 'component') $name = JText::_('COM_VIDEOFLOW_CLOSE'); else $name = JText::_('COM_VIDEOFLOW_CONTINUE');
    }
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
   
	function dispMenu(){
	if (is_array($this->menu)) {
          include_once(JPATH_COMPONENT_SITE.DS.'html'.DS.'videoflow_htmlext.php');
          $m = new videoflowHTMLEXT();
          $m->menu = $this->menu;
          $m->printMenu();
        } 
    }
  
  function setMedia(){
  global $vparams;
  $doc = JFactory::getDocument();
  $altcontent = '';    
  if (stripos ($this->media->embedcode, 'swfobject.embedSWF') !== FALSE){
    //Load swfobject javascript file
    $swfobject = JURI::root().'components/com_videoflow/jscript/swfobject.js';
    $doc->addScript($swfobject); 
    //Load the player using swfobject
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){".$this->media->embedcode." })");
    $altcontent = '';
    } elseif (stripos ($this->media->embedcode, "jwplayer('vfmediaspace').setup") !== FALSE){
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
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){".$this->media->embedcode." })");
    $altcontent = '';           
    } elseif (stripos ($this->media->embedcode, "YT.Player") !== FALSE) {
    $yt = JURI::root().'components/com_videoflow/jscript/youtube.js';
    $doc->addScript($yt);
    $doc->addScriptDeclaration ($this->loadjsprepend."function(){".$this->media->embedcode." })");
    $altcontent = '';  
    } elseif ($vparams->player == 'ME') {
	$altcontent = $this->media->embedcode;
	if ($vparams->jsframework == 'mootools' || !JVERS3) {
		$doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	}
	$doc->addScript(JURI::root().'components/com_videoflow/players/me/mediaelement-and-player.min.js');
	$doc->addStyleSheet(JURI::root().'components/com_videoflow/players/me/mediaelementplayer.min.css', 'text/css', null, array() );
	$doc->addStyleSheet(JURI::root().'components/com_videoflow/players/me/mejs-skins.css', 'text/css', null, array() ); 
  $vcontrols = '.mejs-container {
  width: 100% !important;
  height: auto !important;
  padding-top: 57%;
  }
  .mejs-overlay, .mejs-poster {
  width: 100% !important;
  height: 100% !important;
  }
  .mejs-mediaelement video {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  width: 100% !important;
  height: 100% !important;
  }
  div.mejs-overlay-button[style]{
  margin-top: -50px !important;
  }
';  
//$doc->addStyleDeclaration($vcontrols);   
	} elseif($vparams->player == 'videojs'){
  $altcontent = $this->media->embedcode;
	$doc->addScript(JURI::root().'components/com_videoflow/players/videojs/video.js');  
	$doc->addStyleSheet(JURI::root().'components/com_videoflow/players/videojs/video-js.min.css', 'text/css', null, array() ); 
  $vcontrols = '.video-js {padding-top:56.25%;}
              .vjs-fullscreen {padding-top: 0px;}
              ';
  
  if (!ISMOBILE && ($this->media->type == "mp3" || $this->media->type == "ogg" || $this->media->type == 'wav')){
  $vcontrols .= '
  .vjs-control-bar{display:block !important;}
  .vjs-big-play-button{display:none !important;}
  ';
  }
  $doc->addStyleDeclaration($vcontrols);     
  if ($this->media->type == 'yt') {
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
    $altcontent = $this->media->embedcode;
  } else {
    //If using the standard embed method, replace alternative content with the player
     $altcontent = $this->media->embedcode;
    }
   
   
   $srcss = '.vfplayerwidth{
            width:100%;
            height:auto;
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
}