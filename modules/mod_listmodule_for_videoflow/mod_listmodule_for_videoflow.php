<?php

// VideoFlow List Module //
/**
* @ Version 1.2.0 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow List Module is free software
* @ Requires VideoFlow Multimedia Component available at http://www.videoflow.tv
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/	

// Prevent direct access

defined('_JEXEC') or die('Access denied.');
defined('DS') or define ('DS', DIRECTORY_SEPARATOR);
if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_videoflow'.DS.'videoflow.php')) {
    JError::raiseError(500, JText::_( 'MOD_VIDEOFLOW_COMP_ERR' ));
    return;
    }
defined ('JVER3') or define ('JVER3', version_compare(JVERSION, '3.0', 'ge'));
// Include module helper file
require_once(dirname(__FILE__).DS.'helper.php');

// Get parameters
$modloc = (string) $params->get ('modloc', 'auto');
if ($modloc == 'auto') {
$c = JRequest::getCmd('c');
if ($c == 'fb') $modloc = 'facebook'; else $modloc = 'joomla';
}
$bootstrap = (int) $params->get('bootstrap', 0);
$vcats = (string) $params->get ('cats', null);
$lightboxsys = (string) $params->get ('lightboxsys', 'colorbox');
$lightboxmode = (int) $params->get ('lightboxmode', 0);
$lboxh = (int) $params->get('lboxh', 60);
$lboxw = (int) $params->get('lboxw', 8);
$moo = (int) $params->get ('loadmoo12', 0);
$internaltitle = (int) $params->get ('autotitle', 1);
$listtype = (string) $params->get('listtype', 'latest');
$listlimit = (int) $params->get('listlimit', 6);
$vfcolumns = (int) $params->get('vfcolumns', 3);
$bcolumns = 12 / $vfcolumns; 
$titlepos = (string) $params->get('titlepos', 'bottom');
$showdesc = (string) $params->get('showdesc', 0);
$desclength = (int) $params->get('desclength', 40);
$thumbwidth = "100%";
$thumbheight = (int) $params->get('thumbheight', '210');
$boxheight = (int) $params->get('boxheight', '');
$boxmaxwidth = (int) $params->get('boxmaxwidth', '');
$titlelength = (int) $params->get('titlelength', 20);
$titlelevel = (string) $params->get('titlelevel', 'h4');
$ltexta = (string) $params->get('ltexta', 'pull-left');
$lbgroundc = (string) $params->get('lbgroundc', '');
$ltextc = (string) $params->get('ltextc', '');
$lborderc = (string) $params->get('lborderc', '');
$lborders = (int) $params->get('lborders', 0);
$borders = $params->get('borders', "");
$ltexts = (string) $params->get('ltexts', '120%');
$seemore = (int) $params->get('seemore', 1);
$stexta = (string) $params->get('stexta', 'pull-right');
$vfl = new ModVideoflowList();
$vparams = $vfl->getVparams();
$ismobile = $vfl->detectMobile();
if (!empty ($ismobile)) $lightboxsys = 'none';
$vboxheight = $vparams->lplayerheight + $lboxh;
$vboxwidth = $vparams->lplayerwidth + $lboxw;
if ($listtype == 'poptoday' || $listtype == 'popthisweek' || $listtype == 'popthismonth') {
if (!JVER3 || version_compare($vparams->version, '1.2.2', 'lt')) {
$listtype = 'popular';
} else {
$seemore = 0;
}
}
if ($listtype == 'playing') $seemore = 0;
$voption = JRequest::getCmd('option');
if ($voption == 'com_videoflow') {
   $vlo = JRequest::getCmd('layout');
   if (!empty($vlo)) $flowid = JRequest::getInt('Itemid');
}

if (empty($flowid) && !empty($vparams->flowid)) $flowid = $vparams->flowid; elseif (empty($flowid) && !empty($vparams->jtemplate)) $flowid = $vfl->getFlowid($vparams->jtemplate);

// Retrieve video data
$mv = new ModVideoflowList;
$mv->task = $listtype;
$mv->limit = $listlimit;
$mv->cats = $vcats;
$mv->vparams = $vparams;
$data = $mv->getData();
if ($vparams->iconplay && ($voption != 'com_videoflow')) $mv->loadCstyle();
if ($internaltitle) $label = $mv->getLabel();
if ($lightboxsys != 'none') $mv->initMbox($lightboxsys, $vparams);
if ($bootstrap && JVER3) {
JHtmlBootstrap::framework(); JHtmlBootstrap::loadCss();
}

//Include display file
require(JModuleHelper::getLayoutPath('mod_listmodule_for_videoflow'));