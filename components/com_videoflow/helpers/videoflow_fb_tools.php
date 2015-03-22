<?php

// VideoFlow - Joomla Multimedia System for Facebook//

/**

* @ Version 1.2.0 

* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com

* @ VideoFlow is free software

* @ Visit http://www.fidsoft.com for support

* @ Kirungi Fred Fideri and Fidsoft accept no liability arising from use of this software 

* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html

**/

// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );

class VideoflowFbTools {
    
    function __construct() {
    include_once (JPATH_COMPONENT_SITE.DS.'fbook'.DS.'facebook.php');
    }
    
    function fbnewsFeed ($p){
    global $vparams; 
    $fbuser = JRequest::getVar('fbuser');
    if (empty($fbuser)) return false;
    $fb = new Facebook(array(
        'appId' => $vparams->appid,
        'secret' => $vparams->fbsecret,
        'allowSignedRequest' => true,
        'cookie' => true
        ));
        try {
        ob_start();
        $res = $fb->api("/$fbuser/feed", "POST", $p);
        ob_end_clean();
        } catch (Exception $ex) {
        }
    } 

    function fbwallPost($media){
    global $vparams;
    $fb = new Facebook(array(
        'appId' => $vparams->appid,
        'secret' => $vparams->fbsecret,
        'allowSignedRequest' => true,
        'cookie' => true
        ));        
    if (!empty($vparams->fb_sesskey) && !empty($vparams->fanpage_id)) {
        $fbuser = JRequest::getVar('fbuser');
        if (!empty($fbuser)) {
             include_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_user_manager.php');
             $fbuserdata = VideoflowUserManager::getVFuserObj($fbuser);
             $userid = $fbuserdata->joomla_id;
             $user = & JFactory::getUser($userid);
             $name = $user->name;
        } else {
            $user = & JFactory::getUser();
            if (!empty($user->id)) {
                $userid = $user->id;
                    if (version_compare(JVERSION, '1.6.0', 'ge')) {
                    $auth = $user->getAuthorisedGroups();
                        if (in_array(8, $auth) || in_array(7, $auth)) $usertype = 'Administrator';
                        } else {
                        $usertype = $user->usertype;    
                    }
                if ($usertype == 'Administrator' || $usertype == 'Super Administrator') {
                $name = JText::_('Admin');
                } else {
                $name = $user->name;
                if (!$vparams->displayname) $name = $user->username;
                }
            } else {
            $userid = 0;    
            $name = JText::_('A user');    
            }
        }
                
        if (!empty($userid)) {
        $mlink = $vparams->canvasurl.'&task=visit&cid='.$userid.'&id='.$media->id.'&vf=1';     
        } else {
        $mlink = $vparams->canvasurl.'&task=play&id='.$media->id.'&vf=1';    
        }
        $clink = $vparams->canvasurl.'&task=visit&cid='.$userid.'&vf=1';
        $p = array(
          'message'=>   $name.' '.JText::_('has uploaded').' '.'"'.$media->title.'"',
          'picture'=>$media->pixlink,
          'link'=>$mlink,
          'description'=>$media->details,
          'source'=>$media->fvc,
          'access_token'=>$vparams->appid.'|'.$vparams->fbsecret,
          'comments_xid'=> 'vf_'.$vparams->mode.'_'.$media->id,
          'properties' => array(JText::_('User Channel') => array(
                              'text' => $name,
                              'href' => $clink),
                              JText::_('Source') =>array (
                              'text' => $vparams->appname,
                              'href' => $vparams->canvasurl)
                              ),
          'action_links' => array(
                          array('text' => JText::_('Tune In'),
                            'href' => $mlink)    
                            )
          );         
    try {
        ob_start();
        $res = $fb->api("/".$vparams->fanpage_id."/feed", "POST", $p);
        ob_end_clean();
        } catch (Exception $ex) {
        }
    }   
    }
}