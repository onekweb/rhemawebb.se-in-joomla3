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

defined('_JEXEC') or die('Direct Access to this location is not allowed.');



class ModVideoflowList 
{
    
    var $task = null;
    var $limit = 10;
    var $cats = null;
    var $vparams = null;
    
    function getVparams() 
    {
            if (!empty($this->vparams)) return $this->vparams;
            $db = JFactory::getDBO();         
            $query = 'SELECT * FROM #__vflow_conf';            
            $db->setQuery($query);    
            return $db->loadObject();
    }
    
    
  
    function getData() 
    {
            if ($this->task == "categories") return $this->getCatList();
            $db =  JFactory::getDBO();
            $query = $this-> buildQuery();
            $db->setQuery($query, 0, $this->limit);
            $data = $db->loadObjectList();
            return $data;
    }


  function buildQuery()
	{
       if (($this->task == 'poptoday' || $this->task == 'popthisweek' || $this->task == 'popthismonth') && JVER3) return $this->popQuery();
       $opt = JRequest::getVar('option');
	     $id = JRequest::getInt('id');
       $where = ' WHERE published = "1"';
       if ($this->task == "featured") $where .= ' AND media.recommended="1"';
       if ($this->task == 'playing') $where .= ' AND media.lastclick > NOW() - INTERVAL 60 SECOND';
        if (!empty($this->cats)) {
        $cats = explode(',', $this->cats);
        $where .= ' AND media.cat = ' . implode( ' OR media.cat = ', $cats );
        }
        if ($opt == 'com_videoflow' && (!empty($id))) $where .= ' AND media.id !='.(int) $id;
        $orderby = ' ORDER BY';
        if ($this->task == "popular" || $this->task == 'weeklyview') {
        $orderby .= ' media.views DESC';
        } elseif ($this->task == 'hirated'){
        $orderby .= ' media.rating / media.votes DESC, media.votes DESC';
        } elseif ($this->task == 'random') {
        $orderby .= ' RAND()';
	      } elseif ($this->task == 'order') {
	      $orderby .= ' media.ordering ASC';    
        } elseif ($this->task == 'playing') {
        $orderby .= ' media.lastclick DESC';
        } else {
        $orderby .= ' media.dateadded DESC'; 
        }
        $subquery = ' media.*, u.name, u.username, c.name AS catname'. 
                 ' FROM #__vflow_data AS media' .
                 ' LEFT JOIN #__users AS u ON u.id = media.userid'.
                 ' LEFT JOIN #__vflow_categories AS c ON c.id = media.cat';
        $query = 'SELECT'.$subquery.
			           $where.
			           $orderby;
        return $query;
	}
	    
	function popQuery(){
    $where = ' WHERE published = "1"';
    if (!empty($this->cats)) {
        $cats = explode(',', $this->cats);
        $where .= ' AND cat = ' . implode( ' OR cat = ', $cats );
    }
    if ($this->task == 'poptoday') $p = '1'; elseif ($this->task == 'popthisweek') $p = '7'; else $p = '30';
    
   $query = 'SELECT mid, COUNT(*) AS playcount, v.*, c.name AS catname'.
        ' FROM #__vflow_playcount'.
        ' LEFT JOIN #__vflow_data AS v ON v.id = mid'.
        ' LEFT JOIN #__vflow_categories AS c on c.id = cat'.
        $where.
        ' AND DATEDIFF(CURDATE(), playdate) <= '.
        $p.
        ' GROUP BY mid ORDER BY playcount DESC';
    
    return $query;
	}
     
    
    function getCatList(){
    include_once (JPATH_SITE.DS.'components'.DS.'com_videoflow'.DS.'helpers'.DS.'videoflow_category_manager.php');
        $cm = new VideoflowCategoryManager;
	      $rows = $cm->getCategories();
        if (is_array($rows)){
        $db = JFactory::getDBO();
        foreach ($rows as $row){
        $row->id = $row->catid;
        $row->title = $row->name;
        $row->views = '';
        $row->catname = $row->name;
        $row->dateadded = '';
        $row->type = '';
		    $row->details = $row->desc;
        if (empty($row->pixlink)) {
        $query = 'SELECT pixlink FROM #__vflow_data WHERE cat='.(int) $row->id;
        $db->setQuery( $query );
        $pix = $db->loadResult ();
        } else {
        $pix = $row->pixlink;
        }
        if (!empty($pix)) {
          if (stristr($pix, 'http') === FALSE) {  
          $pix = JURI::root().$this->vparams->mediadir.'/_thumbs/'.$pix;
          } else {   
          $pix = $pix;
          }
        } else {
        $pix = JURI::root().'components/com_videoflow/players/vflow.jpg';
        }
        $row->pixlink = $pix;
        }
      }
    if (count($rows)> $this->limit) {
    $rows = array_slice ($rows, 0, $this->limit);
    }
    return $rows;
    }
    
    function detectMobile() {
     if (JVER3) $web = new JApplicationWebClient; else $web = new JWebClient;
     return $web->mobile;
    }
    
    function getFlowid ($tmpl)
    {
    if (empty($tmpl)) return ''; 
    $query = "SELECT id FROM #__menu WHERE link LIKE '%com_videoflow%' AND link LIKE '%$tmpl%' AND published = '1'";
    $db = JFactory::getDBO();     
    $db -> setQuery($query);
    return $db -> loadResult();
    } 
    
    
    function getLabel ()
    {
        switch($this->task) {
          
          case 'latest':
          default:
          $label = JText::_('VF_LATEST_MEDIA');
          break;
          
          case 'featured':
          $label = JText::_('VF_FEATURED_MEDIA');
          break;
          
          case 'popular':
          $label = JText::_('VF_POPULAR_MEDIA');
          break;
		  
		      case 'poptoday':
          $label = JText::_('VF_MEDIA_POPULAR_TODAY');
          break;
		  
		      case 'popthisweek':
          $label = JText::_('VF_MEDIA_POPULAR_THIS_WEEK');
          break;
		  
		      case 'popthismonth':
          $label = JText::_('VF_MEDIA_POPULAR_THISMONTH');
          break;
		  
          case 'order':
	        $label = JText::_('VF_ORDERED_MEDIA');  
          break;
	  
	        case 'random':
          $label = JText::_('VF_RANDOM_MEDIA');
          break;
          
          case 'hirated':
          $label = JText::_('VF_HIGHLY_RATED_MEDIA');
          break;
          
          case 'weeklyview':
          $label = JText::_('VF_POPULAR_THIS_WEEK');
          break;
          
          case 'categories':
          $label = JText::_('VF_LM_CATEGORIES');
          break;
          
          case 'playing':
          $label = JText::_('VF_BEING_WATCHED_MEDIA');
          break;
        }
        return $label;
	  } 
    
    function imgResize ($media, $type)
	  {
	  global $vparams;
    if (empty($vparams)) $vparams = $this->getVparams(); 
	  include_once(JPATH_SITE.DS.'components'.DS.'com_videoflow'.DS.'html'.DS.'videoflow_htmlext.php');
	  $p = new videoflowHTMLEXT();
	  return $p->imgResize($media, $type);
	  }  
    
    
    function loadCstyle()
    {
    global $vparams;
    if (empty($vparams)) $vparams = $this->getVparams();
    include_once(JPATH_SITE.DS.'components'.DS.'com_videoflow'.DS.'html'.DS.'videoflow_htmlext.php');
    $cs = new videoflowHTMLEXT();
	  $cs->loadCstyle();
    }
    
    function initMbox($sys, $vparams)
    {
      $doc = JFactory::getDocument();
      if ($sys == 'multibox') {
      if (JVER3) JHTML::_('behavior.framework', true); else JHTML::_('behavior.mootools');      
      $css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/multibox.css';
      $doc->addStyleSheet( $css, 'text/css', null, array() );
      if ($vparams->mootools12) $mfile = '_legacy'; else $mfile = '';  
      $overlay = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/overlay'.$mfile.'.js';
      $doc->addScript( $overlay );
      $multibox = JURI::root().'components/com_videoflow/views/videoflow/tmpl/multibox/multibox'.$mfile.'.js';
      $doc->addScript( $multibox );
      } elseif ($sys == 'colorbox') {
      JHtml::_('bootstrap.framework');
      if ($vparams->loadbootstyle) JHtmlBootstrap::loadCss();
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
      $prev = JText::_('VF_PREV');
      $next = JText::_('VF_NEXT');
      $close = JText::_('VF_CLOSE');
      $height = $this->vparams->lplayerheight + $this->vparams->lboxh;
      $width = $this->vparams->lplayerwidth + $this->vparams->lboxw;
      $rand = rand(10000, 19999);
      $clrbox = "
      jQuery(document).ready(function() {
      jQuery('a.vfmod_xmbox').colorbox({rel:'vfmod_mbox".$rand."', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});
      jQuery('a.vfmod_xmboxx').colorbox({rel:'vfmod_mboxx".$rand."', current:'{current}/{total}', previous:'$prev', next:'$next', close:'$close', scrolling:false, iframe:true, $vfprep innerWidth:$width, innerHeight:$height});
      });";
      $doc->addScriptDeclaration($clrbox);    
      } elseif ($sys == 'joomla') {
      if (JVER3) JHTML::_('behavior.framework', true); else JHTML::_('behavior.mootools'); 
      JHTML::_('behavior.modal', 'a.modal-vflow');
      }
   }    
}