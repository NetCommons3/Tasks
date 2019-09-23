<?php
/**
 * TaskBlockRolePermissionsController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BlockRolePermissionsControllerEditTest', 'Blocks.TestSuite');

/**
 * TaskBlockRolePermissionsController::edit()のテスト
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Controller\TaskBlockRolePermissionsController
 */
class TaskBlockRolePermissionsControllerEditTest extends BlockRolePermissionsControllerEditTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
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
	protected $_controller = 'task_block_role_permissions';

/**
 * 権限設定で使用するFieldsの取得
 *
 * @return array
 */
	private function __approvalFields() {
		$data = array(
			'TaskSetting' => array(
				'use_workflow',
				'use_comment_approval',
				'approval_type',
			)
		);

		return $data;
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$data = array(
			'TaskSetting' => array(
				'id' => 2,
				'task_key' => 'task_key_2',
				'use_workflow' => '1',
				'use_comment_approval' => '1',
				'approval_type' => '1',
			)
		);

		return $data;
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - approvalFields コンテンツ承認の利用有無のフィールド
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGet() {
		return array(
			array('approvalFields' => $this->__approvalFields())
		);
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - data POSTデータ
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditPost() {
		return array(
			array('data' => $this->__data())
		);
	}

/**
 * editアクションのGETテスト(Exceptionエラー)
 *
 * @param array $approvalFields コンテンツ承認の利用有無のフィールド
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditGet
 * @return void
 */
	public function testEditGetExceptionError($approvalFields, $exception = null, $return = 'view') {
		$this->_mockForReturnFalse('Tasks.Task', 'getTask');

		$exception = 'BadRequestException';
		$this->testEditGet($approvalFields, $exception, $return);
	}

/**
 * test Post でのsaveTaskSetting 失敗
 *
 * @param array $data saveデータ
 * @return void
 * @dataProvider dataProviderEditPost
 */
	public function testSaveTaskSettingFail($data) {
		$this->_mockForReturnFalse('Tasks.TaskSetting', 'saveTaskSetting');

		$result = $this->testEditPost($data, false, 'view');
		$approvalFields = $this->__approvalFields();
		$this->_assertEditGetPermission($approvalFields, $result);
	}

}
