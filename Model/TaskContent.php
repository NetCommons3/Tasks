<?php
/**
 * TaskContent Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TasksAppModel', 'Tasks.Model');

/**
 * Summary for TaskContent Model
 */
class TaskContent extends TasksAppModel {

/**
 * 実施開始日前のタスク
 *
 * @var const
 */
	const TASK_START_DATE_BEFORE = 1;

/**
 * 実施終了日2日前のタスク
 *
 * @var const
 */
	const TASK_END_DATE_TWO_DAY_BEFORE = 2;

/**
 * 実施終了日を過ぎたタスク
 *
 * @var const
 */
	const TASK_BEYOND_THE_END_DATE = 3;

/**
 * 実施中のタスク
 *
 * @var const
 */
	const TASK_BEING_PERFORMED = 4;

/**
 * タスク完了時の進捗率
 *
 * @var const
 */
	const TASK_COMPLETION_PROGRESS_RATE = 100;

/**
 * @var int recursiveはデフォルトアソシエーションなしに
 */
	public $recursive = -1;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
		'NetCommons.OriginalKey',
		'Workflow.Workflow',
		'Workflow.WorkflowComment',
		'ContentComments.ContentComment',
		'Topics.Topics' => array(
			'fields' => array(
				'title' => 'title',
				'summary' => 'content',
				'path' => '/:plugin_key/task_contents/view/:block_id/:content_key',
			),
		),
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'TaskContent.title',
				'X-BODY' => 'TaskContent.content',
			),
		),
		'Tasks.TaskCharge',
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('content'),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Category' => array(
			'className' => 'Categories.Category',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'TaskCharge' => array(
			'className' => 'Tasks.TaskCharge',
			'foreignKey' => 'task_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

/**
 * バリデートメッセージ多言語化対応のためのラップ
 *
 * @param array $options options
 * @return bool
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge(
			$this->validate,
			$this->_getValidateSpecification()
		);
		return parent::beforeValidate($options);
	}

/**
 * バリデーションルールを返す
 *
 * @return array
 */
	protected function _getValidateSpecification() {
		$validate = array(
			'title' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('tasks', 'Title')),
					'required' => true,
				],
			),
			'content' => array(
				'notBlank' => [
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('tasks', 'Content')),
					'required' => true,
				],
			),
		);
		return $validate;
	}

/**
 * プラリマリキーを除いた新規レコード配列を返す
 * ex) array('ModelName' => array('filedName' => default, ...));
 *
 * @return array
 */
	protected function _getNew() {
		if (is_null($this->_newRecord)) {
			$newRecord = array();
			foreach ($this->_schema as $fieldName => $fieldDetail) {
				if ($fieldName != $this->primaryKey) {
					$newRecord[$this->name][$fieldName] = $fieldDetail['default'];
				}
			}
			$this->_newRecord = $newRecord;
		}
		return $this->_newRecord;
	}

/**
 * 空の新規データを返す
 *
 * @return array
 */
	public function getNew() {
		$new = $this->_getNew();
		return $new;
	}

/**
 * ToDoの一覧データを返す
 *
 * @param array $params 絞り込み条件
 * @param array $order 並べ替え条件
 * @param void $now 現在日時
 * @return array
 */
	public function getList($params = array(), $order = array(), $now = '') {
		$conditions = $this->getConditions(Current::read('Block.id'), $params);

		$lists = $this->find('threaded',
			array('recursive' => 1, 'conditions' => $conditions, 'order' => $order));

		if (!$lists) {
			return array();
		}

		$now = date('Ymd', strtotime($now));
		$deadLine = date("Ymd", strtotime("+2 day"));
		$deadTasks = array();
		$contentLists = array();

		foreach ($lists as $list) {
			// 現在の日付が開始日より前
			$list['TaskContent']['date_color'] = TaskContent::TASK_START_DATE_BEFORE;
			// 終了日が現在の日付から2日後以下でかつ現在の日付以下でないもの
			if (intval($list['TaskContent']['task_end_date']) <= intval($deadLine)
				&& intval($list['TaskContent']['task_end_date']) >= intval($now)
			) {
				$list['TaskContent']['date_color'] = TaskContent::TASK_END_DATE_TWO_DAY_BEFORE;
				$contentLists[] = $list;
				$deadTasks[] = $list;
				continue;
			}
			// 終了日が現在の日付以下のもの
			if (intval($list['TaskContent']['task_end_date']) < intval($now)) {
				$list['TaskContent']['date_color'] = TaskContent::TASK_BEYOND_THE_END_DATE;
				$contentLists[] = $list;
				$deadTasks[] = $list;
				continue;
			}
			// 開始日が設定されており現在の開始日が現在の日付以下のもの
			if (isset($list['TaskContent']['task_start_date'])
				&& intval($list['TaskContent']['task_start_date']) <= intval($now)
			) {
				$list['TaskContent']['date_color'] = TaskContent::TASK_BEING_PERFORMED;
				$contentLists[] = $list;
				continue;
			}
			$contentLists[] = $list;
		}

		$categoryArr = $this->getCategory($contentLists);

		$taskContentList = $this->setCategoryContentList($categoryArr);

		if ($deadTasks) {
			$taskContentList['DeadLine'] = $deadTasks;
		}

		return $taskContentList;
	}

/**
 * カテゴリデータを返す
 *
 * @param array $contentLists ToDoリスト
 *
 * @return array
 */
	public function getCategory($contentLists) {
		// 取得したデータに存在するカテゴリ配列を取得
		$categoryArr = Hash::combine($contentLists, '{n}.Category.id', '{n}.Category');

		// カテゴリが未指定のときのカテゴリ情報を作成
		$notCategory = array();
		if (isset($categoryArr[''])) {
			$notCategory[] = array(
					'id' => 0,
					'name' => __d('tasks', 'No category')
			);
			unset($categoryArr['']);
		}
		// カテゴリをidの降順で表示
		$categoryArr = Set::sort($categoryArr, '{n}.id', 'DESC');

		// カテゴリなしを配列の先頭へ配置するためのマージ
		$categoryArr = array_merge($notCategory, $categoryArr);

		return $categoryArr;
	}

/**
 * カテゴリと担当者データを整理したLISTデータを返す
 *
 * @param array $categoryArr カテゴリデータの配列
 *
 * @return array
 */
	public function setCategoryContentList($categoryArr) {
		$taskContentList = array();
		foreach ($categoryArr as $category) {
			$results = array();
			if (empty($category['id'])) {
				$category['id'] = 0;
				$category['name'] = __d('tasks', 'No category');
			}

			$contents = Hash::extract(
				$taskContentList, '{n}.TaskContent[category_id=' . $category['id'] . ']', '{n}'
			);

			// ToDoとToDo担当者を一つの配列にまとめる
			foreach ($contents as $content) {
				$taskCharge = Hash::extract(
					$taskContentList, '{n}.TaskCharge.{n}[task_id=' . $content['id'] . ']'
				);
				$results['TaskContents'][] = array('TaskContent' => $content, 'TaskCharge' => $taskCharge);
			}

			$categoryPriority = $this->getCategoryPriority($contents);

			$results['Category'] = $category;
			$results['Category']['category_priority'] = $categoryPriority;
			$taskContentList[] = $results;
		}
		return $taskContentList;
	}

/**
 * ToDoのデータを一件返す
 *
 * @param void $key key
 *
 * @return array
 */
	public function getTask($key) {
		$conditions = $this->getConditions(
			Current::read('Block.id')
		);

		$conditions = array_merge($conditions, array('TaskContent.key' => $key));

		$lists = $this->find('all', array(
			'recursive' => 1,
			'conditions' => $conditions
		));

		if (!$lists) {
			return array();
		}

		return $lists[0];
	}

/**
 * UserIdから参照可能なToDoを取得するCondition配列を返す
 *
 * @param int $blockId ブロックId
 * @param array $conditions ソート絞り込み条件
 * @return array condition
 */
	public function getConditions($blockId, $conditions = array()) {
		// デフォルト絞り込み条件にソート絞り込み条件をマージする
		$conditions = array_merge($conditions, array('TaskContent.block_id' => $blockId));

		$conditions = $this->getWorkflowConditions($conditions);

		return $conditions;
	}

/**
 * カテゴリごとのToDoの進捗率の平均値を取得
 *
 * @param array $contents ToDoデータ
 * @return int
 */
	public function getCategoryPriority($contents) {
		$taskPriority = array();
		foreach ($contents as $content) {
			$taskPriority[] = $content['progress_rate'];
		}

		// ToDoの進捗率の合計を求める
		$total = array_sum($taskPriority);

		// 実際の値より大きくならないよう小数点以下切り捨て
		$categoryPriority = floor($total / count($taskPriority));

		return $categoryPriority;
	}

/**
 * ToDoの保存。
 *
 * @param array $data 登録データ
 * @return bool
 * @throws InternalErrorException
 */
	public function saveContent($data) {
		// 必要なモデル読み込み
		$this->loadModels([
			'TaskCharge' => 'Tasks.TaskCharge',
		]);
		$data['TaskCharges'] = Hash::map($data, 'TaskCharge.{n}.user_id', function ($value) {
			return array(
				'TaskCharge' => array(
					'id' => null,
					'user_id' => $value,
				));
		});

		$this->begin();
		try {
			$this->create(); // 常に新規登録
			// 先にvalidate 失敗したらfalse返す
			$this->set($data);
			if (!$this->validates($data)) {
				$this->rollback();
				return false;
			}

			if (($savedData = $this->save($data, false)) === false) {
				//このsaveで失敗するならvalidate以外なので例外なげる
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$data['TaskContent'] = $savedData['TaskContent'];
			// 担当者を登録
			if (!$this->TaskCharge->setCharges($data)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$this->commit();

		} catch (Exception $e) {
			$this->rollback($e);
		}
		return $savedData;
	}

/**
 * 進捗率を更新
 *
 * @param array $key ToDoキー
 * @param array $progressRate ToDo進捗率
 * @return bool
 * @throws InternalErrorException
 */
	public function saveProgressRate($key, $progressRate) {
		$this->begin();
		try {
			$isCompletion = false;
			if ((int)$progressRate === TaskContent::TASK_COMPLETION_PROGRESS_RATE) {
				$isCompletion = true;
			}

			$data = array(
				'TaskContent.progress_rate' => $progressRate,
				'is_completion' => $isCompletion,
			);
			$conditions = array(
				'TaskContent.key' => $key,
			);
			if (!$this->updateAll($data, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$this->commit();

		} catch (Exception $e) {
			$this->rollback($e);
		}

		return true;
	}
}
