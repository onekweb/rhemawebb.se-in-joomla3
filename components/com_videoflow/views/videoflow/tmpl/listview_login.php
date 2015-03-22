<?php

//VideoFlow - Joomla Multimedia System for Facebook//

/**
* @ Version 1.2.1
* @ Copyright (C) 2008 - 2014 Kirungi Fred Fideri at http://www.fidsoft.com
* @ VideoFlow is free software
* @ Visit http://www.fidsoft.com for support
* @ Kirungi Fred Fideri and Fidsoft accept no liability arising from use of this software 
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 
global $vparams;
$xparams = $this->getXparams();
$tmplname = (string) $xparams->get('tmplname', $vparams->jtemplate);
if (empty($tmplname)) $tmplname = 'listview';
$activeborderc = strtolower((string) $xparams->get('activeborderc', '#E3EBFF'));
$inactiveborderc = strtolower((string) $xparams->get('inactiveborderc', '#000000'));
$fontcolor = (string) $xparams->get('fontcolor');
$vfbgcolor = (string) $xparams->get('vfbgcolor');
$iborders = (int) $xparams->get('iborders', 4);
$borders = (int) $xparams->get('borders', 1);
$doc = JFactory::getDocument();
if (file_exists(JPATH_COMPONENT.'/views/videoflow/tmpl/css/'.$tmplname.'.css')) {
$css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/'.$tmplname.'.css';
} else {
$css = JURI::root().'components/com_videoflow/views/videoflow/tmpl/css/listview.css';
}
$doc->addStyleSheet( $css, 'text/css', null, array() );

$iborderc = (string) $xparams->get('iborderc', '#666666');
$bgactive = (string) $xparams->get('bgactive', '#F6F6F6');
$bginactive = (string) $xparams->get('bginactive', '#EDEDED');
 
$css2 = '.vfround, .mod_vflow, .vf_borderc {border-color:'.$iborderc.';}
        .vfmenu_selected, .vf_bgactive {background-color:'.$bgactive.';}
        .vf_bginactive {background-color:'.$bginactive.'}';
$doc->addStyleDeclaration($css2);

$user = JFactory::getUser();
$vtask = JRequest::getCmd('task');
$direct = JRequest::getWord('direct');
$id = JRequest::getInt('id');
$cid = JRequest::getInt('cid');
$lo = $tmplname;
if (!empty($lo)) $lo = '&layout='.$lo; else $lo = '';
$vitemid = JRequest::getInt('Itemid');
if (empty($vitemid) && !empty($vparams->flowid)) $vitemid = $vparams->flowid;
if (!empty($vitemid)) $flowid = '&Itemid='.$vitemid; else $flowid = '';  
if ($id) {
$did = '&id='.$id;
} else if ($cid){
$did = '&cid='.$cid;
} else {
$did = '';
}
$direct = $direct.$did;
$tmpl = JRequest::getWord ('tmpl');
if ($direct && ($tmpl == 'component')) $direct = $direct.'&tmpl=component'; 
$flink = "alert('".JText::_('COM_VIDEOFLOW_FB_LOGGED_IN')."'); self.close(); return false;";
if ($vtask == 'logout') {
$flink = "alert('".JText::_('COM_VIDEOFLOW_FB_LOGGED_OUT')."'); self.close(); return false;";
}
if ($direct) {
$link = base64_encode (JRoute::_(JURI::root().'index.php?option=com_videoflow&task='.$direct.$lo.$flowid));
} else {
$link = base64_encode (JRoute::_(JURI::root().'index.php?option=com_videoflow'.$lo.$flowid));
}
if (version_compare(JVERSION, '1.6.0') < 0) {
	$comuser = 'com_user';
	$login = 'login';
	$pwd = 'passwd';
	$register = 'register';
	$logout = 'logout';
} else {
	$comuser = 'com_users';
	$login = 'user.login';
	$pwd = 'password';
	$register = 'registration';
	$logout = 'user.logout';
}

?>

<!-- Start Joomla login form -->

<?php 
if ($vtask == 'login') {
?>

<div class="row-fluid">
<?php echo $this->dispMenu(); ?>
	<form action="index.php" method="post" name="form-login" id="form-login" >
	<fieldset class="input">
  <legend><?php echo JText::_('COM_VIDEOFLOW_LOGIN');?></legend>
	<label for="username"> <?php echo JText::_('COM_VIDEOFLOW_USERNAME'); ?></label>
	<input name="username" id="username" type="text" alt="username" />
	<label for="<?php echo $pwd;?>"><?php echo JText::_('COM_VIDEOFLOW_PASSWORD'); ?></label>
	<input type="password" name="<?php echo $pwd;?>" id="<?php echo $pwd;?>" alt="password" />
	<label for="remember" class="checkbox">
  <input type="checkbox" name="remember" id="remember" value="yes" alt="<?php echo JText::_('COM_VIDEOFLOW_REMEMBER_ME'); ?>" />
  <?php echo JText::_('COM_VIDEOFLOW_REMEMBER_ME'); ?>
	</label>
	<input type="submit" name="Submit" class="btn" value="<?php echo JText::_('COM_VIDEOFLOW_LOGIN'); ?>" />
	</fieldset>
  <br />
	<ul>
	<li>
	<a href='<?php echo JRoute::_("index.php?option=$comuser&view=reset&tmpl=component"); ?>'>
	<?php echo JText::_('COM_VIDEOFLOW_FORGOT_PWD'); ?>
	</a>
	</li>
	<li>
	<a href='<?php echo JRoute::_("index.php?option=$comuser&view=remind&tmpl=component"); ?>'>
	<?php echo JText::_('COM_VIDEOFLOW_FORGOT_USRNAME'); ?>
	</a>
	</li>
	<li>
	<a href='<?php echo JRoute::_("index.php?option=$comuser&view=$register&tmpl=component"); ?>'>
	<?php echo JText::_('COM_VIDEOFLOW_ACC_CREATE'); ?>
	</a>
	</li>
	</ul>
	
	<input type='hidden' name='option' value='<?php echo $comuser;?>' />
	<input type='hidden' name='task' value='<?php echo $login;?>' />
	<input type='hidden' name='return' value='<?php echo $link; ?>' />
  <?php echo JHTML::_( 'form.token' );?> 
  </form>	 
</div>
<?php
}
?>

<!-- End Joomla login form -->

<!-- Start Joomla Logout Form-->
<?php if 
($vtask == 'logout'){
if (!$user->guest) {
?>
<form action='<?php echo JRoute::_("index.php?option=com_login"); ?>' method='post' name='login' id='login'>
<legend><?php echo JText::_('COM_VIDEOFLOW_LOGOUT');?></legend>
<?php if (!empty($this->name)) echo JText::_('COM_VIDEOFLOW_HI').' '.$this->name.'. '.JText::_('COM_VIDEOFLOW_LOGOUT_SITE').'<br /><br />'; ?>
<input type='submit' name='Submit' class='btn' value='<?php echo JText::_("COM_VIDEOFLOW_LOGOUT"); ?>' />
<br /><br />
<input type='hidden' name='option' value='<?php echo $comuser;?>' />
<input type='hidden' name='task' value='<?php echo $logout; ?>' />
<input type='hidden' name='return' value='<?php echo $link; ?>' />
</form>  
<?php
}
}
?>
<!-- End Joomla logout form -->

<!-- Start Facebook login/logout form -->

<div style="margin:5px;">
  <?php 
  if ($vparams->facebook) {
  $fbout = JRoute::_(JURI::root().'index.php?option=com_videoflow'.$lo.$flowid);
  if (!empty($direct)) {
  $fbin = JRoute::_(JURI::root().'index.php?option=com_videoflow&task='.$direct.'&sess=1'.$lo.$flowid);
  } else if (empty($direct) && $vtask=='login') {
  $fbin = JRoute::_(JURI::root().'index.php?option=com_videoflow&task=logincheck&sess=1'.$lo.$flowid);
  } else {
  $fbin = $fbout;
  }
  $fbuser = JRequest::getVar('fbuser');
  $fbgo = 'isInFB()';
  $fbtext = JText::_('COM_VIDEOFLOW_LOGIN_WITH_FB');
  $btext = JText::_('COM_VIDEOFLOW_FB_LOGIN');
  if ($fbuser) {
  $fbtext = JText::_('COM_VIDEOFLOW_LOGOUT_WITH_FB').'<br/>'; 
  $btext = JText::_('COM_VIDEOFLOW_FB_LOGOUT');
  }
     
   echo '<script type="text/javascript">
	function isInFB(){
	FB.getLoginStatus(function(response) {
        if (response.status === "connected") {
        window.location = "'.$fbin.'";
        } else {
        window.location = "'.$fbout.'";
        }
        });
	}
        </script>';

    echo '<b>'.$fbtext.'</b>';
    echo '<div style="padding: 4px 0px;"><fb:login-button v="2" autologoutlink="true" size="medium" onlogin="isInFB()">'.$btext.'</fb:login-button></div>'; 
    echo '<a href="#" onClick="isInFB();">'.JText::_('COM_VIDEOFLOW_NOTICE_FB_REDIRECT').'</a>';
  }
  ?>
</div>
<!-- End Facebook login/logout form -->
