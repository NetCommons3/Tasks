<?php
/**
 * TaskCharge::getSelectChargeUsers()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * TaskCharge::getSelectChargeUsers()のテスト
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Model\TaskCharge
 */
class TaskChargeGetSelectChargeUsersTest extends NetCommonsGetTest {

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
	protected $_methodName = 'getSelectChargeUsers';

/**
 * テストデータ
 *
 * @return array
 */
	private function __data() {
		//データ生成
		return array(
			0 => array('TaskContents' => array(
				array('TaskCharge' => array(
					'1' => array(
						'user_id' => '1',
					),
				),
				)
			)
			),
		);
	}

/**
 * ユーザーIDが登録されていない場合のテストデータ
 *
 * @return array
 */
	private function __dataNoUser() {
		//データ生成
		return array(
			0 => array('TaskContents' => array(
				array('TaskCharge' => array(
					'100' => array(
						'user_id' => '100',
					),
				),
				)
			)
			),
		);
	}

/**
 * getSelectChargeUsers()のテスト
 *
 * @return void
 */
	public function testGetSelectChargeUsers() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$taskContents = $this->__data();

		//テスト実施
		$result = $this->$model->$methodName($taskContents);

		$userId = reset($this->__data()[0]['TaskContents'][0]['TaskCharge'])['user_id'];
		$expected = $result['TaskContents.charge_user_id_' . $this->__data()[0]['TaskContents'][0]['TaskCharge'][$userId]['user_id']]['user_id'];

		//チェック
		$this->assertNotEmpty($result);
		$this->assertEquals($userId, $expected);
	}

/**
 * getSelectChargeUsers() Nullのテスト
 *
 * @return void
 */
	public function testGetSelectChargeUsersNull() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$taskContents = array();

		//テスト実施
		$result = $this->$model->$methodName($taskContents);

		//チェック
		$this->assertEmpty($result);
	}

/**
 * getSelectChargeUsers() ユーザーIDが登録されていない場合のテスト
 *
 * @return void
 */
	public function testGetSelectChargeUsersNoUser() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$taskContents = $this->__dataNoUser();

		//テスト実施
		$result = $this->$model->$methodName($taskContents);

		//チェック
		$this->assertEmpty($result);
	}

}
