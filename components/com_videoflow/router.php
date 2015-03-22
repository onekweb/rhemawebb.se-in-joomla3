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

if (!defined ('DS')) define ('DS', DIRECTORY_SEPARATOR);

function videoflowBuildRoute( &$query )
{
	   jimport('joomla.filter.output');
     $segments = array();  
      // we must have a category or task in segment 1
       if (isset ($query['task'])) { 
          if ($query['task'] == 'cats' && isset($query['cat'])){
              //This is a category view. Get category name. 
              include_once (dirname(__FILE__).DS.'helpers'.DS.'videoflow_category_manager.php');
              $cm = new VideoFlowCategoryManager;
              $cat = $cm->getCatName((int) $query['cat']);
              if (empty($cat)) return $segments;
              $segments[] = JFilterOutput::stringURLSafe($query['cat'].':'.$cat);
              // segments 0 = category id with appended category name
              unset($query['task']);
              unset($query['cat']); 
        } else {
              $segments[] = $query['task'];
              // segment 0 = task
              unset( $query['task'] ); 
        }
        
        if (isset($query['id'])) {
                $db = JFactory::getDBO();
                $dquery = 'SELECT title FROM #__vflow_data WHERE id=' . (int) $query['id'] . ' AND published="1"';
                $db->setQuery($dquery);
                $title = $db->loadResult();  
                // segment 1 = id with appended title   
                $segments[]= JFilterOutput::stringURLSafe($query['id'].':'.$title);
                unset($query['id']);
              }
       }
       if(isset($query['layout']) && $query['layout'] != 'lightbox')
       {
                unset( $query['layout'] );
       };
       
       if(isset($query['sl']))
       {
                unset( $query['sl'] );
       };
                    
       //Returns 1 (category or task) or 2 (category + id or task + id) segments
       return $segments;
       
}

function videoflowParseRoute( $segments )
{  
	   $vars = array();
     $count = count($segments);
        //Could be a category or a task. Check!
        if (strpos($segments[0], ':') === false) {
        //it is a task
        $vars['task'] = $segments[0];
        } else {
        //It is a category
        $vars['task'] = 'cats';
        list ($catid, $cat) = explode(':', $segments[0], 2);
        $vars['cat'] = (int) $catid;
        }
        //Do we have more?
        if ($count == 2) {
        //segment 2 contains an id
        list($id, $title) = explode (':', $segments[1], 2);
        $vars['id'] = (int) $id;
        }   
    return $vars;
}