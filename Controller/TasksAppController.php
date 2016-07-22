<?php
/**
 * TasksApp Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * TasksApp Controller
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @package NetCommons\Tasks\Controller
 */
class TasksAppController extends AppController {

/**
 * @var array ToDo名
 */
	protected $_taskTitle;

/**
 * @var array ToDo設定
 */
	protected $_taskSetting;

/**
 * use component
 * 
 * @var array
 */
	public $components = array (
		'Pages.PageLayout',
		'Security',
	);

/**
 * @var array use model
 */
	public $uses = array(
		'Tasks.Task',
		'Tasks.TaskSetting',
	);

/**
 * beforeFilter
 * 
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * ブロック名をToDoタイトルとしてセットする
 *
 * @return void
 */
	protected function _setupTaskTitle() {
		$this->loadModel('Blocks.Block');
		$block = $this->Block->findById(Current::read('Block.id'));
		$this->_taskTitle = $block['Block']['name'];
	}

/**
 * 設定等の呼び出し
 *
 * @return void
 */
	protected function _prepare() {
		$this->_setupTaskTitle();
		$this->_initTask(['taskSetting']);
	}

/**
 * initTask
 *
 * @param array $contains Optional result sets
 * @return bool True on success, False on failure
 */
	protected function _initTask($contains = []) {
		if (! $task = $this->Task->getTask(Current::read('Block.id'), Current::read('Room.id'))) {
			return $this->throwBadRequest();
		}
		$this->_blogTitle = $task['Task']['name'];
		$this->set('task', $task);

		if (! $taskSetting = $this->TaskSetting->getTaskSetting($task['Task']['key'])) {
			$taskSetting = $this->TaskSetting->create(
				array('id' => null)
			);
		}
		$this->_taskSetting = $taskSetting;
		$this->set('taskSetting', $taskSetting['TaskSetting']);

		$this->set('userId', (int)$this->Auth->user('id'));

		return true;
	}

}
