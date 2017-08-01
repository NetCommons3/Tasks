<?php
/**
 * TaskContent::setReminderMail()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowSaveTest', 'Workflow.TestSuite');
App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('TaskContentFixture', 'Tasks.Test/Fixture');
App::uses('TaskChargeFixture', 'Tasks.Test/Fixture');
App::uses('CalendarFixture', 'Calendars.Test/Fixture');
App::uses('CalendarFixture', 'Calendars.Test/Fixture');
App::uses('CalendarEventFixture', 'Calendars.Test/Fixture');
App::uses('CalendarEventContentFixture', 'Calendars.Test/Fixture');
App::uses('CalendarFrameSettingFixture', 'Calendars.Test/Fixture');
App::uses('CalendarFrameSettingSelectRoomFixture', 'Calendars.Test/Fixture');

/**
 * TaskContent::setReminderMail()のテスト
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto>
 * @package NetCommons\Tasks\Test\Case\Model\TaskContent
 */
class TaskContentSetReminderMailTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.tasks.calendar_for_task',
		'plugin.tasks.calendar_event_for_task',
		'plugin.tasks.calendar_frame_setting_for_task',
		'plugin.tasks.calendar_frame_setting_select_room_for_task',
		'plugin.tasks.calendar_rrule_for_task',
		'plugin.tasks.calendar_event_share_user_for_task',
		'plugin.tasks.calendar_event_content_for_task',
		'plugin.rooms.room_role',
//		'plugin.tasks.event_content',
		'plugin.categories.category',
		'plugin.categories.category_order',
		'plugin.categories.categories_language',
		'plugin.mails.mail_setting',
		'plugin.mails.mail_queue',
		'plugin.mails.mail_queue_user',
		'plugin.tasks.task',
		'plugin.tasks.task_charge',
		'plugin.tasks.task_content',
		'plugin.tasks.block_setting_for_task',
		'plugin.tasks.rooms_language_for_task',
		'plugin.categories.category',
		'plugin.workflow.workflow_comment',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'tasks';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'TaskContent';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'setReminderMail';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$model = $this->_modelName;
		$this->$model->Behaviors->unload('ContentComment');
		$this->$model->Behaviors->unload('Topics');
		$this->$model->Behaviors->load('MailQueue');
	}

/**
 * testSetReminderMailのDataProvider
 *
 * ### 戻り値
 *  - data 登録用データ
 *
 * @return array
 */
	public function dataProviderSetReminderMail() {
		return array(
			array(
				'data' => array(
					'TaskContent' => (new TaskContentFixture())->records[1],
					'TaskCharge' => array((new TaskChargeFixture())->records[0])
				),
			),
			array(
				'data' => array(
					'TaskContent' => (new TaskContentFixture())->records[1],
					'TaskCharge' => array()
				),
			),
			array(
				'data' => array(
					'TaskContent' => (new TaskContentFixture())->records[1],
					'TaskCharge' => array(
						0 => (new TaskChargeFixture())->records[2],
						1 => (new TaskChargeFixture())->records[3],
						2 => (new TaskChargeFixture())->records[4],
						3 => (new TaskChargeFixture())->records[5]
					)
				),
			),
		);
	}

/**
 * SetReminderMail()のテスト
 * @param $data
 * @dataProvider dataProviderSetReminderMail
 * @return void
 */
	public function testSetReminderMail($data) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		$result = $this->$model->$methodName($data);

		//チェック
		$this->assertNotEmpty($result);
	}

/**
 * testSetReminderMailFalseのDataProvider
 *
 * ### 戻り値
 *  - data 登録用データ
 *
 * @return array
 */
	public function dataProviderSetReminderMailFalse() {
		return array(
			array(
				'data' => array(
					'TaskContent' => (new TaskContentFixture())->records[0],
					'TaskCharge' => array((new TaskChargeFixture())->records[0])
				),
			),
			array(
				'data' => array(
					'TaskContent' => array(),
					'TaskCharge' => array()
				),
			),
			array(
				'data' => array(
					'TaskContent' => (new TaskContentFixture())->records[0],
					'TaskCharge' => array(
						0 => (new TaskChargeFixture())->records[0],
						1 => (new TaskChargeFixture())->records[2],
						2 => (new TaskChargeFixture())->records[3],
						3 => (new TaskChargeFixture())->records[4],
						4 => (new TaskChargeFixture())->records[5]
					)
				),
			),
		);
	}

/**
 * SetReminderMail()のFalseテスト
 * @param $data
 * @dataProvider dataProviderSetReminderMailFalse
 * @return void
 */
	public function testSetReminderMailFalse($data) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		$result = $this->$model->$methodName($data);

		//チェック
		$this->assertEmpty($result);
	}
}
