<?php

//VideoFlow - Joomla Multimedia System for Facebook//
/**
* @ Version 1.2.0 
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no liability arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/


defined( '_JEXEC' ) or die( 'Restricted access' );
 
 
class VideoflowRateMedia
{
 
   	function __construct()
	{	
	  global $vparams;
	   $cid = JRequest::getInt('cid', false);
	   $rate = JRequest::getInt ('rating', false);
	   $status_code = $this-> storeVote($cid, $rate); 
	}
   
	function storeVote($media_id, $user_rating) {
	global $vparams;
	$db  = JFactory::getDBO();	
	$error = 0;
	$message = '';
	$fbuser = JRequest::getVar('fbuser');
		
	if ($vparams->only_registered) {
		$user = JFactory::getUser();
		if ($user->guest && !empty($fbuser)) {
		 include_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'videoflow_user_manager.php');
		 $userman = new VideoFlowUserManager;
		 $userobj = $userman->getVFuserObj ($fbuser);
		 if (!empty($userobj->joomla_id)) $user = JFactory::getUser($userobj->joomla_id);
		}
		if ($user->guest) {
			$error = 4; // only logged users can vote
			$message = JText::_('COM_VIDEOFLOW_RATE_LOGIN');
		}
	}
		
		/** RETRIEVING OLD RATING VALUES **/
		if (!$error) {
		$query = 'SELECT *' .
			' FROM #__vflow_rating' .
			' WHERE media_id = '.(int) $media_id;
		$db->setQuery($query);
		$rating = $db->loadObject();
	
		if (!$rating)	{
		$prev_rating_count = 0;
		$prev_rating_sum = 0;
		} else {
		$prev_rating_count = $rating->rating_count;
		$prev_rating_sum = $rating->rating_sum;		
		}
	
		if ($user_rating >= 1 && $user_rating <= 5) {
		$userIP =  $_SERVER['REMOTE_ADDR'];

		if (!$rating) {
			// There are no ratings yet, so lets insert our rating
			$query = 'INSERT INTO #__vflow_rating ( media_id, lastip, rating_sum, rating_count )' .
					' VALUES ( '.(int) $media_id.', '.$db->quote($userIP).', '.(int) $user_rating.', 1 )';
			$db->setQuery($query);
			if (!$db->query()) {
				$error = 1;
				$message = $db->stderr();
			} else {
				$rating_count = 1;
				$rating_sum = $user_rating;
		}
		} else {
			if ($userIP != ($rating->lastip) ) {
				// We weren't the first voter so lets add our vote to the ratings totals
				$query = 'UPDATE #__vflow_rating' .
						' SET rating_count = rating_count + 1, rating_sum = rating_sum + '.(int) $user_rating.', lastip = '.$db->Quote($userIP) .
						' WHERE media_id = '.(int) $media_id;
				$db->setQuery($query);
				if (!$db->query()) {
					$error = 1;
					$message = $db->stderr();
				} else {
					$rating_count = $prev_rating_count + 1;
					$rating_sum = $prev_rating_sum + $user_rating;
				}
			} else {
				$error = 2; // already rated (check with ip address)!
				$message = JText::_('COM_VIDEOFLOW_RATE_ALREADY_VOTED');
			}
		}
    
    //Update VideoFlow media items table as well
		if (!$error){
		$query = 'UPDATE #__vflow_data' .
		' SET rating = '.(int) $rating_sum.', votes = '. (int) $rating_count.
		' WHERE id = '.(int) $media_id;
		$db->setQuery($query);
		if (!$db->query()) {
		$error = 1;
		$message = $db->stderr();
		} else {
		$message = JText::_('COM_VIDEOFLOW_RATE_THANKS');	
		}
		}	
		}
		} else if (!$error) $error = 3;
	
	/** CALCULATE ACTUAL AVERAGE AND STAR WIDTH **/
		$nvotes = "";
		if (!$error) {
		if ($rating_count) $nvotes = $rating_count == 1 ? sprintf(JText::_( 'COM_VIDEOFLOW_VOTEX'), $rating_count) : sprintf(JText::_( 'COM_VIDEOFLOW_VOTESX'), $rating_count);    
		$average = number_format(intval($rating_sum) / intval( $rating_count ),2);
		$width   = $average * 20;
		} else {
		if ($prev_rating_count) $nvotes = $prev_rating_count == 1 ? sprintf(JText::_( 'COM_VIDEOFLOW_VOTEX'), $prev_rating_count) : sprintf(JText::_( 'COM_VIDEOFLOW_VOTESX'), $prev_rating_count);    	
		$average = ($prev_rating_count ? number_format(intval($prev_rating_sum) / intval( $prev_rating_count ),2) : 0 );
		$width   = $average * 20;
		}
		
	/** PRINT OUT RATING RESULTS **/
?>
{
	"success": <?php echo ( $error ? "false" : "true" ); ?>, 
	"return_code": <?php echo $error; ?>,
	"message": "<?php echo addslashes($message); ?>",
	"width": <?php echo ( false ? '""' : '"'.$width.'%"' ); ?>,
	"num_votes": "<?php echo ( $error ? $prev_rating_count : $rating_count ); ?>", 
	"average": "<?php echo ( false ? '""' : $average ); ?>", 
	"out_of": "5",
	"nvotes" : "<?php echo $nvotes; ?>"
}
<?php
}

}