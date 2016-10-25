<?php
/**
 * RolesRoomsUser4GroupsTestFixture.php
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RolesRoomsUserFixture', 'Rooms.Test/Fixture');

/**
 * Summary for RolesRoomsUser4GroupsTestFixture
 */
class RolesRoomsUser4GroupsTestFixture extends RolesRoomsUserFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'RolesRoomsUser';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'roles_rooms_users';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'roles_room_id' => '1',
			'user_id' => '1',
			'room_id' => '2',
		),
		array(
			'id' => '2',
			'roles_room_id' => '2',
			'user_id' => '2',
			'room_id' => '2',
		),
		array(
			'id' => '3',
			'roles_room_id' => '9',
			'user_id' => '1',
			'room_id' => '8',
		),
		array(
			'id' => '4',
			'roles_room_id' => '9',
			'user_id' => '2',
			'room_id' => '5',
		),
		array(
			'id' => '5',
			'roles_room_id' => '1',
			'user_id' => '3',
			'room_id' => '2',
		),
		//コミュニティスペース
		array(
			'id' => '6',
			'roles_room_id' => '15',
			'user_id' => '1',
			'room_id' => '4',
		),
		array(
			'id' => '7',
			'roles_room_id' => '16',
			'user_id' => '2',
			'room_id' => '4',
		),
		array(
			'id' => '8',
			'roles_room_id' => '15',
			'user_id' => '3',
			'room_id' => '4',
		),

	);

}
