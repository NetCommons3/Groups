<?php
/**
 * GroupsUser Model
 *
 * @property Group $Group
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2016, NetCommons Project
 */

App::uses('GroupsAppModel', 'Groups.Model');
App::uses('Room', 'Rooms.Model');
class_exists('Room'); //autoloaderのために必要

/**
 * GroupsUser Model
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
class GroupsUser extends GroupsAppModel {

/**
 * use tables
 *
 * @var string
 */
	public $useTable = 'groups_users';

/**
 * 一覧表示するユーザアイコン数の定数
 *
 */
	const LIST_DISPLAY_NUM = 10;

/**
 * 1グループに登録可能な人数の定数
 *
 */
	const LIMIT_ENTRY_NUM = 100;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Groups.Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->loadModels([
			'User' => 'Users.User',
		]);

		// ユーザ選択チェック
		if (! isset($this->data['GroupsUser']) || count($this->data['GroupsUser']) === 0) {
			$this->validationErrors['user_id'][] = __d('groups', 'Select user');
			return false;
		}

		// ユーザ存在チェック
		$userIdArr = Hash::extract($this->data['GroupsUser'], '{n}.user_id');
		if (! $this->User->existsUser($userIdArr)) {
			$this->validationErrors['user_id'][] =
				sprintf(__d('net_commons', 'Failed on validation errors. Please check the input data.'));
			return false;
		}

		// ユーザ選択上限チェック
		if (count($this->data['GroupsUser']) > GroupsUser::LIMIT_ENTRY_NUM) {
			$this->validationErrors['user_id'][] =
				sprintf(__d('groups', 'Can be registered upper limit is %s'), GroupsUser::LIMIT_ENTRY_NUM);
			return false;
		}

		return parent::beforeValidate($options);
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = array()) {
		// グループIDの妥当性チェック
		$groupId = Hash::get($this->data, 'Group.id');
		$groupsUserGroupId = Hash::get($this->data, 'GroupsUser.group_id');
		if (empty($groupsUserGroupId) || $groupId != $groupsUserGroupId) {
			return false;
		}

		// グループユーザの妥当性チェック
		$this->loadModels([
			'User' => 'Users.User',
		]);
		$groupsUserIdArr = Hash::extract($this->data, 'GroupsUser.{n}.user_id');
		if (! $this->User->existsUser($groupsUserIdArr)) {
			return false;
		}

		return true;
	}

/**
 * Register the string attached user information to the group
 *
 * @param mixed $data Groups users data
 * @throws InternalErrorException
 * @return bool Success
 */
	public function saveGroupUser($data) {
		$this->begin();

		try {
			if (! $this->save($data, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		} catch (Exception $ex) {
			$this->rollback($ex);
		}

		$this->commit();

		return true;
	}

/**
 * It gets a string attached user information to the group
 *
 * @param array $userIdArr GroupsUser.user_id
 * @param int $roomId Room.id
 * @return array Group users array
 */
	public function getGroupUsers($userIdArr, $roomId = null) {
		if (! isset($roomId)) {
			$roomId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);
		}
		if (empty($userIdArr)) {
			return array();
		}

		$this->loadModels([
			'User' => 'Users.User',
			'UploadFile' => 'Files.UploadFile',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
		]);
		$this->User->prepare();

		$rolesRoomsUsers = $this->RolesRoomsUser->getRolesRoomsUsers(array(
			'Room.id' => $roomId,
			'RolesRoomsUser.user_id' => $userIdArr
		));
		$roomUserIdArr = Hash::extract($rolesRoomsUsers, '{n}.RolesRoomsUser.user_id');
		if (empty($roomUserIdArr)) {
			return array();
		}

		$groupUsers = $this->User->find('all', array(
			'recursive' => -1,
			'fields' => array('User.*', 'UploadFile.*'),
			'conditions' => array(
				$this->User->alias . '.id' => $roomUserIdArr,
				$this->User->alias . '.is_deleted' => false,
			),
			'joins' => array(
				array(
					'table' => $this->UploadFile->table,
					'alias' => $this->UploadFile->alias,
					'type' => 'LEFT',
					'conditions' => array(
						$this->User->alias . '.id' . ' = ' . $this->UploadFile->alias . '.content_key',
						$this->UploadFile->alias . '.field_name' => UserAttribute::AVATAR_FIELD,
					),
				),
			),
		));

		return $groupUsers;
	}
}
