<?php

//VideoFlow - Joomla Multimedia System for Facebook//
/**
* @ Version 1.2.2 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no liability arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if (!defined ('DS')) define ('DS', DIRECTORY_SEPARATOR);

jimport( 'joomla.filesystem.file' );

jimport( 'joomla.filesystem.folder' );
  
class com_videoflowInstallerScript
{
    function preflight($route, $adapter) {}
    
    function install($adapter) {
    $this->legacyUpdate();  
    $adapter->getParent()->setRedirectURL('index.php?option=com_videoflow&c=config');
    }
 
    function update($adapter) {}

    function uninstall($adapter) {}
 
    function postflight($route, $adapter)
    {
        if (stripos($route, 'install') !== false || stripos($route, 'update') !== false)
        {
            return $this->fixManifest($adapter);
        }
    }
     
    private function fixManifest($adapter)
    {
        $filesource = $adapter->get('parent')->getPath('source').'/_videoflow.xml';
        $filedest = $adapter->get('parent')->getPath('extension_root').'/videoflow.xml';     
        if (!(JFile::copy($filesource, $filedest)))
        {
            JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_FAIL_COPY_FILE', $filesource, $filedest), JLog::WARNING, 'jerror');
            if (class_exists('JError'))
            {
                JError::raiseWarning(1, 'JInstaller::install: '.JText::sprintf('Failed to copy file to', $filesource, $filedest));
            }
            else
            {
                throw new Exception('JInstaller::install: '.JText::sprintf('Failed to copy file to', $filesource, $filedest));
            }
            return false;
        }
        return true;
    }

  private function legacyUpdate() {
  $success = "VideoFlow installed successfully. Customise your settings on the Configuration Panel.";
  $failure = "Failed to complete VideoFlow installation or upgrade process. Visit the forums at www.fidsoft.com for help.";
  $vf = $this->versionCheck();
  if (empty($vf)) {
  echo '<font color="red">'.$failure.'</font>';
  return;
  }
  $status = $this->versionUpdate($vf);
  if ($status) echo '<font color="green">'.$success.'</font>'; else echo '<font color="red">'.$failure.'</font>';
  return;
}
  
  private function flowDir() {
  global $mainframe;
   if (!defined ('DS')) define ('DS', DIRECTORY_SEPARATOR);
   $vdir = JPATH_ROOT.DS.'videoflow'; 
   $file = $vdir.DS.'index.html'; 
   $success = $this->createMediadir ($vdir, $file);
   if ($success){
   $dest = JPATH_ROOT.DS.'videoflow'.DS.'_thumbs';
   $file = $dest.DS.'index.html'; 
   $this->createMediadir ($dest, $file);
   chmod ($dest, 0775);
   $dest  = JPATH_ROOT.DS.'videoflow'.DS.'videos'; 
   $file = $dest.DS.'index.html'; 
   $this->createMediadir ($dest, $file);
   chmod ($dest, 0775);
   $dest  = JPATH_ROOT.DS.'videoflow'.DS.'audio'; 
   $file = $dest.DS.'index.html'; 
   $this->createMediadir ($dest, $file);
   chmod ($dest, 0775);
   $dest = JPATH_ROOT.DS.'videoflow'.DS.'photos'; 
   $file = $dest.DS.'index.html'; 
   $this->createMediadir ($dest, $file);
   chmod ($dest, 0775);
   chmod ($vdir, 0775);
   return true;
   } else {
   return false;
   }   
}

private function createMediadir($destdir, $file, $source=null){
if (empty($source)) $source = JPATH_ROOT.DS.'components'.DS.'com_videoflow'.DS.'index.html';     
   if (!is_dir($destdir)){
   mkdir ($destdir, 0777);
   }
   if (is_dir($destdir)){
   $success = JFile::copy ($source, $file);
   chmod ($destdir, 0775);
   return $success; 
   } 
   return false;
  }

private function versionCheck(){
  $db = JFactory::getDBO();
  $query = "SELECT * FROM #__vflow_conf";
  $db->setQuery($query);
  $vf = $db->loadObject();
  if(!empty($vf->version)){
  return $vf; 
  } else {
  return false;
  }
}


private function versionUpdate($vf) {
  $status = false;
  // v. 1.1.0 to 1.1.6
  if ($vf->version == '1.1.0') {
  
  // Create media directory if necessary
  $mdir = JPATH_ROOT.DS.'videoflow';  
  if ($vf->mediadir == 'videoflow' && !is_dir($mdir)){
  $status = $this->flowDir();
  if (!$status) echo '<font color="blue">Failed to create "videoflow" directory and subdirectories in joomla root. You must create them manually.</font>';
  }
  // Upgrage to v. 1.2.x
  $status = $this->toV111($vf);
  if ($status){
  $status = $this->toV112($vf);
  }
  if ($status) {
  $status = $this->toV113 ();
  }
  if ($status) {
  $status = $this->toV114 ();  
  }
  if ($status) {
  $status = $this->toV115 ();  
  }
  if ($status) {
  $status = $this->toV116 ();  
  }
  if ($status) {
  $status = $this->toV120();  
  }
  if ($status) $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
  //v. 1.1.1 to 1.2.x
  } elseif($vf->version == '1.1.1'){
  $status = $this->toV112($vf);
  if ($status) {
  $status = $this->toV113();
  }
  if ($status) {
  $status = $this->toV114 ();  
  }
  if ($status) {
  $status = $this->toV115 ();  
  }
  if ($status) {
  $status = $this->toV116 ();  
  }
  if ($status) {
  $status = $this->toV120 ();  
  }
  if ($status) $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
  // v. 1.1.2 to 1.2.x
  } elseif ($vf->version == '1.1.2'){
  $status = $this->toV113();
  if ($status) {
  $status = $this->toV114 ();  
  }
  if ($status) {
  $status = $this->toV115 ();  
  }
  if ($status) {
  $status = $this->toV116 ();  
  }
  if ($status) {
  $status = $this->toV120 ();  
  }
  if ($status) $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
  //v. 1.1.3 to 1.2.x
  } elseif ($vf->version == '1.1.3'){
  $status = $this->toV114 ();
  if ($status) {
  $status = $this->toV115 ();  
  }
  if ($status) {
  $status = $this->toV116 ();  
  }
  if ($status) {
  $status = $this->toV120 ();  
  }
  if ($status) $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
  // v. 1.1.4 to 1.2.x
  } elseif ($vf->version == '1.1.4') {
  $status = $this->toV115();  
  if ($status) {
  $status = $this->toV116 ();  
  }
  if ($status) {
  $status = $this->toV120 ();  
  }
  if ($status) $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
  // v. 1.1.5 to 1.2.x
  } elseif ($vf->version == '1.1.5') {
  $status = $this->toV116();
  if ($status) {
  $status = $this->toV120();  
  }
  if ($status) $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
 // v. 1.1.6 to 1.2.x
  } elseif ($vf->version == '1.1.6') {
  $status = $this->toV120();
  if ($status) $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
  //V. 1.2.0 to 1.2.x
  } elseif ($vf->version == '1.2.0') {
  $status = $this->toV121($vf);
  if ($status) $status = $this->toV122();
 //v. 1.2.1 to 1.2.2
  } elseif ($vf->version == '1.2.1') {
  $status = $this->toV122();
  //Reinstall v. 1.2.2.
  } elseif ($vf->version == '1.2.2') { 
  $status = $this->toV122();
  } 
   $this->versConfig();
   return $status;
  }

private function toV111($vf) {
  
  // Update configuration table - v. 1.1.1
  
  $db = JFactory::getDBO();
        $vf = $db->getTableColumns('#__vflow_conf');
        if (array_key_exists ('commentsyss', $vf)) echo "yest"; else echo "nought";
  
  $db = JFactory::getDBO();
  $vf = $db->getTableColumns ('#__vflow_conf');
  if (!is_array($vf)) $vf = array();
  if (!array_key_exists ('lboxh', $vf)) {
  $query = "ALTER TABLE #__vflow_conf ADD (
            lboxh smallint(6) default NULL,
            lboxw smallint(6) default NULL)";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
			}
  $query = "UPDATE #__vflow_conf SET lboxh = '20', lboxw = '8', version = '1.1.1' WHERE fid = '1'";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 }
  
  // Update categories table - v. 1.1.1
  $vf = $db->getTableColumns ('#__vflow_categories');
  if (!is_array($vf)) $vf = array();
  if (!array_key_exists('date', $vf)) {
    $query = "ALTER TABLE #__vflow_categories ADD (
             date datetime NOT NULL default '0000-00-00 00:00:00'";
     if (!array_key_exists('pixlink', $vf)) {       
         $query .= ", pixlink text";
      }
  $query .= ")";
  }
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
			}
  }
  return true;
} 

private function toV112($vf){
  //Update media directory
   $dest  = JPATH_ROOT.DS.$vf->mediadir.DS.'flash'; 
   $file = $dest.DS.'index.html'; 
   if (!$this->createMediadir ($dest, $file)){
   echo '<font color="blue">Failed to create the subdirectory '.$dest.'. You should create it manually.</font>';
   }   
  $db = JFactory::getDBO();
  
   // Update configuration table
  $vft = $db->getTableColumns('#__vflow_conf');
  if (!is_array($vft)) $vft = array();
  if (!array_key_exists ('upsys', $vft)) {
  $query = "ALTER TABLE #__vflow_conf ADD (
            upsys varchar(150) default NULL,
            fbcommentint varchar(150) default NULL,
            repunderscore tinyint(1) default '1',
            catplay tinyint(1) default '1',
            jshare tinyint(1) default '1',
            fbshare tinyint(1) default '1',
            fbshowuser tinyint(1) default '1',
            fbshowviews tinyint(1) default '1',
            fbshowplaylists tinyint(1) default '1',
            fbshowcategory tinyint(1) default '1',
            fbshowrating tinyint(1) default '1',
            fbshowdate tinyint(1) default '1',
            fbshowmylist tinyint(1) default '1')";
  $db->setQuery($query);
  if (!$db->query()) {
      JError::raiseError( 500, $db->stderr());
			return false;
			}
  $query = "UPDATE #__vflow_conf SET upsys = 'plupload', fbcommentint = 'auto', version = '1.1.2' WHERE fid = '1'";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 }
  }
  // Update categories table - v. 1.1.1
  $vft = $db->getTableColumns('#__vflow_data');
  if (!is_array($vft)) $vft = array();
  if (!array_key_exists('downloads', $vft)) {
    $query = "ALTER TABLE #__vflow_data ADD (
             downloads int(11) default '0')";
   
    $db->setQuery($query);
    if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
			}
   }
   
  $query = "INSERT IGNORE INTO #__vflow_plugins (pid, name, jname, propername, type) VALUES
  (NULL, 'playerview', NULL, 'PlayerView', 'jtemplate'),
  (NULL, 'alphabetic', NULL, 'Alphabetic', 'jmenu'),
  (NULL, 'jomcomment', 'com_jomcomment', 'Jom Comment', 'comments')";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 } 
   return true;
}

private function toV113(){
  $db = JFactory::getDBO();
  $query = 'SELECT joomla_id FROM #__vflow_users';
  $db -> setQuery ($query);    
  $res = $db->loadResultArray();
  if (is_array($res)) {
    foreach ($res as $c) {
      $query = 'SELECT COUNT(*) FROM #__vflow_mychannels WHERE cid='.(int) $c;
      $db->setQuery($query);
      $count = $db->loadResult();
      $query = 'UPDATE #__vflow_users SET subscribers = '.(int) $count.' WHERE joomla_id='.(int) $c;
      $db->setQuery($query);
      $db->query();
    }
  }  
  $query = 'SHOW INDEX FROM #__vflow_data WHERE Column_name = "title"';
  $db->setQuery($query);
  $ind = $db->loadAssoc();
  if ($ind['Index_type'] != 'FULLTEXT') {
  $query = 'ALTER TABLE #__vflow_data ADD FULLTEXT (title, details, tags)';
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 } 
  }  
  $query = "SELECT * FROM #__vflow_plugins WHERE name = 'default' AND type = 'jtemplate'";       
  $db->setQuery($query);      
  $res = $db->loadObject();  
  if (!empty($res)) {
  $query = "UPDATE #__vflow_plugins SET name = 'listview', propername = 'ListView' WHERE name = 'default' AND type = 'jtemplate'";
  $db->setQuery($query);
  $db->query();
  }
  $query = "SELECT * FROM #__vflow_plugins WHERE name = 'grid' AND type = 'jtemplate'";       
  $db->setQuery($query);      
  $res = $db->loadObject();  
  if (!empty($res)) {
  $query = "UPDATE #__vflow_plugins SET propername = 'GridView' WHERE name = 'grid' AND type = 'jtemplate'";
  $db->setQuery($query);
  $db->query();
  }
  $query = "SELECT jtemplate FROM #__vflow_conf WHERE fid = '1'";
  $db->setQuery($query);
  $jtemp = $db->loadResult();
  if ($jtemp == 'default') {
  $query = "UPDATE #__vflow_conf SET version = '1.1.3', jtemplate = 'simple' WHERE fid = '1'";
  } else {
  $query = "UPDATE #__vflow_conf SET version = '1.1.3' WHERE fid = '1'";
  }
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 } 
 return true;
}

private function toV114(){
  $db = JFactory::getDBO();
   // Update configuration table
  $vft = $db->getTableColumns('#__vflow_conf');
  if (!is_array($vft)) $vft = array();
  if (!array_key_exists ('ffmpegpath', $vft)) { 
  $query = "ALTER TABLE #__vflow_conf ADD (
            showuser tinyint(1) default '1',
            showcat tinyint(1) default '1',
            showviews tinyint(1) default '1',
            showrating tinyint(1) default '1',
            showdate tinyint(1) default '1',
            likebutton tinyint(1) default '1',
            showplaylistcount tinyint(1) default '1',
            ffmpegpath varchar(150) default NULL,
            autothumb tinyint(1) default '1',
            ffmpegthumbwidth smallint(6) default '320',
            ffmpegthumbheight smallint(6) default '240',
            ffmpegsec smallint(6) default '10',
            wallposts tinyint(1) default '1',
            bwallposts tinyint(1) default '1',
            slist tinyint(1) default '1',
            showdownloads tinyint(1) default '1',
            showvotes tinyint(1) default '1',
            slistlimit smallint(6) default '5',
            fshowdownloads tinyint(1) default '1',
            fshowvotes tinyint(1) default '1',
            canvasheight smallint(6) default NULL)";
  $db->setQuery($query);
  if (!$db->query()) {
      JError::raiseError( 500, $db->stderr());
			return false;
			}
  
  $query = "UPDATE #__vflow_conf SET ftemplate = 'simple', version = '1.1.4' WHERE fid = '1'";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 }
   
  $query = "INSERT IGNORE INTO #__vflow_plugins (pid, name, jname, propername, type) VALUES
  (NULL, 'simple', NULL, 'SimpleView', 'jtemplate'),
  (NULL, 'simple', NULL, 'SimpleView', 'ftemplate')";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 }
  
  $query = "DELETE FROM #__vflow_plugins WHERE name='dialog'";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
  }
  } 
  if (version_compare(JVERSION, '1.6.0', 'lt')) {               
  $this->fixMenus();
  }
  return true;
} 
  
private function toV115() {
  
  // Update configuration table - v. 1.1.5
  
  $db = JFactory::getDBO();
  $vf = $db->getTableColumns ('#__vflow_conf');
  if (!is_array($vf)) $vf = array();
  if (!array_key_exists ('fb_sesskey', $vf)) {
  
  $query = "ALTER TABLE #__vflow_conf ADD (
            flashhtml5 tinyint(1) default '1',
            fb_sesskey text,
            fanpage_url text,
            fanpage_id int(11),
            deflisting text,
            fbcolorscheme tinytext)";
            
  $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }
  
  $query = "UPDATE #__vflow_conf SET deflisting = 'dateadded desc', fbcolorscheme = 'light' WHERE fid = '1'";
  $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }

  }
  // check ordering
  $vf = $db->getTableColumns ('#__vflow_plugins');
  if (!is_array($vf)) $vf = array();
  if (!array_key_exists ('ordering', $vf)) { 
  //add ordering to plugins, categories and media
  $tarry = array('#__vflow_plugins', '#__vflow_categories', '#__vflow_data');
  foreach ($tarry as $tarry) {
  $query = "ALTER TABLE $tarry ADD (
            ordering int (11) default '0' )";         
  $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }
  }
  
  //add ording menu items
  $query = "INSERT IGNORE INTO #__vflow_plugins (pid, name, jname, propername, type) VALUES (NULL, 'ordered', NULL, 'Ordered', 'jmenu')";
  $db->setQuery($query);
  if (!$db->query()) {
      JError::raiseError( 500, $db->stderr());
      return false;
  }
  
    
  }
  
  if (!$this->fixTables()) return false;
  
  //update version number
  $query = "UPDATE #__vflow_conf SET version = '1.1.5' WHERE fid = '1'";
  $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }
  
  if (version_compare(JVERSION, '1.6.0', 'lt')) $this->fixMenus();
  
  return true;
}

private function toV116()
{
 $db = JFactory::getDBO();
 $query = "UPDATE #__vflow_conf SET version = '1.1.6' WHERE fid = '1'";
 $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }
  if (version_compare(JVERSION, '1.6.0', 'lt')) $this->fixMenus();
  return true;
}

private function toV120()
{
 $this->newFields();
 $db = JFactory::getDBO();
 $query = "UPDATE #__vflow_conf SET version = '1.2.0' WHERE fid = '1'";
 $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  } 
  return true;  
}

private function toV121($vf){
$db = JFactory::getDBO();   
$query = "INSERT IGNORE INTO #__vflow_plugins (pid, name, jname, propername, type) VALUES
  (NULL, 'mp4', NULL, 'MP4', 'vsource'),
  (NULL, 'webm', NULL, 'WebM', 'vsource'),
  (NULL, 'ogv', NULL, 'OGV', 'vsource'),
  (NULL, 'flv', NULL, 'FLV', 'vsource')";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
  } 
$query = "UPDATE #__vflow_plugins set name = 'videojs', propername = 'Video.js' WHERE name = 'nonverblaster'";
$db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  } 
$query = "UPDATE #__vflow_plugins set name = 'ME', propername = 'MediaElement' WHERE name = 'neolao'";
$db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }

$query = "ALTER TABLE #__vflow_conf ADD (
          vsources text, 
          jwplayerurl text, 
          maxplayerwidth smallint(6) default NULL)";   
 
$db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }
$query = "UPDATE #__vflow_conf SET player = 'ME', version = '1.2.1' WHERE fid = '1'";
$db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }  
  $dest  = JPATH_ROOT.DS.$vf->mediadir.DS.'_altvideos'; 
  $file = $dest.DS.'index.html'; 
   if (!$this->createMediadir ($dest, $file, null)){
   echo '<font color="blue">Failed to create the subdirectory '.$dest.'. You should create it manually.</font>';
   }    
 return true;  
}

private function toV122(){ 
$db = JFactory::getDBO();
$pre = $db->getPrefix();
$tables = $db->getTableList();
$autoclean = 0; 
if (!in_array($pre.'vflow_playcount', $tables)) {          
            $query = "CREATE TABLE IF NOT EXISTS #__vflow_playcount (
            id int NOT NULL auto_increment,
            mid int(11),
            playdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
            )";
            $db->setQuery($query);
            if (!$db->query()) {
	             JError::raiseError( 500, $db->stderr());
               return false;
            }
            }
            @$ignore = @$db->query("SET GLOBAL event_scheduler = ON");
            $db->setQuery("show variables like '%event_scheduler%'");
            $es = $db->loadObject();
            if (!empty($es)) {
              if ($es->Variable_name == 'event_scheduler' && $es->Value == 'ON'){
              $autoclean = 1;
              $query =    "CREATE EVENT IF NOT EXISTS vf_autoclean_playcount
                          ON SCHEDULE EVERY 1 DAY
                          STARTS CURRENT_TIMESTAMP
                          DO                          
                          DELETE LOW_PRIORITY IGNORE FROM #__vflow_playcount WHERE playdate < now() - interval 30 day";
              $db->setQuery($query);
                if (!$db->query()) {
	                JError::raiseError( 500, $db->stderr());
                }
              }
            } 
  $query = "SELECT * FROM #__vflow_plugins WHERE type = 'playicon'";
  $db->setQuery($query);
  $chk = $db->loadObject();
  if (empty($chk)) {  
  $query = "INSERT IGNORE INTO #__vflow_plugins (pid, name, jname, propername, type) VALUES
  (NULL, 'play', NULL, 'COM_VIDEOFLOW_PLAYNORM', 'playicon'),
  (NULL, 'play-circled', NULL, 'COM_VIDEOFLOW_PLAYCIRCLED', 'playicon'),
  (NULL, 'play-circled2', NULL, 'COM_VIDEOFLOW_PLAYCIRCLED2', 'playicon'),
  (NULL, 'expand-right', NULL, 'COM_VIDEOFLOW_PLAYBOXED', 'playicon')";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
  }
  } 
$vf = $db->getTableColumns ('#__vflow_conf');
if (!is_array($vf)) $vf = array();
if (!array_key_exists ('downloadfree', $vf)) {
$query = "ALTER TABLE #__vflow_conf ADD( 
   downloadfree tinyint(1) default '0',
   iconplay tinyint(1) default '0',
   playicon text,
   autoclean tinyint(1) default '0'
 )";
  $db->setQuery($query);
  if (!$db->query()) {
	JError::raiseError( 500, $db->stderr());
    return false;
  }
 }  
$query = "UPDATE #__vflow_conf SET version = '1.2.2', playicon = 'play-circled2', autoclean = '".$autoclean."' WHERE fid = '1'";
$db->setQuery($query);
 if (!$db->query()) {
	JError::raiseError( 500, $db->stderr());
    return false;
  }
return true;
} 

private function fixMenus()
{  
  $db = JFactory::getDBO();
  $menu = array('COM_VIDEOFLOW_CONFIGURE_MENU'=>'Configure', 'COM_VIDEOFLOW_MEDIA_MENU'=>'Media', 'COM_VIDEOFLOW_UPGRADE_MENU'=>'Upgrade');
  foreach ($menu as $key=>$val) {
  $query = "UPDATE #__components SET name = '".$val."', admin_menu_alt = '".$val."' WHERE name = '".$key."' AND `option` = 'com_videoflow'"; 
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
                        return false;
		 }
  }
}

private function fixTables()
{
   $db = JFactory::getDBO();
   $query = "ALTER TABLE #__vflow_conf
   CHANGE appid appid BIGINT UNSIGNED NULL DEFAULT NULL ,
   CHANGE profile_id profile_id BIGINT UNSIGNED NULL DEFAULT NULL ,
   CHANGE fanpage_id fanpage_id BIGINT UNSIGNED NULL DEFAULT NULL";
   $db->setQuery($query);
   if (!$db->query()) {
	JError::raiseError( 500, $db->stderr());
          return false;
      }
      
  $query = "ALTER TABLE #__vflow_users
   CHANGE fb_id fb_id BIGINT UNSIGNED NULL DEFAULT NULL";
   $db->setQuery($query);
   if (!$db->query()) {
	JError::raiseError( 500, $db->stderr());
          return false;
      }    
   
  $query = "ALTER TABLE #__vflow_mymedia
   CHANGE faceid faceid BIGINT UNSIGNED NULL DEFAULT NULL";
   $db->setQuery($query);
   if (!$db->query()) {
	JError::raiseError( 500, $db->stderr());
          return false;
      }        
   
   $query = "ALTER TABLE #__vflow_mychannels
   CHANGE faceid faceid BIGINT UNSIGNED NULL DEFAULT NULL";
   $db->setQuery($query);
   if (!$db->query()) {
	JError::raiseError( 500, $db->stderr());
          return false;
      }         
    return true;
} 

private function versConfig(){
 if (version_compare(JVERSION, '3.0.0', 'lt')) {
  $file = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_videoflow'.DS.'videoflow.php';  
  } else {  
  $file = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_videoflow'.DS.'admin.videoflow.php';  
  $legacycontrollers = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_videoflow'.DS.'controllers'.DS.'legacy'; 
  $legacyviews = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_videoflow'.DS.'views'.DS.'legacy';
  if (is_dir($legacycontrollers)) JFolder::delete($legacycontrollers);
  if (is_dir($legacyviews)) JFolder::delete($legacyviews);
  }
  if (is_file($file)) JFile::delete($file);
}

private function newFields() {
  $db = JFactory::getDBO();
  $vf = $db->getTableColumns ('#__vflow_conf');
  if (!is_array($vf)) $vf = array();
  if (!array_key_exists ('jsframework', $vf)) {
  
  $query = "ALTER TABLE #__vflow_conf ADD (
            addthis tinyint(1) default '1',
            addthisid text,
            loadbootstyle tinyint(1) default '0',
            iframecentre tinyint(1) default '1',
            iframecss tinyint(1) default '0',
            jsframework tinytext,
            fbsharecode tinytext,
            twitterbutton tinyint(1) default '1',
            twitterhandle text,
            hashtags text,
            cboxtheme text)";
            
  $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }
  if (version_compare(JVERSION, '3.0', 'lt')) $lboxsys = 'joomlabox'; else $lboxsys = 'colorbox';
  $query = "UPDATE #__vflow_conf SET jsframework = 'auto', fbsharecode = 'iframe', cboxtheme = 'classic', lightboxsys = '".$lboxsys."', lboxh = '30' WHERE fid = '1'";
  $db->setQuery($query);
  if (!$db->query()) {
    JError::raiseError( 500, $db->stderr());
    return false;
  }  
  
  $query = "INSERT IGNORE INTO #__vflow_plugins (pid, name, jname, propername, type) VALUES
  (NULL, 'classic', NULL, 'COM_VIDEOFLOW_CBCLASSIC', 'cboxtheme'),
  (NULL, 'colorbox', NULL, 'COM_VIDEOFLOW_CBOX', 'lightbox'),
  (NULL, 'dark', NULL, 'COM_VIDEOFLOW_CBDARK', 'cboxtheme'),
  (NULL, 'light', NULL, 'COM_VIDEOFLOW_CBLIGHT', 'cboxtheme'),
  (NULL, 'framed', NULL, 'COM_VIDEOFLOW_CBFRAMED', 'cboxtheme'),
  (NULL, 'system', NULL, 'COM_VIDEOFLOW_CBSYSTEM', 'cboxtheme'),
  (NULL, 'auto', NULL, 'COM_VIDEOFLOW_AUTO', 'jsframework'),
  (NULL, 'jquery', NULL, 'COM_VIDEOFLOW_JQUERY', 'jsframework'),
  (NULL, 'mootools', NULL, 'COM_VIDEOFLOW_MOOTOOLS', 'jsframework')";
  $db->setQuery($query);
  if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			return false;
		 } 
  }
}
}