<?php
/**
 * CalendarFrameSettingFixture
 */

App::uses('CalendarFrameSettingForTaskFixture', 'Calendars.Test/Fixture');

/**
 * Summary for CalendarFrameSettingFixture
 */
class CalendarFrameSettingForTaskFixture extends CalendarFrameSettingFixture {

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
	public $name = 'CalendarFrameSettings';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'calendar_frame_settings';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'frame_key' => 'frame_3',
			'display_type' => 1,
			'start_pos' => 1,
			'display_count' => 3,
			'is_myroom' => 1,
			'is_select_room' => 1,
			'room_id' => '2',
			'timeline_base_time' => 1,
			'created_user' => 1,
			'created' => '2016-03-24 07:10:18',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:10:18'
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
