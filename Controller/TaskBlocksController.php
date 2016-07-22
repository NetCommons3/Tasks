<?php
/**
 * TaskBlocks Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TasksAppController', 'Tasks.Controller');

/**
 * TaskBlocks Controller
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @package NetCommons\Tasks\Controller
 */
class TaskBlocksController extends TasksAppController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Blocks.Block',
		'Tasks.TaskContent',
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
				'index,add,edit,delete' => 'block_editable',
			),
		),
		'Paginator',
		'Categories.CategoryEdit',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockForm',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'task_blocks')),
			),
			'blockTabs' => array(
				'block_settings' => array('url' => array('controller' => 'task_blocks')),
				'mail_settings',
				'role_permissions' => array('url' => array('controller' => 'task_block_role_permissions')),
			),
		),
		'Blocks.BlockIndex',
		'Likes.Like',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		//CategoryEditComponentの削除
		if ($this->params['action'] === 'index') {
			$this->Components->unload('Categories.CategoryEdit');
		}
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->Paginator->settings = array(
			'Task' => $this->Task->getBlockIndexSettings()
		);

		$tasks = $this->Paginator->paginate('Task');
		if (! $tasks) {
			$this->view = 'Blocks.Blocks/not_found';
			return;
		}
		foreach ($tasks as &$task) {
			// is_latestで同一言語の件数をとってくる
			$conditions = [
				'TaskContent.language_id' => Current::read('Language.id'),
				'TaskContent.is_latest' => true,
				'TaskContent.task_key' => $task['Task']['key']
			];
			$count = $this->TaskContent->find('count', ['conditions' => $conditions]);

			$task['Task']['entries_count'] = $count;
		}
		$this->set('tasks', $tasks);
		$this->request->data['Frame'] = Current::read('Frame');
	}

/**
 * add
 *
 * @return void
 */
	public function add() {
		$this->view = 'edit';

		if ($this->request->is('post')) {
			//登録処理
			if ($this->Task->saveTask($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->Task->validationErrors);

		} else {
			//表示処理(初期データセット)
			$this->request->data = $this->Task->createTask();
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		if ($this->request->is('put')) {
			//登録処理
			if ($this->Task->saveTask($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->Task->validationErrors);

		} else {
			//表示処理(初期データセット)
			if (! $task = $this->Task->getTask()) {
				return $this->throwBadRequest();
			}
			$this->request->data = Hash::merge($this->request->data, $task);
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}

/**
 * delete
 *
 * @return void
 */
	public function delete() {
		if ($this->request->is('delete')) {
			if ($this->Task->deleteTask($this->data)) {
				return $this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
		}

		return $this->throwBadRequest();
	}
}
