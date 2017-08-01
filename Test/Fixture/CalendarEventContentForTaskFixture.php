<?php
/**
 * CalendarEventContentForTaskFixture
 */

App::uses('CalendarEventContentFixture', 'Calendars.Test/Fixture');

/**
 * Summary for CalendarEventContentFixture
 */
class CalendarEventContentForTaskFixture extends CalendarEventContentFixture {

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
	public $name = 'CalendarEventContents';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'calendar_event_contents';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'model' => 'calendarmodel',
			'content_key' => 'calendarplan1',
			'calendar_event_id' => 1,
			'created_user' => 1,
			'created' => '2016-03-24 07:09:51',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:09:51'
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
