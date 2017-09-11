<?php
/**
 * TaskProgressRateController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * TaskProgressRateController::edit()のテスト
 *
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Controller\TaskProgressRateController
 */
class TaskProgressRateControllerEditTest extends NetCommonsControllerTestCase {

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
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'tasks';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'task_progress_rate';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//ログイン
		TestAuthGeneral::login($this);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * edit()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testEditGet() {
		//テストデータ
		$blockId = '2';

		//アクション実行
		$url = NetCommonsUrl::actionUrl(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
			'block_id' => $blockId
		));
		$params = array(
			'method' => 'GET',
		);
		$result = $this->testAction($url, $params);

		//チェック
		$this->assertNull($result);
	}

/**
 * POSTリクエストデータ生成
 *
 * @return array リクエストデータ
 */
	private function __data() {
		$data = array(
			'TaskContent' => array(
				'progress_rate' => '10',
			),

			'Task' => array(
				'key' => 'task_key_2',
			),
		);

		return $data;
	}

/**
 * edit()アクションのPOSTリクエストテスト
 *
 * @return void
 */
	public function testEditPost() {
		//テストデータ
		$blockId = '2';
		$data = $this->__data();

		//アクション実行
		$url = NetCommonsUrl::actionUrl(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
			'content_key' => 'task_content_key',
			'block_id' => $blockId
		));
		$params = array(
			'data' => $data,
		);
		$this->testAction($url, $params);

		//チェック
		$header = $this->controller->response->header();
		$asserts = array(
			array('method' => 'assertNotEmpty', 'value' => $header['Location'])
		);
		$this->asserts($asserts, $this->contents);
	}

/**
 * edit()アクションのPOSTリクエストテスト
 *
 * @return void
 */
	public function testEditPostNamed() {
		//テストデータ
		$blockId = '2';

		//アクション実行
		$url = NetCommonsUrl::actionUrl(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
			'block_id' => $blockId,
			'content_key' => 'task_content_key',
			'TaskContent' => array('progress_rate' => 100)
		));
		$params = array();
		$this->testAction($url, $params);

		//チェック
		$header = $this->controller->response->header();
		$asserts = array(
			array('method' => 'assertNotEmpty', 'value' => $header['Location'])
		);
		$this->asserts($asserts, $this->contents);
	}

/**
 * ValidationErrorテスト
 *
 * @return void
 */
	public function testEditPostValidationError() {
		$this->_mockForReturnFalse('taskContent', 'updateProgressRate');

		//テスト実行
		$result = $this->_testPostAction('post', $this->__data(),
			array('action' => 'edit', 'content_key' => 'task_content_key'), 'BadRequestException', 'json');

		$expected = array(
			'name' => '不正なリクエストの可能性があります。',
			'code' => 400,
			'class' => 'danger',
			'interval' => 4000,
			//'plugin' => 'NetCommons',
			'ajax' => true,
			'error' => '不正なリクエストの可能性があります。',
		);

		$this->assertEquals($expected, $result);
	}

}
