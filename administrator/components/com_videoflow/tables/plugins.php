<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.1.5 
* @ Copyright (C) 2008 - 2012 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no responsibility arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TablePlugins extends JTable {
	var $pid		= null;
	var $name    		= null;
	var $jname     		= null;
	var $propername     	= null;
	var $type       	= null;
	var $ordering		= null;
	
function __construct( &$_db )
	{
	parent::__construct( '#__vflow_plugins', 'pid', $_db );
		
	}
}