<?php
/**
 * CalendarFrameSettingSelectRoomFixture
 */

App::uses('CalendarFrameSettingSelectRoomForTaskFixture', 'Calendars.Test/Fixture');

/**
 * Summary for CalendarFrameSettingSelectRoomFixture
 */
class CalendarFrameSettingSelectRoomForTaskFixture extends CalendarFrameSettingSelectRoomFixture {

/**
 * Plugin key
 *
 * @var string
 */
	public $pluginKey = 'calendars';

/**
 * Model name
 *
 * @var string
 */
	public $name = 'CalendarFrameSettingSelectRooms';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'calendar_frame_setting_select_rooms';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'calendar_frame_setting_id' => 1,
			'room_id' => '2',
			'created_user' => 1,
			'created' => '2016-03-24 07:10:11',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:10:11'
		),
		array(
			'id' => 2,
			'calendar_frame_setting_id' => 1,
			'room_id' => '3',
			'created_user' => 1,
			'created' => '2016-03-24 07:10:11',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:10:11'
		),
		array(
			'id' => 3,
			'calendar_frame_setting_id' => 1,
			'room_id' => '4',
			'created_user' => 1,
			'created' => '2016-03-24 07:10:11',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:10:11'
		),
		array(
			'id' => 4,
			'calendar_frame_setting_id' => 1,
			'room_id' => '5',
			'created_user' => 1,
			'created' => '2016-03-24 07:10:11',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:10:11'
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