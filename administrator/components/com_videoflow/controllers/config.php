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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
  
class VideoflowControllerConfig extends JControllerLegacy
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
{
	if (version_compare(JVERSION, '3.0.0', 'lt')) VideoflowUtilities::setVideoflowTitle ('Settings', 'vflow.png');
	
	parent::__construct( $config );
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$user = JFactory::getUser();
			if (!$user->authorise('core.admin', 'com_videoflow')) {
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}	
	$this->registerTask( 'apply', 'save' );
	$this->checkUpdates();
}

	/**
	 * Display media list
	 */
	 
function display($cachable = false, $urlparams = false)
{
	global $vparams;
	
	$row = $vparams;
	
	require_once(JPATH_COMPONENT.DS.'views'.DS.'config.php');
	
	$row->msg = $this->getMsg($row);
	$row->selectsys = $this->pluginSelect('CMS');    
  $row->selectplayer = $this->pluginSelect('player');
  $row->selectjtemp = $this->pluginSelect('jtemplate');
  $row->selectftemp = $this->pluginSelect('ftemplate');
  $row->selectpicon = $this->prepTranslate($this->pluginSelect('playicon'));
  $row->tcolour = $this->prepTranslate($this->pluginSelect('toolcolour'));
  $row->selectlbox = $this->prepTranslate($this->pluginSelect('lightbox'));
  $row->selectjs = $this->prepTranslate($this->pluginSelect('jsframework'));
  $row->selectcbtheme = $this->prepTranslate($this->pluginSelect('cboxtheme'));
	$row->ffmpegdetected = $this->findFFMPEG();
  $activemenu = explode('|', $row->menu);
  $activefbmenu = explode('|', $row->fbmenu);
  $row->jmenu = $this->genSelectBox($this->pluginSelect('jmenu'), $activemenu, 'menu');
  $row->fmenu = $this->genSelectBox($this->pluginSelect('fbmenu'), $activefbmenu, 'fbmenu');
  
  $activevsources = explode('|', $row->vsources);
  $row->vsources = $this->genSelectBox($this->pluginSelect('vsource'), $activevsources, 'vsources');

  
  $selectcomsys = $this->pluginSelect('comments');
	$none = new stdClass();
	$none->value = '';
	$none->text = JText::_('None');
	$row->selectcomsys = array_merge($selectcomsys, array($none));
	$row->findmods = $this->genSelect(array ('0'=>JText::_('Normal'), '1'=>JText::_('Show module positions')));
	$row->selectname = $this->genSelect(array ('0'=>JText::_('Username'), '1'=>JText::_('Full name')));
	$row->lboxmode = $this->genSelect(array ('0'=>JText::_('Dual'), '1'=>JText::_('Full')));
	$row->fbcommintselect = $this->genSelect(array ('auto'=>JText::_('Automatic'), 'none'=>JText::_('None')));
	$row->upsysselect = $this->genSelect(array ('plupload'=>JText::_('Plupload'), 'swfupload'=>JText::_('SWFUpload')));
	$row->bselect = $this->genSelect(array ('0'=>JText::_('No'), '1'=>JText::_('Yes')));
	$row->catmode = $this->genSelect(array ('0'=>JText::_('Media list'), '1'=>JText::_('Media play')));
	$row->deflistingselect = $this->genSelect(array ('dateadded desc'=>JText::_('Newest'), 'dateadded asc'=>JText::_('Oldest'), 'ordering asc'=>JText::_('Order ascending'), 'ordering desc'=>JText::_('Order descending')));
  $row->selectfbcode = $this->genSelect(array ('iframe'=>JText::_('COM_VIDEOFLOW_IFRAME'), 'xfbml'=>JText::_('COM_VIDEOFLOW_XFBML'), 'html5'=>JText::_('COM_VIDEOFLOW_HTML5')));
  $row->selectplayicon = $this->genSelect(array ('play'=>JText::_('COM_VIDEOFLOW_PLAYNORM'), 'play-circled'=>JText::_('COM_VIDEOFLOW_PLAYCIRCLED'), 'play-circled2'=>JText::_('COM_VIDEOFLOW_PLAYCIRCLED2'), 'expand-right'=>JText::_('COM_VIDEOFLOW_PLAYBOXED')));
  $row->vcredit = $this->getCredits();
	$row->proadds = $this-> selectProadds();
  $view = new VideoflowViewConfig();
  $view->listSettings($row);
	} 

function mOrder(){
	global $vparams;
	if ($vparams->deflisting == 'ordering asc') $deflisting = 'asc'; else $deflisting = 'desc';
	$m = JRequest::getVar('m', 'jmenu');
	$app = JFactory::getApplication();
   	$context		= 'com_videoflow.menu.';
	$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',	'filter_order',	'm.propername', 'cmd' );
	$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', $deflisting, 'word' );
	$limit			= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
	$limitstart 		= $app->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );      
	if (stripos($filter_order, 'm.') === false ) $filter_order = 'm.propername';
	$orderby		= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', m.ordering';
	$db = JFactory::getDBO();


    // Get the total number of records
	$query = "SELECT COUNT(*)"
		. " FROM #__vflow_plugins AS m"
		. " WHERE m.type = '". (string) $m ."'";
	$db->setQuery( $query );
	$total = $db->loadResult();
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );
	$query = "SELECT m.*"
		. " FROM #__vflow_plugins AS m"
		. " WHERE m.type= '". (string) $m ."'"
		.$orderby 
		;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();

	// Table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
	
	$order = $app->input->get('filter_order');
	
	if ($order !="m.ordering") $app->setUserState( "com_videoflow.menu.filter_order", "m.ordering" );
	
	require_once(JPATH_COMPONENT.DS.'views'.DS.'config.php');
	VideoflowViewConfig::listMenu ( $rows, $pageNav, $lists );	
}

	
function genSelect($items)
{
    foreach ($items as $value=>$text) {
    $class = new stdClass();
    $class->value = $value;
    $class->text = $text;
    $class->disable = null;
    $sel[] = $class; 
    }
    return $sel;
}
  
  function pluginSelect($type) {
    $db = JFactory::getDBO();
    $order = '';
    if ($type == 'jmenu' || $type == 'fbmenu') $order = 'ORDER BY ordering ASC';  
		$query = 'SELECT name AS value, propername AS text FROM #__vflow_plugins WHERE type = "'.$type.'" '.$order;
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
  function selectProadds(){
    $db = JFactory::getDBO();
		$query = 'SELECT * FROM #__vflow_addons';
    $db->setQuery($query);
    return $db->loadObjectList();
  }
  
  
  function getActiveMenu ($list) {
    $db = JFactory::getDBO();
		$query = 'SELECT name AS value, propername AS text FROM #__vflow_plugins WHERE type = "jmenu" AND name = ' . implode (' OR name = ', $list);
    $db->setQuery($query);
    return $db->loadObjectList();
  }
    
  function getMsg ($row){
  global $vparams;
  $message = '';
  if ($vparams->message) {
    $vsite = urlencode(JURI::root());
    $url = "http://www.fidsoft.com/index.php?option=com_fidsoft&task=news&vcode=$row->fkey&vmode=$row->vmode&vsite=$vsite&version=$row->version&format=raw";
    $message = $this->runTool('readRemote', $url);
    }
    if (empty($message)) {
    $message = 'There are no new messages at this time.';
    }
    return $message;
  }
  
  function getCredits()
  {
  $tac = JRoute::_('index.php?option=com_videoflow&c=config&task=terms&format=raw');
  $vcredit = 
<<<VCREDITS
   <a href="http://www.videoflow.tv" target"_blank">VideoFlow</a> is a free Joomla multimedia component that integrates seamlessly with Facebook, taking your Joomla content to Facebook and bringing Facebook social networking features to your Joomla site. 
   It is distributed under the GNU/GPL License.
   <br />
   <br />
   VideoFlow software is written by <a href="mailto:fideri@fidsoft.com"> Kirungi F. Fideri</a> at <a href="http://www.fidsoft.com" target"_blank">fidsoft.com</a>. It includes the following third-party software or content in original or modified form:
   <ul>
   <li><a href="http://nonverbla.de/blog/2008/09/15/nonverblasterhover/" target"_blank">NonverBlaster</a> flash media player by <a href="http://www.nonverbla.de/contact.html" target="_blank">Rasso Hilber</a> </li>
   <li><a href="http://flv-player.net/" target"_blank">Neolao</a> flash media player.</li>
   <li><a href="http://phatfusion.net/multibox/" target"_blank">MultiBox</a> lightbox system by <a href="http://www.samuelbirch.com" target"_blank">Samuel Birch</a>.</li>
   <li><a href="http://www.joomitaly.com" target"_blank">VOTItaly</a> rating system.</li>
   <li><a href="http://www.zkara.net" target"_blank">Thumbnail browser class </a>by Boutekedjiret Zoheir Ramzi</li>
   <li><a href="http://swfupload.org/" target"_blank">SWFUpload </a>flash uploader</li>
   <li><a href="http://plupload.org/" target"_blank">Plupload </a>multi-platform uploader</li>
   <li><a href="http://somerandomdude.com/projects/sanscons/" target"_blank">Sanscons</a> graphic icons by P.J. Onori</li>
   </ul> 
   Installation and/or use of this software constitutes acceptance of <a href="$tac" class="modal-vfpop" rel="{handler: 'iframe', size: {x: 725, y: 520}}">terms and conditions</a>.
   <br />
   <br />
   For more information and support, visit <a href="http://www.fidsoft.com" target="_blank">fidsoft.com</a>.
VCREDITS;
  return $vcredit;
  }

  function terms(){
PRINT <<<CONDITIONS
  VIDEOFLOW SOFTWARE, ALL THE FILES CONTAINED IN THE VIDEOFLOW 
  DISTRIBUTION PACKAGE, AND ALL UPDATES ARE PROVIDED BY THE AUTHOR(S) "AS IS" 
  AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
  IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
  ARE DISCLAIMED.  
  <BR/><BR/>
  IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,         
  INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT 
  NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
  DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY    
  THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
  THIS SOFTWARE AND ASSOCIATED FILES, EVEN IF ADVISED OF THE POSSIBILITY 
  OF SUCH DAMAGE.
  <BR/><BR/>
  INSTALLATION AND/OR USE OF THIS SOFTWARE CONSTITUTES ACCEPTANCE OF THE ABOVE
  TERMS AND CONDITIONS.
  <BR/><BR/>
CONDITIONS;
  }
  
  
  function prepTranslate($items) {
  foreach ($items as $item){
    $item->text = JText::_($item->text);
    $trans[] = $item;
  }
  return $trans;
  }

 function genSelectBox($items, $ckd = null, $name){
   if (!is_array($ckd)) $ckd = array($ckd);
   $sel = '';
   foreach ($items as $item) {
    if (in_array($item->value, $ckd)) $checked = 'checked'; else $checked = '';
    $sel .= '<div style="float:left; margin-right:10px;"><input type="checkbox" name="'.$name.'[]" value="'.$item->value.'" '.$checked.' />&nbsp;&nbsp;' .JText::_($item->text).'</div>';  
    }
  return $sel;
  }
  
  function save()
	{
	JRequest::checkToken() or jexit( 'Invalid Token' );
	$post	= JRequest::get( 'post' );
  $menu = JRequest::getVar('menu');
	$fbmenu = JRequest::getVar('fbmenu');
  $vsources = JRequest::getVar('vsources'); 
  if (!empty($menu)) $post['menu'] = implode ('|', $menu); else $post['menu'] = '';
  if (!empty($fbmenu)) $post ['fbmenu'] = implode ('|', $fbmenu); else $post['fbmenu'] = '';
  if (!empty($vsources)) $post ['vsources'] = implode ('|', $vsources); else $post['vsources'] = '';
	$vtab= JRequest::getInt('vtab', 0);
  $row= JTable::getInstance('Config', 'Table');
      if (!$row -> bind($post)) {
        return JError::raiseWarning(500, $row->getError());
      }
      if (!$row -> store()) {
         return JError::raiseWarning(500, $row->getError());
      }
    $message = JText::_('Settings saved.');  
    $link = 'index.php?option=com_videoflow&task=display&c=config&vtab='.$vtab;
    $this->setRedirect( $link, $message);    
	}
	
function findFFMPEG() {
 include_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_tools.php';
 $ffm = new VideoflowTools();
 $fpath = $ffm->runExternal('type -P ffmpeg', $code);
 return $fpath;	
}

function checkUpdates(){
  global $vparams;
  if(!$vparams->message) return;
  jimport( 'joomla.filesystem.file' );
  $h = JURI::getInstance();
  $site = base64_encode($h->getHost());
  $upd_url = "http://www.fidsoft.com/index.php?option=com_fidsoft&task=vcheck&vsite=$site&version=$vparams->version&format=raw";
  $upd = $this->runTool('readRemote', $upd_url); 
  $udir = JPATH_COMPONENT.'/utilities';
  $ufile = $udir.'/xdate.php'; 
  if (!empty($upd)) JFile::write($ufile, $upd);
  if (file_exists ($ufile) ){
  $status = include_once $ufile;
  JFile::delete($ufile);
  }
}

  function gentok(){
      global $vparams;
      $message = 'COM_VIDEOFLOW_FB_SESSGEN_ERROR';
      $mtype = 'error';
      $session = JFactory::getSession();
      if (version_compare(JVERSION, '2.5.0', 'lt')) {
      jimport( 'joomla.utilities.utility' );
    //  JRequest::checkToken('get') or JExit( 'Invalid Token' );
      $sess = JUtility::getToken();
      } else {
      //$session->checkToken() or JExit('Invalid Token');
      $sess = $session->getName().'='.$session->getId().'&'.$session->getFormToken();
      }
      $code = JRequest::getString('code');
      if (empty($code)) {
	return JError::raiseWarning(403, JText::_('COM_VIDEOFLOW_FB_NO_CODE'));
      }
      $redir = urlencode(JURI::current().'?option=com_videoflow&c=config&task=gentok&'.$sess.'=1&fk=1');
      $tokenurl = "https://graph.facebook.com/oauth/access_token?client_id=".$vparams->fbkey."&client_secret=".$vparams->fbsecret."&code=".$code."&redirect_uri=".$redir;
      $token = $this->runTool('readRemote', $tokenurl);
      if (!empty($token)) parse_str ($token);
	if(!empty($access_token) && is_string($access_token)) {
	$paccess_token = "https://graph.facebook.com/".$vparams->fanpage_id."?fields=access_token&access_token=".$access_token;	
	$paccess_token = $this->runTool('readRemote', $paccess_token);
	if (!empty($paccess_token)) {
		$paccess_token = json_decode ($paccess_token, true);
		if (!empty($paccess_token['access_token'])) {
		$db = JFactory::getDBO();	
		$query = "UPDATE #__vflow_conf SET fb_sesskey = ".$db->quote( $db->getEscaped( $paccess_token['access_token']), false );
		$db->setQuery($query);
			if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			} else {
			$message = "COM_VIDEOFLOW_FB_CODE_SET";
			$mtype = 'message';
			}
		}		
	}   
	}
	$link = 'index.php?option=com_videoflow&task=display&c=config&vtab=2';
        $this->setRedirect( $link, JText::_($message), $mtype);
  }
  
  
  function findurl() {
      global $vparams;
      $mtype = 'error';
      $message = 'COM_VIDEOFLOW_FB_FINDURL_ERROR';
      if (empty($vparams->fanpage_id)) {
	$message = "COM_VIDEOFLOW_FANPAGEID_REQUIRED";
      } else {
      $fbquery = "https://graph.facebook.com/".$vparams->fanpage_id;	
	$res = $this->runTool('readRemote', $fbquery);
	if (!empty($res)) {
		$res = json_decode ($res, true);
		if (!empty($res['link'])) {
		$db = JFactory::getDBO();	
		$query = "UPDATE #__vflow_conf SET fanpage_url = ".$db->quote( $db->getEscaped( $res['link']), false );
		$db->setQuery($query);
			if (!$db->query()) {
			JError::raiseError( 500, $db->stderr());
			} else {
			$message = 'COM_VIDEOFLOW_FB_URL_SET';
			$mtype = 'message';
			}
		}		
	}   
      }
	$link = 'index.php?option=com_videoflow&task=display&c=config&vtab=2';
        $this->setRedirect( $link, JText::_($message), $mtype);
  }
  
  function saveOrder()
	{
		$m = JRequest::getVar('m');
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_videoflow&c=config&task=morder&tmpl=component&m='.$m );
					
		// Initialize variables
		$db		= JFactory::getDBO();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array(), 'post', 'array' );
		$row		= JTable::getInstance('Plugins', 'Table');
		$total		= count( $cid );
		$conditions	= array();

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
				//remember to reorder this type
				$condition = "type= '$row->type'";	
				$found = false;
				foreach ($conditions as $cond) {
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}
				if (!$found) {
					$conditions[] = array ( $row->pid, $condition );
				}
				
			}
		}

		// execute reorder for each type

		foreach ($conditions as $cond)
		{
			$row->load( $cond[0] );
			$row->reorder( $cond[1] );
		}
		

		// Clear the component's cache
		$cache = JFactory::getCache('com_videoflow');
		$cache->clean();
		$message = 'COM_VIDEOFLOW_ORDER_SET';
		$this->setMessage( JText::_($message) );
	}
	
  	function morderup() {
		$app = JFactory::getApplication();
		$m = $app->input->get('m');
		$order = $app->input->get('filter_order');
		$dir = $app->input->get('filter_order_Dir');
		if ($order == 'm.ordering') {
			if ($dir == 'asc') $this->changeOrder(-1); elseif ($dir == 'desc') $this->changeOrder(1);
		}
		else {
			JError::raiseWarning ( 500, "<a href='index.php?option=com_videoflow&c=config&task=setstate&m=$m'>".JText::_('COM_VIDEOFLOW_ORDERCOLUMN')."</a>" );
			$this->setRedirect('index.php?option=com_videoflow&c=config&task=morder&m='.$m);	
		}
	}
	
	function morderdown() {
		$app = JFactory::getApplication();
		$order = $app->input->get('filter_order');
		$m = $app->input->get('m');
		$dir = $app->input->get('filter_order_Dir');
		if ($order == 'm.ordering') {
			if ($dir == 'asc') $this->changeOrder(1); elseif ($dir == 'desc') $this->changeOrder(-1);
		} else {
			JError::raiseWarning ( 500, "<a href='index.php?option=com_videoflow&c=config&task=setstate'>".JText::_('COM_VIDEOFLOW_ORDERCOLUMN')."</a>" );
			$this->setRedirect('index.php?option=com_videoflow&c=config&task=morder&m='.$m);	
		}
	}
	
	function changeOrder($dir) {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$m = JRequest::getVar('m');
		 $cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		 $order = JRequest::getVar('filter_order');
		 if ($order == 'm.ordering') {
		 $row = JTable::getInstance('Plugins', 'Table');
		 $row->load($cid[0]);
		 $row->move($dir, "type = '".$row->type."'");
		 $row->reorder("type = '".$row->type."'");
		 $this->setRedirect( 'index.php?option=com_videoflow&c=config&task=morder&m='.$m );
		 } 
		 $cache =JFactory::getCache('com_videoflow');
		 $cache->clean();
		 $this->setMessage( JText::_('COM_VIDEOFLOW_ORDER_SAVED') );
	}
	
	function setstate(){
		$app = JFactory::getApplication();
		$m = JRequest::getVar('m');
		$redir = 'index.php?option=com_videoflow&c=config&task=morder&m='.$m;		
		$app->setUserState( "com_videoflow.menu.filter_order", "m.ordering" );
		$this->setRedirect($redir);
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
