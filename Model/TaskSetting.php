<?php
/**
 * TaskSettings Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TasksAppModel', 'Tasks.Model');

/**
 * TaskSetting Model
 *
 * @author Yuto Kitatsuji <kitasuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Model
 */
class TaskSetting extends TasksAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Blocks.BlockRolePermission',
	);

/**
 * Get task setting data
 *
 * @param string $taskKey tasks.key
 * @return array
 */
	public function getTaskSetting($taskKey) {
		$conditions = array(
			'task_key' => $taskKey
		);

		$taskSetting = $this->find(
			'first', array(
				'recursive' => -1,
				'conditions' => $conditions,
			)
		);

		return $taskSetting;
	}

/**
 * Save task_setting
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveTaskSetting($data) {
		$this->loadModels([
			'TaskSetting' => 'Tasks.TaskSetting',
		]);

		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			$this->rollback();
			return false;
		}

		try {
			if (! $this->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}
}
