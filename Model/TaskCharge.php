<?php
/**
 * TaskCharge Model
 *
 * @author   Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('TasksAppModel', 'Tasks.Model');

/**
 * TaskCharge Model
 *
 * @author   Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Model
 * @property User $User
 */
class TaskCharge extends TasksAppModel {

/**
 * @var int recursiveはデフォルトアソシエーションなしに
 */
	public $recursive = -1;

/**
 * Validate this model
 *
 * @return bool
 */
	public function validateTaskCharge() {
		$this->validates = $this->_getValidateSpecification();
		return $this->validationErrors ? false : true;
	}

/**
 * バリデーションルールを返す
 *
 * @return array
 */
	protected function _getValidateSpecification() {
		$validate = array(
			'task_content_id' => array(
				'naturalNumber' => array(
					'rule' => array('naturalNumber'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'user_id' => array(
				'naturalNumber' => array(
					'rule' => array('naturalNumber'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		);

		return $validate;
	}

/**
 * 担当者に設定したユーザーの存在確認
 *
 * @param int $userId ユーザーID
 * @return bool
 * @throws InternalErrorException
 */
	public function searchChargeUser($userId) {
		// 必要なモデル読み込み
		$this->loadModels([
			'User' => 'Users.User',
		]);
		if (! $this->User->findById($userId)) {
			return false;
		}
		return true;
	}

/**
 * ToDoの担当者を登録
 *
 * @param array $data 登録データ
 * @return bool
 * @throws InternalErrorException
 */
	public function setCharges($data) {
		$taskId = $data['TaskContent']['id'];

		// すべてDelete
		if (! $this->deleteAll(array('TaskCharge.task_content_id' => $taskId), false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		// 1件ずつ保存
		if (isset($data['TaskCharges']) && count($data['TaskCharges']) > 0) {
			foreach ($data['TaskCharges'] as $charge) {
				$charge['TaskCharge']['task_content_id'] = $taskId;
				if (! $this->validateTaskCharge($charge)) {
					return false;
				}
				$this->create($charge);
				if (! $this->save(null, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
		}

		return true;
	}

/**
 * 担当者情報を取得する
 *
 * @param array $taskContent Todo情報
 * @return array
 */
	public function getSelectUsers($taskContent) {
		$this->loadModels(['User' => 'Users.User']);
		$setSelectUsers = [];
		if (isset($taskContent['TaskCharge'])) {
			$selectUserIdArr = [];
			foreach ($taskContent['TaskCharge'] as $item) {
				$selectUserIdArr[] = $item['user_id'];
			}

			// 不要なイベントを発生させないためにBehaviorを除去
			$this->User->Behaviors->unload('Files.Attachment');
			$setSelectUsers = $this->User->find('all', [
				'recursive' => -1,
				'fields' => ['User.id', 'User.handlename'],
				'conditions' => [
					$this->User->alias . '.id' => $selectUserIdArr
				],
			]);
		}
		return $setSelectUsers;
	}

/**
 * 担当者ユーザのハンドル名を絞り込み選択肢として取得
 *
 * @param array $taskContents ToDoListデータ
 * @return array
 */
	public function getSelectChargeUsers($taskContents) {
		$this->loadModels(['User' => 'Users.User']);

		$selectChargeUsers = [];

		// 一覧に表示可能なToDoで担当者として設定されているユーザidを取得(idをkeyに設定し重複を省く)
		$chargeUserIdArr = [];
		foreach ($taskContents as $taskContent) {
			foreach ($taskContent['TaskContents'] as $item) {
				if (isset($item['TaskCharge'])) {
					$tmpIdArr = array_keys($item['TaskCharge']);
					$chargeUserIdArr = array_merge($chargeUserIdArr, $tmpIdArr);
				}
			}
		}
		$chargeUserIdArr = array_unique($chargeUserIdArr);

		if (! empty($chargeUserIdArr)) {
			// 不要なイベントを発生させないためにBehaviorを除去
			$this->User->Behaviors->unload('Files.Attachment');
			$chargeUsers = $this->User->find('all', [
				'recursive' => -1,
				'fields' => ['User.id', 'User.handlename'],
				'conditions' => [
					$this->User->alias . '.id' => $chargeUserIdArr
				],
			]);
			foreach ($chargeUsers as $chargeUser) {
				if (isset($chargeUser['User'])) {
					$selectChargeUsers['TaskContents.charge_user_id_' . $chargeUser['User']['id']] = [
						'label' => $chargeUser['User']['handlename'],
						'user_id' => $chargeUser['User']['id']
					];
				}
			}
		}

		return $selectChargeUsers;
	}
}
