<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.0 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class VideoflowListManager {
  
  function addToList ($id, $myid = null, $media, $res)
  {		
    global $vparams;      
    $db = JFactory::getDBO();
    $fbuser = JRequest::getVar('fbuser');
    if (!$media || !$vparams->vmode ) return $res;
    $query = 'SELECT * FROM #__vflow_mymedia WHERE mid=' .(int) $id .
             ' AND component = "'.$vparams->mode.'" ' .
             ' AND (jid ='. (int) $myid;
    if (!empty($fbuser)){
      $query .= ' OR faceid = '. (int) $fbuser;
    }
    $query .= ')';
    $db->setQuery ($query);
    $vid = $db->loadObject();
    if ($vid) {
    $res->message = '<b>'.$media->title.'</b> '.JText::_('already exists in your personal playlist!');
    return $res;
    }
    if (JVERS3) $date = JFactory::getDate()->toSql(); else $date = JFactory::getDate()->toMySQL(); 
    $query = 'INSERT INTO #__vflow_mymedia (id, jid, faceid, mid, type, component, date)'. 
             ' VALUES ("", "'. (int) $myid.'", "'. $fbuser .'", "'. (int) $id .'", "'. $media->type.'", "'.$vparams->mode.'", "'.$date.'")';
    $db->setQuery($query);
    if (!$db->query()){
    return $res;
    }    
    $res->status = 1;
    $res->message = '<b>'.$media->title.'</b> '.JText::_('has been added to your playlist');
    $res->type = 'message';
    return $res;
  }
		
  function removeFromList($id, $vid, $res)	
  {
    global $vparams;
    $db = & JFactory::getDBO();
    $jid = & JFactory::getUser();
    $fbuser = JRequest::getVar('fbuser');
    if ($jid->guest && !$fbuser) return $res;
    $query = 'DELETE FROM #__vflow_mymedia WHERE mid ='.(int) $id .' AND component = "'.$vparams->mode.'"';
    if (!$jid->guest) $query .= ' AND jid='.(int) $jid->id;
    if ($fbuser) $query .= ' AND faceid='.$fbuser;
    $db->setQuery($query);
    if (!$db->query()){
    return $res;
    } else {
    $res->status = 1;
    $res->message = '<b>'.$vid->title.'</b> '.JText::_('has been removed from your list');
    $res->type = 'message';
    return $res;
    }
  }

  function addCToList ($id, $myid)
  {		
    global $vparams;
    $db = & JFactory::getDBO();
    $res = VideoflowModelVideoflow::runTool('createResp');
    $vrand = mt_rand(100000000001, 999999999999);
    $fbuser = JRequest::getVar('fbuser');
    if (!empty ($fbuser)) $vrand = $fbuser;
    if (JVERS3) $date = JFactory::getDate()->toSql(); else $date = JFactory::getDate()->toMySQL();
    $ccode = & JFactory::getUser($id); 
    if (empty ($ccode) || $ccode->guest || !$vparams->vmode ) return $res;    
    $query = 'SELECT * FROM #__vflow_mychannels WHERE cid =' .(int) $id .
             ' AND (jid ='. (int) $myid . ' OR faceid = '. $vrand.')';
    $db->setQuery ($query);
    $obj = $db->loadObject();
    if ($obj) {
    $res->message = JText::_('You have already subscribed to the channel').' '.'<b>'.$ccode->name.'</b>';
    return $res;
    }
    $query = 'INSERT INTO #__vflow_mychannels (id, jid, faceid, cid, date)'. 
             ' VALUES ("", "'. (int) $myid.'", "'. $fbuser .'", "'. (int) $id .'", "'.$date.'")';
    $db->setQuery($query);
    if (!$db->query()){
    return $res;
    }
    $res->message = '<b>'.$ccode->name.'</b> '.JText::_('has been added to your channel subscription');
    $res->type = 'message';
    $res->status = 1;
    return $res;
  }

  function removeCfromList ($cid)
  {		
    global $vparams;
    $db = & JFactory::getDBO();
    $jid = & JFactory::getUser();
    $fbuser = JRequest::getVar('fbuser');
    $res = VideoflowModelVideoflow::runTool('createResp');
    if ($jid->guest && !$fbuser) return $res;
    if (!$jid->guest) {
      $myid = $jid->id;
      $res->message = $myid;
    } else {
    include_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_user_manager.php');
    $jid = VideoflowUserManager::getVFuserObj($fbuser);
    if (empty($jid)) return $res;
    $myid = $jid->joomla_id;
    }    
    $query = 'DELETE FROM #__vflow_mychannels WHERE cid = '.(int) $cid.' AND jid='.(int) $myid;
    $db->setQuery($query);
              if (!$db->query()){
              return $res;
              } else {
              $res->message = JText::_('You have unsubscribed from the selected channel');
              $res->type = 'message';
              $res->status = 1;
              return $res;
            }
    return $res;
    }  
 }