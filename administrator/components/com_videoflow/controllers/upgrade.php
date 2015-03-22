<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.0
* @ Copyright (C) 2008 - 2012 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class VideoflowControllerUpgrade extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$user = JFactory::getUser();
			if (!$user->authorise('core.admin', 'com_videoflow')) {
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
	}

	function display($cachable = false, $urlparams = false)
	{
		global $vparams;
		require_once(JPATH_COMPONENT.DS.'views'.DS.'config.php');
		if (!empty($vparams->prostatus)) {
     JError::raiseNotice (100, JText::_( 'VideoFlow is already running in pro mode. If you are running the trial version, you can convert it to the full version at any time by completing "STEP 1" below. If you are running the full version but you are experiencing errors, try resetting it on the "Settings" panel under the "System Info" tab. For help, visit <a href="http://www.fidsoft.com" target="_blank">fidsoft.com</a> for support.' ));
    }
		@VideoflowViewConfig::donate();
	}
	
function processpro(){
  global $vparams;
  $error = "ERROR! Upgrade process failed. Please contact fideri@fidsoft.com for help. Quote error code: $vparams->fkey";
  $mestype = 'error';
  $link = JRoute::_('index.php?option=com_videoflow');
  if ($vparams->fkey > 0){
  JExit ("<script> alert('$error VFPRO1'); window.history.go(-1); </script>");
  }
  $h = JURI::getInstance();
  $vsite = base64_encode($h->getHost());
  $udir = JPATH_COMPONENT.'/utilities';
  $ufile = $udir.'/utility.php';
  $vcode = JRequest::getVar ('vcode', mt_rand(100000,999999));
  $ustatus = false;
  $reg_url = "http://www.fidsoft.com/index.php?option=com_fidsoft&task=regpro&vcode=$vcode&vsite=$vsite&version=$vparams->version&format=raw";
  $reg = $this->runTool('readRemote', $reg_url); 
  jimport( 'joomla.filesystem.file' );
  if (!empty($reg)) JFile::write($ufile, $reg); else JExit("<script> alert ('$error VFPRO03'); window.history.go(-1); </script>"); 
  if (file_exists ($ufile) ){
  $status = include_once $ufile;
  if (!$status){
  JExit("<script> alert ('$error VFPRO08'); window.history.go(-1); </script>"); 
  } else {
  $mestype = 'message';
  if (empty($ustatus)) {
  $error = JText::_("Success! Your VideoFlow status has been set to Pro pending confirmation of your subscription. No further action is required on your side. Thank you for upgrading.");
  } else {
  $link = html_entity_decode(JRoute::_('index.php?option=com_videoflow&c=config&vtab=3')); 
  $error = JText::_('Success! Your VideoFlow pro status is confirmed. All pro features are now active. To install pro templates, go to the "Pro Updates" tab of the Settings Panel. Thank you for upgrading.');	
  }
  }  
  JFile::delete($ufile);
  }
  $this->setRedirect( $link, $error, $mestype);
  }

function resetpro(){  
  $vcode = JRequest::getInt('vcode');
  $version = JRequest::getString('version');
  $error = "Unable to reset your pro status. Contact fideri@fidsoft.com for help. Quote error code:";
  $mestype = 'error';
  $link = html_entity_decode(JRoute::_('index.php?option=com_videoflow&c=config')); 
  if (empty($vcode)){
  JExit ("<script> alert('$error RESPRO1'); window.history.go(-1); </script>");
  }
  $h = JURI::getInstance();
  $vsite = base64_encode($h->getHost());
  $udir = JPATH_COMPONENT.'/utilities';
  $ufile = $udir.'/utility.php'; 
  $version = JRequest::getString('version');
  $reg_url = "http://www.fidsoft.com/index.php?option=com_fidsoft&task=reset&vcode=$vcode&vsite=$vsite&version=$version&format=raw";
  $reg = $this->runTool('readRemote', $reg_url); 
  jimport( 'joomla.filesystem.file' );
  if (!empty($reg)) {
  JFile::write($ufile, $reg);
  } 
  if (file_exists ($ufile) ){
  $status = include_once $ufile;
  JFile::delete($ufile);
  }
  if (empty($status)){
  $status = $this->dReset();
  }
  if (!empty($status)) {
  $error = JText::_("Your pro status has been reset. To reactivate, complete STEP 2 on the Upgrade Panel.");
  $mestype = 'message';
  $link = html_entity_decode(JRoute::_('index.php?option=com_videoflow&c=upgrade')); 
  }  
  $this->setRedirect( $link, $error, $mestype);
  }

function dReset(){	
$db = JFactory::getDBO();
$query = "UPDATE #__vflow_conf SET fkey='0', prostatus='0', downloads = '0', vmode='0', showpro='1'";
$db->setQuery($query); 
if (!$db->query()) return false; else return true;
}


function autoupdate(){
  global $vparams;
  jimport( 'joomla.filesystem.file' );
  $mes = JText::_('Auto update failed. Please visit fidsoft.com for manual update instructions. Note that pro templates are only available for the full pro version.');
  $mestype = 'error';
  $link = html_entity_decode(JRoute::_('index.php?option=com_videoflow&c=config&vtab=3')); 
  if (empty ($vparams->vmode)) {
  $this->setRedirect( $link, $mes, $mestype);
  return;
	}
  $type = JRequest::getWord('aname');
  $aid = JRequest::getInt('aid');
  $action = JRequest::getWord('action');
  $h = JURI::getInstance();
  $site = base64_encode($h->getHost());
  if (!$vparams->prostatus){
  $mes = JText::_('Auto update available only with Pro version. Visit fidsoft.com for manual update instructions.');
  $this->setRedirect( $link, $mes, $mestype);
  return;
  } 
  $upd_url = "http://www.fidsoft.com/index.php?option=com_fidsoft&task=autofix&type=$type&aid=$aid&action=$action&vcode=$vparams->fkey&vsite=$site&vmode=$vparams->vmode&version=$vparams->version&format=raw";
  $upd = $this->runTool('readRemote', $upd_url); 
  $udir = JPATH_COMPONENT.'/utilities';
  $ufile = $udir.'/vupdate.php'; 
  if (!empty($upd)) JFile::write($ufile, $upd);
  if (file_exists ($ufile) ){
  $status = include_once $ufile;
  if ($status){
  $mes = JText::_("Your system has been updated.");
  $mestype = 'message'; 
  } 
  JFile::delete($ufile);
  }
  $this->setRedirect( $link, $mes, $mestype);
}


function runTool($func=null, $param1=null, $param2=null, $param3=null, $param4=null)
    {
    include_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_tools.php';
    $tools = new VideoflowTools();
    $tools->func   = $func;
    $tools->param1 = $param1;
    $tools->param2 = $param2;
    $tools->param3 = $param3;
    $tools->param4 = $param4;
    return $tools->runTool();
    }
}