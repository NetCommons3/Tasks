<?php
/**
 * TaskProgressRate Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TasksAppController', 'Tasks.Controller');

/**
 * TaskProgressRate Controller
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @package NetCommons\Tasks\Controller
 * @property TaskContent $TaskContent
 * @property TaskCharge $TaskCharge
 */
class TaskProgressRateController extends TasksAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Tasks.TaskContent',
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
 * 進捗率を更新
 *
 * @return void
 * @throws BadRequestException
 */
	public function edit() {
		if ($this->request->is('post')) {
			$rateCondition = $this->params['named'];
			$key = $rateCondition['content_key'];
			if (isset($rateCondition['progress_rate'])) {
				$rateCondition['TaskContent']['progress_rate'] = $rateCondition['progress_rate'];
				unset($rateCondition['progress_rate']);
			}

			// 一覧から完了ボタンの処理時にnamedから値を取得
			$this->params['data'] = array_merge($this->params['data'], $rateCondition);
			$progressRate = $this->params['data']['TaskContent']['progress_rate'];
			// 進捗率を更新する
			if (! $this->TaskContent->updateProgressRate($key, $progressRate)) {
				$this->throwBadRequest();
				return;
			}

			$message = __d('tasks', 'Updated progress rate');
			$this->NetCommons->setFlashNotification(
				$message, array('class' => 'success')
			);

			// 元の画面を表示
			$this->redirect($this->request->referer(true));
		}
	}
}
