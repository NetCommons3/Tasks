<?php
/**
 * TaskContentEdit Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TasksAppController', 'Tasks.Controller');

/**
 * TaskContentEdit Controller
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @package NetCommons\Tasks\Controller
 * @property TaskContent $TaskContent
 * @property TaskCharge $TaskCharge
 */
class TaskContentEditController extends TasksAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Tasks.TaskContent',
		'Tasks.TaskCharge',
		'Mails.MailSetting',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'add,edit,delete' => 'content_creatable',
			),
		),
		'Categories.Categories',
		'NetCommons.NetCommonsTime',
	);

/**
 * beforeFilters
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Workflow.Workflow',
		'NetCommons.NetCommonsForm',
		'Categories.Category',
		'Users.DisplayUser',
		'Users.UserSearch',
		'Groups.GroupUserList',
	);

/**
 * index add
 *
 * @return void
 */
	public function add() {
		$this->_prepare();
		$this->set('listTitle', $this->_taskTitle);

		if ($this->request->is('post')) {
			$data = $this->request->data;

			$data['TaskContent']['task_key'] = $this->_taskSetting['TaskSetting']['task_key'];

			// set status
			$status = $this->Workflow->parseStatus();
			$data['TaskContent']['status'] = $status;

			// set block_id
			$data['TaskContent']['block_id'] = Current::read('Block.id');
			// set language_id
			$data['TaskContent']['language_id'] = Current::read('Language.id');

			// set task_end_date
			$data = $this->__setTaskDate($data);
			if (! $data['TaskContent']['is_enable_mail']) {
				unset($data['TaskContent']['email_send_timing']);
			} else {
				$data['is_reminder'] = true;
			}

			if (($result = $this->TaskContent->saveContent($data))) {
				$url = NetCommonsUrl::actionUrl(array(
					'controller' => 'task_contents',
					'action' => 'view',
					'frame_id' => Current::read('Frame.id'),
					'block_id' => Current::read('Block.id'),
					'key' => $result['TaskContent']['key'],
					'is_makeReminder' => $result['is_makeReminder']
				));

				return $this->redirect($url);
			}
			// ToDo担当者ユーザー保持
			$this->request->data = $this->TaskCharge->getSelectUsers($this->request->data, false);

			$this->NetCommons->handleValidationError($this->TaskContent->validationErrors);

		} else {
			$this->request->data = Hash::merge($this->request->data, $this->TaskContent->create());
			$this->request->data = $this->TaskCharge->getSelectUsers($this->request->data, true);
		}
		$this->request->data = $this->NetCommonsTime->toUserDatetimeArray(
			$this->request->data,
			array(
				'TaskContent.task_start_date',
				'TaskContent.task_end_date',
			)
		);

		$mailSetting = $this->__getMailSetting();
		$this->set('mailSetting', $mailSetting);

		$this->view = 'edit';
	}

/**
 * index edit
 *
 * @return void
 * @throws BadRequestException
 */
	public function edit() {
		$this->_prepare();
		$this->set('listTitle', $this->_taskTitle);
		$key = $this->params['key'];
		$taskContent = $this->TaskContent->getTask($key);
		if (empty($taskContent)) {
			return $this->throwBadRequest();
		}

		//ADD カレンダ連携キーの取り出し
		$calendarKey = $taskContent['TaskContent']['calendar_key'];
		$taskContent['TaskContent']['use_calendar'] = ($calendarKey == "") ? 0 : 1;
		if (! $this->request->data) {
			$this->request->data = $taskContent;
		}

		// ToDo担当者ユーザー保持
		$this->request->data = $this->TaskCharge->getSelectUsers($this->request->data, false);

		if ($this->TaskContent->canEditWorkflowContent($taskContent) === false) {
			return $this->throwBadRequest();
		}
		$this->_prepare();

		if ($this->request->is('put')) {

			$this->TaskContent->create();
			$this->request->data['TaskContent']['task_key'] =
				$this->_taskSetting['TaskSetting']['task_key'];
			$this->request->data['TaskContent']['key'] = $key;

			//ADD カレンダー連携キー転写
			$this->request->data['TaskContent']['calendar_key'] = $calendarKey;

			// set status
			$status = $this->Workflow->parseStatus();
			$this->request->data['TaskContent']['status'] = $status;
			// set block_id
			$this->request->data['TaskContent']['block_id'] = Current::read('Block.id');
			// set language_id
			$this->request->data['TaskContent']['language_id'] = Current::read('Language.id');

			$data = $this->request->data;

			// set task_end_date
			$data = $this->__setTaskDate($data);
			if (! $data['TaskContent']['is_enable_mail']) {
				unset($data['TaskContent']['email_send_timing']);
			} else {
				$data['is_reminder'] = true;
			}

			unset($data['TaskContent']['id']); // 常に新規保存

			if ($result = $this->TaskContent->saveContent($data)) {
				$url = NetCommonsUrl::actionUrl(
					array(
						'controller' => 'task_contents',
						'action' => 'view',
						'frame_id' => Current::read('Frame.id'),
						'block_id' => Current::read('Block.id'),
						'key' => $data['TaskContent']['key'],
						'is_makeReminder' => $result['is_makeReminder']
					)
				);

				return $this->redirect($url);
			}

			$this->NetCommons->handleValidationError($this->TaskContent->validationErrors);
		}

		$this->request->data = $this->NetCommonsTime->toUserDatetimeArray(
				$this->request->data,
			array(
				'TaskContent.task_start_date',
				'TaskContent.task_end_date',
			));

		$mailSetting = $this->__getMailSetting();
		$this->set('taskContent', $taskContent);
		$this->set('mailSetting', $mailSetting);
		$this->set('isDeletable', $this->TaskContent->canDeleteWorkflowContent($taskContent));

		$comments = $this->TaskContent->getCommentsByContentKey($taskContent['TaskContent']['key']);
		$this->set('comments', $comments);
	}

/**
 * delete method
 *
 * @throws InternalErrorException
 * @return void
 */
	public function delete() {
		$this->request->allowMethod('post', 'delete');

		$key = $this->request->data['TaskContent']['key'];
		$taskContent = $this->TaskContent->findByKeyAndIsLatest($key, 1);

		// 権限チェック
		if ($this->TaskContent->canDeleteWorkflowContent($taskContent) === false) {
			return $this->throwBadRequest();
		}

		if ($this->TaskContent->deleteContentByKey($key) === false) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		$this->redirect(NetCommonsUrl::backToPageUrl());
	}

/**
 * Get Mail Setting
 *
 * メール設定情報の取得
 *
 * @return array メール設定情報の配列
 */
	private function __getMailSetting() {
		$mailSetting = $this->MailSetting->find('first', array(
				'conditions' => array(
					$this->MailSetting->alias . '.plugin_key' => 'tasks',
					$this->MailSetting->alias . '.block_key' => Current::read('Block.key'),
				),
				'recursive' => -1,
			)
		);
		return $mailSetting;
	}

/**
 * Set Task Date
 *
 * 実施日設定が選択されていない場合実施日を初期化する
 * 実施日設定が選択されており実施終了日が設定されている場合ToDoの実施日終了日の時刻を23:59:59に設定する
 * 
 * @param array $data POSTされたToDoデータ
 * @return array
 */
	private function __setTaskDate($data) {
		if ($data['TaskContent']['is_date_set'] && $data['TaskContent']['task_end_date']) {
			$endDate = $data['TaskContent']['task_end_date'];
			if ($endDate) {
				$data['TaskContent']['task_end_date'] = date(
					'Y-m-d H:i:s', strtotime($endDate . '+1 days -1 second')
				);
			}
		} elseif (! $data['TaskContent']['is_date_set']) {
			$data['TaskContent']['task_start_date'] = null;
			$data['TaskContent']['task_end_date'] = null;
		}
		return $data;
	}
}
