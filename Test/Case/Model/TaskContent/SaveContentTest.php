<?php
/**
 * TaskContent::saveContent()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowSaveTest', 'Workflow.TestSuite');
App::uses('TaskContentFixture', 'Tasks.Test/Fixture');
App::uses('TaskChargeFixture', 'Tasks.Test/Fixture');

/**
 * TaskContent::saveContent()のテスト
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Model\TaskContent
 */
class TaskContentSaveContentTest extends WorkflowSaveTest {

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
	protected $_methodName = 'saveContent';

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
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderSave() {
		$data['TaskContent'] = (new TaskContentFixture())->records[1];
		$data['TaskCharge'][] = (new TaskChargeFixture())->records[0];
		$data['TaskContent']['status'] = '1';
		
		$data['TaskContent']['use_calendar'] = false;
		$data['TaskContent']['calendar_key'] = '';

		$results = array();
		// * 編集の登録処理
		$results[0] = array($data);
		// * 新規の登録処理
		$results[1] = array($data);
		$results[1] = Hash::insert($results[1], '0.TaskContent.id', null);
		$results[1] = Hash::insert($results[1], '0.TaskContent.key', null);
		$results[1] = Hash::remove($results[1], '0.TaskContent.created_user');
		$results[1] = Hash::remove($results[1], '0.TaskContent.created');

		return $results;
	}

/**
 * SaveのExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Tasks.TaskContent', 'save'),
		);
	}

/**
 * SaveのValidationError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド(省略可：デフォルト validates)
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Tasks.TaskContent'),
		);
	}

/**
 * SetChargeFalseのテスト
 *
 * @return void
 */
	public function testSetChargeFalse() {
		$data['TaskContent'] = (new TaskContentFixture())->records[1];

		$this->setExpectedException('InternalErrorException');

		$model = $this->_modelName;
		$method = $this->_methodName;

		$choicesMock = $this->getMockForModel('Tasks.' . 'TaskCharge', ['setCharges']);
		$choicesMock->expects($this->any())
				->method('setChargesAll')
				->will($this->returnValue(false));

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertFalse($result);
	}

/**
 * SaveFalseのテスト
 *
 * @return void
 */
	public function testSaveFalse() {
		$data['TaskContent'] = (new TaskContentFixture())->records[1];
		$data['TaskContent']['title'] = null;

		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertFalse($result);
	}
}
