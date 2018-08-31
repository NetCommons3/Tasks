<?php
/**
 * TaskContent::updateProgressRate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * TaskContent::updateProgressRate()のテスト
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\Model\TaskContent
 */
class TaskContentUpdateProgressRateTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'updateProgressRate';

/**
 * testUpdateProgressRateのDataProvider
 *
 * ### 戻り値
 *  - key Contentキー
 *  - progressRate 進捗率
 *
 * @return array
 */
	public function dataProviderUpdateProgressRate() {
		return array(
			array(
				'key' => 'content_key_1',
				'progressRate' => 0,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 10,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 20,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 30,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 40,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 50,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 60,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 70,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 80,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 90,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 100,
			),
		);
	}

/**
 * UpdateProgressRate()のUpdatesテスト
 * @param $key
 * @param $progressRate
 * @dataProvider dataProviderUpdateProgressRate
 * @return void
 */
	public function testUpdateProgressRate($key, $progressRate) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		Current::$current['User']['id'] = 1;	// ログイン

		//テスト実施
		$result = $this->$model->$methodName($key, $progressRate);

		//チェック
		$this->assertTrue($result);
	}

/**
 * testUpdateProgressRateのDataProvider
 *
 * ### 戻り値
 *  - key Contentキー
 *  - progressRate 進捗率
 *
 * @return array
 */
	public function dataProviderUpdateProgressRateUserFalse() {
		return array(
			array(
				'key' => 'content_key_2',
				'progressRate' => 0,
			),
			array(
				'key' => 'content_key_3',
				'progressRate' => 0,
			),
		);
	}

/**
 * UpdateProgressRateUserFalse()のUpdatesテスト
 * @param $key
 * @param $progressRate
 * @dataProvider dataProviderUpdateProgressRateUserFalse
 * @return void
 */
	public function testUpdateProgressRateUserFalse($key, $progressRate) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		Current::$current['User']['id'] = 1;	// ログイン

		//テスト実施
		$result = $this->$model->$methodName($key, $progressRate);

		//チェック
		$this->assertFalse($result);
	}

/**
 * testUpdateProgressRateのDataProvider
 *
 * ### 戻り値
 *  - key Contentキー
 *  - progressRate 進捗率
 *
 * @return array
 */
	public function dataProviderUpdateProgressRateValidateFalse() {
		return array(
			array(
				'key' => 'content_key_1',
				'progressRate' => null,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 'value',
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 73,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => 101,
			),
			array(
				'key' => 'content_key_1',
				'progressRate' => '',
			),
		);
	}

/**
 * UpdateProgressRateUserFalse()のUpdatesテスト
 * @param $key
 * @param $progressRate
 * @dataProvider dataProviderUpdateProgressRateValidateFalse
 * @return void
 */
	public function testUpdateProgressRateValidateFalse($key, $progressRate) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		Current::$current['User']['id'] = 1;	// ログイン

		//テスト実施
		$result = $this->$model->$methodName($key, $progressRate);

		//チェック
		$this->assertFalse($result);
	}

/**
 * testUpdateProgressRateのDataProvider
 *
 * ### 戻り値
 *  - key Contentキー
 *  - progressRate 進捗率
 *
 * @return array
 */
	public function dataProviderUpdateProgressRateException() {
		return array(
			array(
				'key' => 'content_key_1',
				'progressRate' => 10,
			),
		);
	}

/**
 * UpdateProgressRateException()のUpdatesテスト
 * @param $key
 * @param $progressRate
 * @dataProvider dataProviderUpdateProgressRateException
 * @return void
 */
	public function testUpdateProgressRateException($key, $progressRate) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		Current::$current['User']['id'] = 1;	// ログイン

		$this->setExpectedException('InternalErrorException');
		$this->_mockForReturnFalse($model, 'TaskContent', 'updateAll');

		//テスト実施
		$this->$model->$methodName($key, $progressRate);
	}

}
