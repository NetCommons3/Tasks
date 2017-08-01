<?php
/**
 * TaskContentsController::view()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * TaskContentsController::view()のテスト
 *
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Controller\TaskContentsController
 */
class TaskContentsControllerViewTest extends WorkflowControllerViewTest {

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
		'plugin.tasks.task',
		'plugin.tasks.task_charge',
		'plugin.tasks.task_content',
		'plugin.tasks.block_setting_for_task',
		'plugin.tasks.rooms_language_for_task',
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
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'task_contents';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$frameId = '6';
		$blockId = '2';
		$contentKey = 'content_key_1';

		$data = array(
			'action' => 'view',
			'frame_id' => $frameId,
			'block_id' => $blockId,
			'key' => $contentKey,
		);

		return $data;
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderView() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_1'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_2'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_5'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_999'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		// ブロックID取れないとき
		$data6 = $this->__data();
		$data6['block_id'] = 0; // 存在しない
		$data6['frame_id'] = null;
		$results[6] = array(
			'urlOptions' => Hash::insert($data6, 'key', 'content_key_999'),
			'assert' => array('method' => 'assertNull'),
		);
		$results[7] = array(
			'urlOptions' => Hash::insert($data, 'block_id', '10'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		// リマインダーメールを作成するとき
		$data8 = $this->__data();
		$data8['is_makeReminder'] = true;
		$results[8] = array(
			'urlOptions' => Hash::insert($data8, 'key', 'content_key_11'),
			'assert' => array('method' => 'assertNull'),
		);
		$results[9] = array(
			'urlOptions' => Hash::insert($data8, 'key', 'content_key_12'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * viewアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderView
 * @return void
 */
	public function testView($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testView($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		$this->__assertView($urlOptions['key'], false);
	}

/**
 * viewアクションのテスト（TaskSettingが取得できなかった場合）
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderView
 * @return void
 */
	public function testViewTaskSettingNotFound($urlOptions, $assert, $exception = null, $return = 'view') {
		$mockModel = 'Tasks.TaskSetting';
		$mockMethod = 'getTaskSetting';
		list($mockPlugin, $mockModel) = pluginSplit($mockModel);
		$this->controller->$mockModel = $this->getMockForModel(
			$mockPlugin . '.' . $mockModel,
			array($mockMethod),
			array('plugin' => 'Tasks')
		);
		//テスト実行
		parent::testView($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		$this->__assertView($urlOptions['key'], false);
	}

/**
 * viewアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByCreatable() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_1'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_5'),
			'assert' => null, 'exception' => 'BadRequestException'
		);
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_999'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * viewアクションのテスト(作成権限のみ)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewByCreatable
 * @return void
 */
	public function testViewByCreatable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testViewByCreatable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		if ($urlOptions['key'] === 'content_key_1') {
			$this->__assertView($urlOptions['key'], false);

		} elseif ($urlOptions['key'] === 'content_key_3') {
			$this->__assertView($urlOptions['key'], true);

		} elseif ($urlOptions['key'] === 'content_key_4') {
			$this->__assertView($urlOptions['key'], false);

		} else {
			$this->__assertView($urlOptions['key'], false);
		}
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByEditable() {
		$data = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_1'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[2] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[4] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_5'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[5] = array(
			'urlOptions' => Hash::insert($data, 'key', 'content_key_999'),
			'assert' => null, 'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * viewアクションのテスト(編集権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewByEditable
 * @return void
 */
	public function testViewByEditable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testViewByEditable($urlOptions, $assert, $exception, $return);
		if ($exception) {
			return;
		}

		//チェック
		$this->__assertView($urlOptions['key'], true);
	}

/**
 * view()のassert
 *
 * @param string $contentKey コンテンツキー
 * @param bool $isLatest 最終コンテンツかどうか
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	private function __assertView($contentKey, $isLatest = false) {
		if ($contentKey === 'content_key_1') {
			if ($isLatest) {
				//コンテンツのデータ(id=2, key=content_key_1)に対する期待値
				$this->assertTextContains('Title 2', $this->view);
			} else {
				//コンテンツのデータ(id=1, key=content_key_1)に対する期待値
				$this->assertTextContains('Title 1', $this->view);
			}

		} elseif ($contentKey === 'content_key_2') {
			//コンテンツのデータ(id=3, key=content_key_2)に対する期待値
			$this->assertTextContains('Title 3', $this->view);

		} elseif ($contentKey === 'content_key_3') {
			if ($isLatest) {
				//コンテンツのデータ(id=5, key=content_key_3)に対する期待値
				$this->assertTextContains('Title 5', $this->view);
			} else {
				//コンテンツのデータ(id=4, key=content_key_3)に対する期待値
				$this->assertTextContains('Title 4', $this->view);
			}

		} elseif ($contentKey === 'content_key_4') {
			if ($isLatest) {
				//コンテンツのデータ(id=7, key=content_key_4)に対する期待値
				$this->assertTextContains('Title 7', $this->view);
			} else {
				//コンテンツのデータ(id=6, key=content_key_4)に対する期待値
				$this->assertTextContains('Title 6', $this->view);
			}

		} elseif ($contentKey === 'content_key_5') {
			//コンテンツのデータ(id=8, key=content_key_5)に対する期待値
			$this->assertTextContains('Title 8', $this->view);
		}
	}

/**
 * test ContentComment
 *
 * @return void
 */
	public function testCommentPost() {
		// ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		// コメントPOST
		$data = [
			'TaskContent' => [
				'key' => 'content_key_1'
			]
		];

		$this->generateNc('Tasks.TaskContents', [
			'components' => [
				'ContentComments.ContentComments' => ['comment'],
			]
		]);

		// ContentCommentsComponent::comment()がコールされる
		$this->controller->ContentComments->expects($this->once())
			->method('comment')
			->with(
				$this->equalTo('tasks'),
				$this->equalTo('content_key_1'),
				$this->anything()
			)
			->will($this->returnValue(true));

		$taskSetting = new ReflectionProperty($this->controller, '_taskSetting');
		$taskSetting->setAccessible(true);
		$taskSetting->setValue($this->controller, ['TaskSetting' => ['use_comment' => true]]);

		$this->_testPostAction('post', $data, ['action' => 'view', 'key' => 'content_key_1', 'block_id' => 2]);

		TestAuthGeneral::logout($this);
	}

/**
 * test ContentComment
 *
 * @return void
 */
	public function testCommentPostFail() {
		// ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		// コメントPOST
		$data = [
			'TaskContent' => [
				'key' => 'content_key_1'
			]
		];

		$this->generateNc('Tasks.TaskContents', [
			'components' => [
				'ContentComments.ContentComments' => ['comment'],
			]
		]);

		// ContentCommentsComponent::comment()がコールされる
		$this->controller->ContentComments->expects($this->once())
			->method('comment')
			->with(
				$this->equalTo('tasks'),
				$this->equalTo('content_key_1'),
				$this->anything()
			)
			->will($this->returnValue(false)); // ContentCommentsComponent::comment()がfalseなら例外発生する

		$taskSetting = new ReflectionProperty($this->controller, '_taskSetting');
		$taskSetting->setAccessible(true);
		$taskSetting->setValue($this->controller, ['TaskSetting' => ['use_comment' => true]]);

		$this->setExpectedException('BadRequestException');

		$this->_testPostAction('post', $data, ['action' => 'view', 'key' => 'content_key_1', 'block_id' => 2]);

		TestAuthGeneral::logout($this);
	}
}
