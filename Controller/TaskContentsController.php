<?php
/**
 * TaskContents Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TasksAppController', 'Tasks.Controller');

/**
 * TaskContents Controller
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @package NetCommons\Tasks\Controller
 * @property TaskContent $TaskContent
 * @property TaskCharge $TaskCharge
 */
class TaskContentsController extends TasksAppController {

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
		'ContentComments.ContentComments' => array(
			'viewVarsKey' => array(
				'contentKey' => 'taskContent.TaskContent.key',
				'useComment' => 'taskSetting.use_comment'
			),
			'allow' => array('view')
		),
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
		'NetCommons.TitleIcon',
		'ContentComments.ContentComment' => array(
			'viewVarsKey' => array(
				'contentKey' => 'taskContent.TaskContent.key',
				'contentTitleForMail' => 'taskContent.TaskContent.title',
				'useComment' => 'taskSetting.use_comment',
				'useCommentApproval' => 'taskSetting.use_comment_approval'
			)
		),
		'Categories.Category',
		'Users.DisplayUser',
		'Users.UserSearch',
		'Groups.GroupUserList',
	);

/**
 * index action
 *
 * @return void
 */
	public function index() {
		if (! Current::read('Block.id')) {
			$this->autoRender = false;
			return;
		}

		$this->_prepare();
		$this->set('listTitle', $this->_taskTitle);
		$this->set('categoryLabel', h(__d('tasks', 'No category assignment')));

		$conditions = $this->request->params['named'];

		if (isset($conditions['category_id'])) {
			$category = $this->Category->find('first', array(
				'recursive' => 0,
				'conditions' => array('Category.id' => $conditions['category_id']),
			));
			if (! $category) {
				return $this->throwBadRequest();
			}
			$this->set('categoryLabel', $category['CategoriesLanguage']['name']);
		}

		$this->__list($conditions);
	}

/**
 * index view
 *
 * @return void
 */
	public function view() {
		if (! Current::read('Block.id')) {
			$this->autoRender = false;
			return;
		}
		$key = $this->params['key'];

		$this->_prepare();
		$this->set('listTitle', $this->_taskTitle);

		$taskContent = $this->TaskContent->getTask($key);

		if ($taskContent) {
			$this->set('taskContent', $taskContent);

			$selectUsers = Hash::extract($taskContent['TaskCharge'], '{n}.user_id');

			$this->request->data['selectUsers'] = array();
			$this->loadModel('Users.User');
			$this->request->data['selectUsers'] = $this->__setSelectUsers($selectUsers);

			// コメントを利用する
			if ($this->_taskSetting['TaskSetting']['use_comment']) {
				if ($this->request->is('post')) {
					// コメントする
					$taskContentKey = $taskContent['TaskContent']['key'];
					$useCommentApproval = $this->_taskSetting['TaskSetting']['use_comment_approval'];
					if (! $this->ContentComments->comment('tasks', $taskContentKey,
						$useCommentApproval)
					) {
						return $this->throwBadRequest();
					}
				}
			}

			// リマインダーメールを設定
			$conditions = $this->request->params['named'];
			if (isset($conditions['is_makeReminder']) && $conditions['is_makeReminder'] === '1') {
				if ($result = $this->TaskContent->setReminderMail($taskContent)) {
					$url = NetCommonsUrl::actionUrl(array(
						'controller' => 'task_contents',
						'action' => 'view',
						'frame_id' => Current::read('Frame.id'),
						'block_id' => Current::read('Block.id'),
						'key' => $result['TaskContent']['key'],
					));
					return $this->redirect($url);
				} else {
					// セーブに失敗しているのでBadRequestを返す
					return $this->throwBadRequest();
				}
			}

		} else {
			// 表示できないToDoへのアクセスならBadRequest
			return $this->throwBadRequest();
		}
	}

/**
 * 一覧
 *
 * @param array $conditions ソート絞り込み条件
 * @return void
 */
	private function __list($conditions) {
		$this->TaskContent->recursive = 0;
		$this->TaskContent->Behaviors->load('ContentComments.ContentComment');

		$params = array();
		$afterOrder = array(
			'TaskContent.task_end_date' => 'asc',
			'TaskContent.priority' => 'desc',
			'TaskContent.progress_rate' => 'desc',
			'TaskContent.title' => 'asc',
			'TaskContent.modified' => 'desc'
		);

		$userParam = array();
		// カテゴリ絞り込み
		if (isset($conditions['category_id'])) {
			$params[] = array('TaskContent.category_id' => $conditions['category_id']);
		}

		// 担当者絞り込み
		if (isset($conditions['user_id'])
				&& $this->TaskCharge->searchChargeUser($conditions['user_id'])) {
			$userParam = array('TaskCharge.user_id' => $conditions['user_id']);
			$currentUserId = $conditions['user_id'];
		} else {
			$currentUserId = 'all';
		}

		// 完了未完了option取得
		$isCompletionOptions = $this->__getSelectOptions('is_completion');
		// 完了未完了絞り込み
		$currentIsCompletion = '';
		if (isset($conditions['is_completion'])) {
			if ($conditions['is_completion'] === 'all') {
				$currentIsCompletion = $conditions['is_completion'];
			} elseif (isset(
					$isCompletionOptions['TaskContents.is_completion.' . $conditions['is_completion']]
			)) {
				$params[] = array('TaskContent.is_completion' => $conditions['is_completion']);
				$currentIsCompletion = $conditions['is_completion'];
			}
		} else {
			$params[] = array('TaskContent.is_completion' => TasksComponent::TASK_CONTENT_INCOMPLETE_TASK);
		}

		// 並べ替えoption取得
		$sortOptions = $this->__getSelectOptions('sort');
		// 並べ替え絞り込み
		$sort = $this->__getSortParam($conditions, $sortOptions);

		// order情報を整理
		$order = array_merge($sort['order'], $afterOrder);
		$params = $this->__setTaskChargeContents($params, $userParam);

		// ToDo一覧を取得
		$results = $this->TaskContent->getTaskContentList($params, $order);
		$taskContents = Hash::extract($results, 'tasks');

		// 期限間近のToDo一覧を取得
		$deadLineTasks = Hash::extract($results, 'deadLineTasks');

		// 期限間近のToDo一覧
		$this->set('deadLineTasks', $deadLineTasks);

		// ToDo一覧
		$this->set('taskContents', $taskContents);

		// デフォルトの担当者選択肢を取得
		$defaultOptions = $this->__getSelectOptions('default');

		$selectChargeUsers = $this->TaskCharge->getSelectChargeUsers($taskContents);

		// 担当者絞り込み条件をマージする
		$userOptions = array_merge($defaultOptions, $selectChargeUsers);
		$this->set('currentUserId', $currentUserId);
		$this->set('userOptions', $userOptions);
		$this->set('currentIsCompletion', $currentIsCompletion);
		$this->set('currentSort', $sort['currentSort']);

		$this->TaskContent->Behaviors->unload('ContentComments.ContentComment');
	}

/**
 * Get Select Options
 *
 * 絞り込み及びソートのoption取得
 *
 * @param void $selectTarget 絞り込み及びソート対象名
 * @return array selectOptions
 */
	private function __getSelectOptions($selectTarget = '') {
		$selectOptions = array();

		if ($selectTarget === 'default') {
			// 自身のユーザーデータを取得
			// 担当者絞り込みデフォルト値
			$defaultOptions = array(
				'TaskContents.charge_user_id_all' => array(
					'label' => __d('tasks', 'No person in charge'),
					'user_id' => 'all',
				),
			);
			$myUserOptions = array();
			$myUser = array(Current::read('User'));
			if ($myUser[0]) {
				$myUserOptions = array(
					'TaskContents.charge_user_id_' . $myUser[0]['id'] => array(
						'label' => $myUser[0]['handlename'],
						'user_id' => $myUser[0]['id'],
					),
				);
			}
			$selectOptions = array_merge($defaultOptions, $myUserOptions);
		}

		if ($selectTarget === 'is_completion') {
			$selectOptions = array(
				'TaskContents.is_completion.' . TasksComponent::TASK_CONTENT_INCOMPLETE_TASK => array(
					'label' => __d('tasks', 'Incomplete task'),
					'is_completion' => TasksComponent::TASK_CONTENT_INCOMPLETE_TASK,
				),
				'TaskContents.is_completion.' . TasksComponent::TASK_CONTENT_IS_COMPLETION => array(
					'label' => __d('tasks', 'Completed task'),
					'is_completion' => TasksComponent::TASK_CONTENT_IS_COMPLETION,
				),
				'TaskContents.is_completion.' . 'all' => array(
					'label' => __d('tasks', 'All task'),
					'is_completion' => 'all',
				),
			);
			$this->set('isCompletionOptions', $selectOptions);
		}

		if ($selectTarget === 'sort') {
			$selectOptions = array(
				'TaskContent.task_end_date.asc' => array(
					'label' => __d('tasks', 'Close of the deadline order'),
					'sort' => 'TaskContent.task_end_date',
					'direction' => 'asc'
				),
				'TaskContent.title.asc' => array(
						'label' => __d('tasks', 'Title order'),
						'sort' => 'TaskContent.title',
						'direction' => 'asc'
				),
				'TaskContent.priority.desc' => array(
					'label' => __d('tasks', 'Priority order'),
					'sort' => 'TaskContent.priority',
					'direction' => 'desc'
				),
				'TaskContent.progress_rate.desc' => array(
					'label' => __d('tasks', 'High progress rate order'),
					'sort' => 'TaskContent.progress_rate',
					'direction' => 'desc'
				),
				'TaskContent.progress_rate.asc' => array(
					'label' => __d('tasks', 'Low progress rate order'),
					'sort' => 'TaskContent.progress_rate',
					'direction' => 'asc'
				),
			);
			$this->set('sortOptions', $selectOptions);
		}

		return $selectOptions;
	}

/**
 * Get Sort Param
 *
 * 並び替えのorderパラメーターとcurrentSortの値を取得
 *
 * @param array $conditions POSTされた絞り込み及びソートデータ
 * @param array $sortOptions 並び替え選択肢
 * @return array sortパラメーター
 */
	private function __getSortParam($conditions = array(), $sortOptions = array()) {
		$sortPram = '';
		$afterOrder = array(
			'TaskContent.is_date_set' => 'desc',
			'TaskContent.task_end_date is null',
			'TaskContent.task_end_date' => 'asc'
		);
		if (isset($conditions['sort']) && isset($conditions['direction'])) {
			$sortPram = $conditions['sort'] . '.' . $conditions['direction'];
		}
		if (isset($sortOptions[$sortPram])
			&& $sortPram !== 'TaskContent.task_end_date.asc'
		) {
			$order = array($conditions['sort'] => $conditions['direction']);
			$order = array_merge($order, $afterOrder);
			$currentSort = $conditions['sort'] . '.' . $conditions['direction'];
		} else {
			$order = $afterOrder;
			$currentSort = 'TaskContent.task_end_date.asc';
		}

		$sort = array(
			'order' => $order,
			'currentSort' => $currentSort,
		);

		return $sort;
	}

/**
 * Get Task Charge Content
 *
 * 絞り込み条件に担当者IDをセットする
 *
 * @param array $params 絞り込み条件
 * @param array $userParam 担当者絞り込み条件
 * @return array
 */
	private function __setTaskChargeContents($params, $userParam) {
		if ($userParam) {
			// 絞り込み条件に指定した担当者データを全て取得
			$taskChargeContents = $this->TaskCharge->find('threaded',
					array('recursive' => 1, 'conditions' => $userParam));
			// 担当者として設定されているToDoのcontent_idのみ取得
			$taskContentIds = Hash::extract($taskChargeContents, '{n}.TaskCharge.task_content_id');

			// 絞り込み条件に加える
			$params[] = array('TaskContent.id' => $taskContentIds);
		}

		return $params;
	}

/**
 * Get Task Charge Content
 *
 * 絞り込み条件に担当者IDをセットする
 *
 * @param array $selectUsers 選択されている担当者配列
 * @return array
 */
	private function __setSelectUsers($selectUsers) {
		$setSelectUsers = array();
		foreach ($selectUsers as $userId) {
			$setSelectUsers[] = $this->User->getUser($userId);
		}

		return $setSelectUsers;
	}
}
