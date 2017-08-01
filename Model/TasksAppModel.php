<?php
/**
 * TaskAppModel
 */
App::uses('AppModel', 'Model');
App::uses('TasksComponent', 'Tasks.Controller/Component');
App::uses('MailQueueBehavior', 'Mails.Model/Behavior');

/**
 * Class TasksAppModel
 */
class TasksAppModel extends AppModel {

/**
 * Validate datetime from to.
 *
 * @param array $check check fields.
 * @param array $params parameters.
 * @return bool
 */
	public function validateIsDateCheck($check, $params) {
		$valid = false;
		$checkValue = array_values($check)[0];
		if (! $checkValue) {
			$valid = true;
		}
		if (isset($params['from']) && $params['from']) {
			$valid = true;
		}
		if (isset($params['to']) && $params['to']) {
			$valid = true;
		}

		return $valid;
	}

/**
 * Validate datetime from to.
 *
 * @param array $check check fields.
 * @param array $params parameters.
 * @return bool
 */
	public function validateDatetimeFromTo($check, $params) {
		$checkValue = array_values($check)[0];
		$isCompareFrom = isset($params['from']);
		if (! $isCompareFrom) {
			return true;
		}
		$compareValue = $isCompareFrom ? $params['from'] : $params['to'];

		if (($isCompareFrom && $checkValue >= $compareValue)
				|| (! $isCompareFrom && $checkValue <= $compareValue)) {
			return true;
		}
		return false;
	}

/**
 * Validate is set reminder check
 *
 * @param array $check check fields.
 * @param array $params parameters.
 * @return bool
 */
	public function validateIsSetReminderCheck($check, $params) {
		// is_enable_mailがFalseならそのままTrueを返す
		if (! $params['is_enable_mail']) {
			return true;
		}

		$checkValue = array_values($check)[0];
		$valid = true;
		if ($params['is_enable_mail'] === true && ! $params['to']) {
			$valid = false;
		}

		// 実施期間終了日が現在日時より前の場合、リマインダーメールは破棄されてしまうためFalseを返す
		$taskEndDate = date('Y-m-d H:i:s', strtotime($params['to'] . ' -1day +1 second'));
		$sendTime = date(
			'Y-m-d H:i:s', strtotime(
				$taskEndDate . ' -' . $checkValue . 'day'
			)
		);
		$now = date('Y-m-d H:i:s');

		if ($now > $sendTime) {
			$valid = false;
		}

		return $valid;
	}

/**
 * 登録されている実施日によりdate_colorを取得
 *
 * @param array $taskStartDate 実施開始日
 * @param array $taskEndDate 実施終了日
 *
 * @return int
 */
	public function getTaskDateColor($taskStartDate, $taskEndDate) {
		$now = date('Ymd', strtotime(date('Y/m/d H:i:s')));
		$deadLine = date('Ymd', strtotime('+2 day'));

		$dateColor = TasksComponent::TASK_BEING_PERFORMED;
		// 現在の日付が開始日より前
		if (! empty($taskStartDate)
				&& intval(date('Ymd', strtotime($taskStartDate))) > $now
		) {
			$dateColor = TasksComponent::TASK_START_DATE_BEFORE;
		}
		if (! empty($taskEndDate)) {
			// 終了期限間近
			if (intval(date('Ymd', strtotime($taskEndDate))) >= intval($now)
					&& intval(date('Ymd', strtotime($taskEndDate))) <= intval($deadLine)
			) {
				$dateColor = TasksComponent::TASK_DEADLINE_CLOSE;
				// 終了期限切れ
			} elseif (intval(date('Ymd', strtotime($taskEndDate))) < intval($now)
			) {
				$dateColor = TasksComponent::TASK_BEYOND_THE_END_DATE;
			}
		}

		return $dateColor;
	}

/**
 * date_colorにより期限間近か否か判定
 *
 * @param array $dateColor ToDoの実施期間判定色
 *
 * @return array
 */
	public function isDeadLine($dateColor) {
		if ($dateColor == TasksComponent::TASK_DEADLINE_CLOSE
			|| $dateColor == TasksComponent::TASK_BEYOND_THE_END_DATE
		) {
			return true;
		}
		return false;
	}

/**
 * 通知メール及びリマインダーメールを作成する
 *
 * @param array $reminder リマインダーメール設定フラグ
 * @param array $makeReminder リマインダーメール作成フラグ
 * @return bool
 *
 * @throws InternalErrorException
 */
	public function isReminder($reminder, $makeReminder) {
		$isMakeReminder = false;
		if ($reminder && ! $makeReminder) {
			$isMakeReminder = true;
		}
		return $isMakeReminder;
	}

/**
 * カレンダー連携
 *
 * @param array $data 登録データ
 * @param array $savedData 登録後データ
 * @return bool
 */
	public function calendarCollaboration($data, $savedData) {
		//カレンダー連携ここから ADD
		$cmd = 'del';
		if ($data['TaskContent']['is_date_set']) {
			$cmd = ($data['TaskContent']['use_calendar']) ? 'save' : 'del';
		}
		if ($cmd === 'save') {
			$savedData = $this->__saveCalendar($data, $savedData);

		} else {	//cmd===del
			if (! empty($data['TaskContent']['calendar_key'])) {
				$savedData = $this->__deleteCalendar($data);
			}
		}
		//カレンダー連携ここまで ADD
		return $savedData;
	}

/**
 * カレンダー登録
 *
 * @param array $data 登録データ
 * @param array $savedData 登録後データ
 * @return bool
 * @throws InternalErrorException
 */
	private function __saveCalendar($data, $savedData) {
		//実施期間設定あり&&カレンダー登録する
		$this->loadModels([
			'CalendarActionPlan' => 'Calendars.CalendarActionPlan',
		]);

		//登録・変更用settings指定付きでbehaviorロード
		$this->CalendarActionPlan->Behaviors->load('Calendars.CalendarLink', array(
			'linkPlugin' => Current::read('Plugin.key'),
			'table' => $this->alias,	//fieldsの対象テーブル
			'inputFields' => array(
				'title' => 'title',
				'description' => 'content',
			),
			'sysFields' => array(
				'key' => 'key',	//tasksの場合、task_contentsテーブルのkey
				'calendar_key' => 'calendar_key',	//tasksの場合、task_contentsテーブルのcalendar_key
			),
			'startendFields' => array(
				'start_datetime' => 'task_start_date',
				'end_datetime' => 'task_end_date',
			),
			'isServerTime' => true,
			'useStartendComplete' => true,
			'isLessthanOfEnd' => false,
			'isRepeat' => false,
			'isPlanRoomId' => false,
		));

		$calendarKey = $this->CalendarActionPlan->savePlanForLink($data);
		if (is_string($calendarKey) && ! empty($calendarKey)) {
			//カレンダ登録成功
			//calenar_keyを TaskContentにsave(update)しておく。
			$data['TaskContent']['calendar_key'] = $calendarKey;
			$savedData = $this->save($data, false);
			if ($savedData === false) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$data['TaskContent'] = $savedData['TaskContent'];
		} elseif ($calendarKey === '') {
			//未承認や一時保存はカレンダー登録条件を満たさないのでスルー（通常）
		} else { //false
			//カレンダー登録時にエラー発生（エラー）
			//例外なげる
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		$this->CalendarActionPlan->Behaviors->unload('Calendars.CalendarLink');
		return $savedData;
	}

/**
 * カレンダー削除
 *
 * @param array $data 登録データ
 * @return bool
 * @throws InternalErrorException
 */
	private function __deleteCalendar($data) {
		//calendar_keyが記録されているので、消しにいく。
		$this->loadModels([
			'CalendarDeleteActionPlan' => 'Calendars.CalendarDeleteActionPlan',
		]);
		//削除用settings指定
		$this->CalendarDeleteActionPlan->Behaviors->load('Calendars.CalendarLink', array(
			'linkPlugin' => Current::read('Plugin.key'),
			'table' => $this->alias,	//fieldsの対象テーブル
			'sysFields' => array(
				'key' => 'key',	//tasksの場合、task_contentsテーブルのkey
				'calendar_key' => 'calendar_key',	//tasksの場合、task_contentsテーブルのcalendar_key
			),
			'isDelRepeat' => false,	//tasksはfalse固定
		));
		$this->CalendarDeleteActionPlan->deletePlanForLink($data);
		//if ($data['TaskContent']['calendar_key'] == $delCalendarKey) {
		//削除が成功したので、calenar_keyをクリアし、use_calendarをＯＦＦにして、
		//TaskContentにsave(update)しておく。
		$data['TaskContent']['calendar_key'] = '';
		$data['TaskContent']['use_calendar'] = 0;
		$savedData = $this->save($data, false);
		if ($savedData === false) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		$data['TaskContent'] = $savedData['TaskContent'];
		//}
		$this->CalendarDeleteActionPlan->Behaviors->unload('Calendars.CalendarLink');
		return $savedData;
	}

/**
 * 通知メール及びリマインダーメールを作成する
 *
 * @param array $data 登録データ
 * @return bool
 */
	public function setReminderMail($data) {
		$data['is_make_reminder'] = true;
		$savedData = $this->saveContent($data);
		return $savedData;
	}
}
