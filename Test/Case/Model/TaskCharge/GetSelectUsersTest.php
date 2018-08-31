<?php
/**
 * TaskCharge::getSelectUsers()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * TaskCharge::getSelectUsers()のテスト
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Model\TaskCharge
 */
class TaskChargeGetSelectUsersTest extends NetCommonsGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.categories.category',
		'plugin.categories.category_order',
		'plugin.categories.categories_language',
		'plugin.tasks.task',
		'plugin.tasks.task_charge',
		'plugin.tasks.task_content',
		'plugin.tasks.block_setting_for_task',
		'plugin.workflow.workflow_comment',
		'plugin.user_roles.user_role_setting',
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
	protected $_modelName = 'TaskCharge';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getSelectUsers';

/**
 * テストデータ
 *
 * @return array
 */
	private function __data() {
		//データ生成
		return array(
			'TaskCharge' => array(
				array(
					'user_id' => '1',
					'task_content_id' => '1',
				),
			),
		);
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Users.User');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->User);
		parent::tearDown();
	}

/**
 * getSelectUsers()のテスト
 *
 * @return void
 */
	public function testGetSelectUsers() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$taskContent = $this->__data();

		//テスト実施
		$result = $this->$model->$methodName($taskContent, false);

		//登録データ取得
		$user = $this->User->findById($taskContent['TaskCharge'][0]['user_id']);
		$expected = [
			0 => [
				'User' => [
					'id' => $user['User']['id'],
					'handlename' => $user['User']['handlename']
				]
			]
		];

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getSelectUsers() Nullのテスト
 *
 * @return void
 */
	public function testGetSelectUsersNull() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$taskContent = null;

		//テスト実施
		$result = $this->$model->$methodName($taskContent, false);
		$expected = [];

		//チェック
		$this->assertEquals($result, $expected);
	}

}
