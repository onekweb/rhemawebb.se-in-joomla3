<?php
/**
 * @version   $Id: Joomla25StorageService.php 9276 2013-04-11 17:47:57Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * Class RokUpdater_Joomla25StorageService
 */
class RokUpdater_Joomla25StorageService extends RokUpdater_AbstractStorageService
{
	/**
	 *
	 */
	public function forceUpdatesRefresh()
	{
		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');
		/** @var $model InstallerModelUpdate */
		$model = JModel::getInstance('update', 'InstallerModel');
		$model->purge();
		@$model->findUpdates();
	}
}
