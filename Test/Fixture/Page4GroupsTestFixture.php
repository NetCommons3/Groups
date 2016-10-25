<?php
/**
 * Page4GroupsTestFixture
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for Page4GroupsTestFixture
 */
class Page4GroupsTestFixture extends PageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Page';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'pages';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '7',
			'room_id' => '5',
			'parent_id' => null,
			'lft' => 5,
			'rght' => 6,
			'permalink' => 'test2',
			'slug' => 'test2',
			//'is_published' => 1,
			//'from' => null,
			//'to' => null,
			'is_container_fluid' => 1,
		)
	);

}
