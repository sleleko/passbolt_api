<?php
/**
 * Insert Avatar Task
 *
 * @copyright    copyright 2012 Passbolt.com
 * @license      http://www.passbolt.com/license
 * @package      app.plugins.DataExtras.Console.Command.Task.AvatarTask
 * @since        version 2.12.11
 */

require_once(APP_DIR . DS  . 'Plugin' . DS . 'DataExtras' . DS . 'Console' . DS . 'Command' . DS . 'Task' . DS . 'ModelTask.php');

App::uses('Profile', 'Model');
App::uses('User', ' Model');
App::uses('Avatar', 'Model');

class AvatarTask extends ModelTask {

	public $model = 'Avatar';

	public function execute() {
		$User = ClassRegistry::init('User');
		$UserTask = $this->Tasks->load('Data.User');
		$users = $UserTask::getAlias();

		// For all users, if an image has been defined insert it as profile avatar.
		foreach ($users as $userId) {
			// Retrieve the user.
			$data = array('User.id' => $userId);
			$o = $User->getFindOptions('User::view', 'admin', $data);
			$users = $User->find('all', $o);
			$user = reset($users);

			// Check if an image exists for him.
			$path = dirname(__FILE__) . DS . 'img' . DS . 'avatar' . DS;
			$matches = array();
			preg_match('/^(.*)@(.*)$/', $user['User']['username'], $matches);
			$fileName = $matches[1] . '.png';

			if (file_exists($path . $fileName)) {
				$data = array(
					'Avatar' => array(
						'file' => array (
							'tmp_name' => $path . $fileName
						)
					)
				);
				if(!$User->Profile->Avatar->upload($user['Profile']['id'], $data)){
					$this->out('Avatar ' . $path . $fileName . ' has not been uploaded');
				}
			}
		}
	}
}