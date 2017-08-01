<?php
/**
 * CalendarFixture
 */

App::uses('CalendarForTaskFixture', 'Calendars.Test/Fixture');

/**
 * Summary for CalendarFixture
 */
class CalendarForTaskFixture extends CalendarFixture {

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
	public $name = 'Calendars';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'calendars';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'block_key' => 'block_1',
			'created_user' => 1,
			'created' => '2016-03-24 07:10:30',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:10:30'
		),
		array(
			'id' => 2,
			'block_key' => 'block_1',
			'created_user' => 1,
			'created' => '2016-03-24 07:10:30',
			'modified_user' => 1,
			'modified' => '2016-03-24 07:10:30'
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
