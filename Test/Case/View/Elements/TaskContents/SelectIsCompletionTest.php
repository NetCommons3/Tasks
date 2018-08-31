<?php
/**
 * View/Elements/TaskContents/select_is_completionのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('TaskContent', 'Tasks.Model');
App::uses('TasksComponent', 'Tasks.Controller/Component');

/**
 * View/Elements/TaskContents/select_is_completionのテスト
 *
 * @author Tomoyoshi Nakata <nakata.tomoyoshi@withone.co.jp>
 * @package NetCommons\Tasks\Test\Case\View\Elements\TaskContents\SelectIsCompletion
 */
class TasksViewElementsTaskContentsSelectIsCompletionTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'tasks';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Tasks', 'TestTasks');
		//テストコントローラ生成
		$this->generateNc('TestTasks.TestViewElementsTaskContentsSelectIsCompletion');
	}

/**
 * View/Elements/TaskContents/select_is_completionのテスト
 *
 * @return void
 */
	public function testSelectIsCompletion() {
		$this->controller->set('currentIsCompletion', 1);
		$this->controller->set('options', array(
			'TaskContents.is_completion.1' => array(
				'label' => 'aaa',
				'is_completion' => 1,
			)
		));
		//テスト実行
		$this->_testGetAction('/test_tasks/test_view_elements_task_contents_select_is_completion/select_is_completion',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/TaskContents/select_is_completion', '/') . '/';
		$this->assertRegExp($pattern, $this->view);
	}

/**
 * View/Elements/TaskContents/select_is_completionのテスト
 *
 * @return void
 */
	public function testSelectIsCompletionNull() {
		$this->controller->set('currentIsCompletion', '');
		$this->controller->set('options', array(
			'TaskContents.is_completion.0' => array(
				'label' => 'aaa',
				'is_completion' => 0,
			)
		));
		//テスト実行
		$this->_testGetAction('/test_tasks/test_view_elements_task_contents_select_is_completion/select_is_completion',
			array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/TaskContents/select_is_completion', '/') . '/';
		$this->assertRegExp($pattern, $this->view);
	}
}
