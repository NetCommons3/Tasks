<?php
/**
 * TaskContent::deleteContentByKey()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowDeleteTest', 'Workflow.TestSuite');
App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('TaskContentFixture', 'Tasks.Test/Fixture');
App::uses('TaskChargeFixture', 'Tasks.Test/Fixture');

/**
 * TaskContent::deleteContentByKey()のテスト
 *
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Model\TaskContent
 */
class TaskContentDeleteContentByKeyTest extends NetCommonsModelTestCase {

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
		'plugin.rooms.room4test',
		'plugin.rooms.rooms_language4test',
		'plugin.categories.category',
		'plugin.categories.category_order',
		'plugin.categories.categories_language',
		'plugin.tasks.task',
		'plugin.tasks.task_charge',
		'plugin.tasks.task_content',
		'plugin.tasks.block_setting_for_task',
		'plugin.workflow.workflow_comment',
		'plugin.content_comments.content_comment',
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
	protected $_methodName = 'deleteContentByKey';

/**
 * testDeleteContentByKey
 *
 * @return void
 */
	//public function testDeleteContentByKey() {
		//$key = 'content_key_1';
		//$result = $this->TaskContent->deleteContentByKey($key);
		//$this->assertTrue($result);

		//$count = $this->TaskContent->find('count', ['conditions' => ['key' => $key]]);

		//$this->assertEquals(0, $count);
	//}

/**
 * testDeleteContentByKey
 *
 * @return void
 */
	public function testDeleteContentByKeyNull() {
		$key = null;
		$result = $this->TaskContent->deleteContentByKey($key);
		$this->assertTrue($result);

		$count = $this->TaskContent->find('count', ['conditions' => ['key' => $key]]);

		$this->assertEquals(0, $count);
	}

/**
 * testDeleteContentByKey delete failed
 *
 * @return void
 */
	public function testDeleteContentByKeyFailed1() {
		$model = 'TaskContent';
		$key = 'content_key_1';
		$this->_mockForReturnFalse($model, 'TaskCharge', 'deleteAll');

		$this->setExpectedException('InternalErrorException');
		$this->$model->deleteContentByKey($key);
	}

/**
 * testDeleteContentByKey delete failed
 *
 * @return void
 */
	//public function testDeleteContentByKeyFailed2() {
		//$key = 'content_key_1';
		//$taskContentMock = $this->getMockForModel('Tasks.TaskContent', ['deleteAll']);
		//$taskContentMock->expects($this->once())
			//->method('deleteAll')
			//->will($this->returnValue(false));

		//$this->setExpectedException('InternalErrorException');
		//$taskContentMock->deleteContentByKey($key);
	//}
}
