<?php
/**
 * CalendarEventShareUserFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for CalendarEventShareUserFixture
 */
class CalendarEventShareUserFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'calendar_event_id' => 1,
			'share_user' => 1,
			'created_user' => 1,
			'created' => '2016-03-24 07:09:58',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:09:58'
		),
		array(
			'id' => 2,
			'calendar_event_id' => 27,
			'share_user' => 3, //編集長と共有
			'created_user' => 1,
			'created' => '2016-03-24 07:09:58',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:09:58'
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Calendars') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new CalendarsSchema())->tables[Inflector::tableize($this->name)];
		parent::init();
	}

}
