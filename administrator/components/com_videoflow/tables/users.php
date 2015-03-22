<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.1 
* @ Copyright (C) 2008 - 2013 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableUsers extends JTable

{

	var $id 	= null;

	var $joomla_id	= null;

	var $fb_id 	= null;

	var $join_date	= null;

	function __construct( &$_db )
	{
	parent::__construct( '#__vflow_users', 'id', $_db );
		
	$now = JFactory::getDate();
	
	if (version_compare(JVERSION, '1.6.0', 'lt')) {
	
	$this->set( 'join_date', $now->toMySQL() );
		
	} else {
		
	$this->set( 'join_date', $now->toSql() );
	
	}
		
	}

}