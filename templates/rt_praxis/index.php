<?php
/**
* @version   $Id: index.php 12358 2013-08-01 09:57:28Z arifin $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once(dirname(__FILE__) . '/lib/gantry/gantry.php');
$gantry->init();

// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));

?>
<!doctype html>
<html xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
	<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
	<meta name="viewport" content="width=960px">
<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
	<meta name="viewport" content="width=1200px">
<?php else : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php endif; ?>
<?php
$gantry->displayHead();

$gantry->addStyle('grid-responsive.css', 5);
$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);

if ($gantry->browser->name == 'ie'){
	if ($gantry->browser->shortversion == 9){
		$gantry->addInlineScript("if (typeof RokMediaQueries !== 'undefined') window.addEvent('domready', function(){ RokMediaQueries._fireEvent(RokMediaQueries.getQuery()); });");
	}
	if ($gantry->browser->shortversion == 8){
		$gantry->addScript('html5shim.js');
	}
}
if ($gantry->get('layout-mode', 'responsive') == 'responsive') $gantry->addScript('rokmediaqueries.js');
if ($gantry->get('loadtransition')) {
	$gantry->addScript('load-transition.js');
	$hidden = ' class="rt-hidden"';}

	?>
</head>
<body <?php echo $gantry->displayBodyTag(); ?>>
	<div id="rt-page-surround">
		<?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
		<div id="rt-drawer">
			<div class="rt-container">
				<?php echo $gantry->displayModules('drawer','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Drawer **/ endif; ?>		
		<?php /** Begin Top Surround **/ if ($gantry->countModules('top') or $gantry->countModules('navigation') or $gantry->countModules('header')) : ?>
		<header id="rt-top-surround">
			<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
			<div id="rt-top" <?php echo $gantry->displayClassesByTag('rt-top'); ?>>
				<div class="rt-container">
					<?php echo $gantry->displayModules('top','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Top **/ endif; ?>
			<?php /** Begin Navigation **/ if ($gantry->countModules('navigation')) : ?>
			<div id="rt-navigation">
				<div class="rt-container">
					<?php echo $gantry->displayModules('navigation','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Navigation **/ endif; ?>			
			<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
			<div id="rt-header" class="<?php echo ($gantry->get('header-overlay') == 'light' ? 'rt-light' : 'rt-dark'); ?>">
				<div class="rt-container">
					<?php echo $gantry->displayModules('header','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Header **/ endif; ?>
		</header>
		<?php /** End Top Surround **/ endif; ?>
		<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
		<div id="rt-showcase" class="<?php echo ($gantry->get('showcase-overlay') == 'light' ? 'rt-light' : 'rt-dark'); ?>">
			<div class="rt-showcase-pattern">
				<div class="rt-container">
					<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?php /** End Showcase **/ endif; ?>
		<div id="rt-transition"<?php if ($gantry->get('loadtransition')) echo $hidden; ?>>
			<div id="rt-mainbody-surround">
				<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
				<div id="rt-feature" class="<?php echo ($gantry->get('feature-overlay') == 'light' ? 'rt-light' : 'rt-dark'); ?>">
					<div class="rt-container">
						<?php echo $gantry->displayModules('feature','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Feature **/ endif; ?>
				<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
				<div id="rt-utility" class="<?php echo ($gantry->get('utility-overlay') == 'light' ? 'rt-light' : 'rt-dark'); ?>">
					<div class="rt-container">
						<?php echo $gantry->displayModules('utility','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Utility **/ endif; ?>
				<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
				<div id="rt-breadcrumbs">
					<div class="rt-container">
						<?php echo $gantry->displayModules('breadcrumb','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Breadcrumbs **/ endif; ?>
				<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
				<div id="rt-maintop">
					<div class="rt-container">
						<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Main Top **/ endif; ?>
				<?php /** Begin Main Body **/ ?>
				<div class="rt-container">
					<?php echo $gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
				</div>
				<?php /** End Main Body **/ ?>
				<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
				<div id="rt-mainbottom">
					<div class="rt-container">
						<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Main Bottom **/ endif; ?>
				<?php /** Begin Extension **/ if ($gantry->countModules('extension')) : ?>
				<div id="rt-extension">
					<div class="rt-container">
						<?php echo $gantry->displayModules('extension','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php /** End Extension **/ endif; ?>
			</div>
		</div>
		<?php /** Begin Footer Section **/ if ($gantry->countModules('bottom') or $gantry->countModules('footer')) : ?>
		<footer id="rt-footer-surround" class="<?php echo ($gantry->get('footer-overlay') == 'light' ? 'rt-light' : 'rt-dark'); ?>">
			<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
			<div id="rt-bottom">
				<div class="rt-container">
					<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Bottom **/ endif; ?>			
			<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
			<div id="rt-footer">
				<div class="rt-container">
					<?php echo $gantry->displayModules('footer','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Footer **/ endif; ?>
		</footer>
		<?php /** End Footer Surround **/ endif; ?>
		<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
		<div id="rt-copyright">
			<div class="rt-container">
				<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Copyright **/ endif; ?>		
		<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
		<div id="rt-debug">
			<div class="rt-container">
				<?php echo $gantry->displayModules('debug','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Debug **/ endif; ?>
		<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
		<?php echo $gantry->displayModules('analytics','basic','basic'); ?>
		<?php /** End Analytics **/ endif; ?>
		<?php /** Begin SideModule **/ if ($gantry->get('sidemodule-enabled') and $gantry->countModules('sidemod')) : ?>
		<div id="rt-sidemod" class=<?php echo "rt-".$gantry->get('sidemodule-position'); ?>>
			<div class="rt-sidewrapper">
				<?php echo $gantry->displayModules('sidemod','basic','basic'); ?>
			</div>
			<div class="clear"></div>	
		</div>
		<?php /** End SideModule **/ endif; ?>
		<?php /** Begin Popups **/
		echo $gantry->displayModules('popup','popup','popup');
		echo $gantry->displayModules('login','login','popup');
		/** End Popups **/ ?>					
	</div>
</body>
</html>
<?php
$gantry->finalize();
?>
