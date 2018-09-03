<?php
/**
 * TaskContentsController::index()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerIndexTest', 'Workflow.TestSuite');

/**
 * TaskContentsController::index()のテスト
 *
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Controller\TaskContentsController
 */
class TaskContentsControllerIndexTest extends WorkflowControllerIndexTest {

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
		'plugin.content_comments.content_comment'
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

		$data = array(
			'action' => 'index',
			'frame_id' => $frameId,
			'block_id' => $blockId,
		);

		return $data;
	}

/**
 * indexアクションのテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndex() {
		$data0 = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => $data0,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// ブロックID取れないとき
		$data1 = $this->__data();
		$data1['block_id'] = 0; // 存在しない
		$data1['frame_id'] = null;
		$results[1] = array(
			'urlOptions' => $data1,
			'assert' => array('method' => 'assertNull'),
		);

		// カテゴリIDによる絞り込み
		$data2 = $this->__data();
		$data2['category_id'] = 1;

		$results[2] = array(
			'urlOptions' => $data2,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// カテゴリIDがない場合
		$data3 = $this->__data();
		$data3['category_id'] = 100;

		$results[3] = array(
			'urlOptions' => $data3,
			'assert' => array(),
			'exception' => 'BadRequestException',
		);

		// ソート
		$data4 = $this->__data();
		$data4['sort'] = 'TaskContent.progress_rate';
		$data4['direction'] = 'asc';

		$results[4] = array(
			'urlOptions' => $data4,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// 完了・未完了の絞り込み
		$data5 = $this->__data();
		$data5['is_completion'] = 0;

		$results[5] = array(
			'urlOptions' => $data5,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		$data6 = $this->__data();
		$data6['is_completion'] = 'all';

		$results[6] = array(
			'urlOptions' => $data6,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		return $results;
	}

/**
 * indexアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndex
 * @return void
 */
	public function testIndex($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndex($urlOptions, $assert, $exception, $return);

		//チェック
		// ゲストなら追加ボタンはでない
		$this->assertTextNotContains(__d('tasks', 'ToDo Add'), $this->view);
	}

/**
 * indexアクションのテスト(作成権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByCreatable() {
		return array($this->dataProviderIndex()[0]);
	}

/**
 * indexアクションのテスト(作成権限のみ)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByCreatable
 * @return void
 */
	public function testIndexByCreatable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndexByCreatable($urlOptions, $assert, $exception, $return);

		//チェック
		$this->assertTextContains(__d('tasks', 'ToDo Add'), $this->view);
	}

/**
 * indexアクションのテスト(編集権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByEditable() {
		$data0 = $this->__data();

		//テストデータ
		$results = array();
		$results[0] = array(
			'urlOptions' => $data0,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// 担当者による絞り込み
		$data1 = $this->__data();
		$data1['user_id'] = 3;

		$results[1] = array(
			'urlOptions' => $data1,
			'assert' => array('method' => 'assertNotEmpty'),
		);

		// 担当者による絞り込み ユーザーIDがNullの場合
		$data2 = $this->__data();
		$data2['user_id'] = 100;

		$results[2] = array(
			'urlOptions' => $data2,
			'assert' => array('method' => 'assertNotEmpty'),
		);
		return $results;
	}

/**
 * indexアクションのテスト(編集権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByEditable
 * @return void
 */
	public function testIndexByEditable($urlOptions, $assert, $exception = null, $return = 'view') {
		//テスト実行
		parent::testIndexByEditable($urlOptions, $assert, $exception, $return);

		//チェック
		$this->assertTextContains(__d('tasks', 'ToDo Add'), $this->view);
	}

}
