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
 * @property Task $Task
 * @property TaskContent $TaskContent
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
				'block_index' => array('url' => array('controller' => 'task_blocks'))
			),
			'blockTabs' => array(
				'block_settings' => array('url' => array('controller' => 'task_blocks')),
				'mail_settings',
				'role_permissions' => array('url' => array('controller' => 'task_block_role_permissions'))
			)
		),
		'Blocks.BlockIndex',
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
			$oldData = $this->request->data('old');
			$updateCategoryIds = array();
			if (isset($oldData['Categories']) && $oldData['Categories'] !== []) {
				// 変更後のカテゴリデータ
				$newCategories = $this->request->data('Categories');
				$newCategoryIdArr = [];
				foreach ($newCategories as $newCategory) {
					$newCategoryIdArr[] = $newCategory['Category']['id'];
				}
				// 変更前のカテゴリデータ
				$oldCategories = json_decode($oldData['Categories'], true);
				$oldCategoryIdArr = [];
				foreach ($oldCategories as $oldCategory) {
					$oldCategoryIdArr[] = $oldCategory['Category']['id'];
				}
				// 更新対象カテゴリID
				$updateCategoryIds = array_diff($oldCategoryIdArr, $newCategoryIdArr);
			}
			//カテゴリID更新処理
			if ($updateCategoryIds) {
				$this->Task->updateCategoryId($updateCategoryIds);
			}
			//登録処理
			if ($this->Task->saveTask($this->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			}
			$this->NetCommons->handleValidationError($this->Task->validationErrors);

		} else {
			//表示処理(初期データセット)
			$task = $this->Task->getTask();
			if (! $task) {
				return $this->throwBadRequest();
			}
			$this->request->data = array_merge($this->request->data, $task);
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
