<?php
/**
 * Group Model
 *
 * @property Group $Group
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2016, NetCommons Project
 */

App::uses('GroupsAppModel', 'Groups.Model');

/**
 * Group Model
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
class Group extends GroupsAppModel {

/**
 * use tables
 *
 * @var string
 */
	public $useTable = 'groups';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * グループ名の入力上限文字数の定数
 *
 */
	const GROUP_NAME_MAX_LENGTH = 100;

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'GroupsUser' => array(
			'className' => 'Groups.GroupsUser',
			'foreignKey' => 'group_id',
			'dependent' => false
		)
	);

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Groups.GroupsUser'
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
		$this->validate = array(
			'name' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'required' => true,
					'allowEmpty' => false,
					'message' => __d('groups', 'Please enter group name'),
				),
				'maxLength' => array(
					'rule' => array('maxLength', Group::GROUP_NAME_MAX_LENGTH),
					'message' => sprintf(__d('groups', 'Please enter group name no more than %s characters'),
						Group::GROUP_NAME_MAX_LENGTH),
				),
			)
		);

		return parent::beforeValidate($options);
	}

/**
 * グループ及びグループユーザ一覧取得処理
 *
 * @param array $query find条件
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function getGroupList($query = array()) {
		$params = array(
			'fields' => array('Group.id', 'Group.name', 'Group.modified'),
			'conditions' => array('Group.created_user' => Current::read('User.id')),
			'order' => array('Group.created ASC'),
			'recursive' => 1,
		);
		$params = Hash::merge($params, $query);
		$groups = $this->find('all', $params);

		return $groups;
	}

/**
 * グループ取得処理
 *
 * @param int $groupId グループID
 * @return mixed On success Model::$group, false on failure
 * @throws InternalErrorException
 */
	public function getGroupById($groupId) {
		$options = array('conditions' => array('Group.' . $this->primaryKey => $groupId));
		$group = $this->find('first', $options);
		$userIdArr = Hash::extract($group, 'GroupsUser.{n}.user_id');
		$groupUsers = $this->GroupsUser->getGroupUsers($userIdArr);
		$group['GroupsUsersDetail'] = $groupUsers;

		return $group;
	}

/**
 * グループ取得処理
 *
 * @param int|array $groupId グループID
 * @param int $roomId ルームID
 * @return mixed On success Model::$groupUsers
 * @throws InternalErrorException
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function getGroupUser($groupId, $roomId = false) {
		if ($roomId === false) {
			$roomId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);
		}
		$groups = $this->find('all', array(
			'fields' => array('Group.id', 'Group.name', 'Group.modified'),
			'conditions' => array(
				'Group.' . $this->primaryKey => $groupId,
				'Group.created_user' => Current::read('User.id'),
			),
			'order' => array('Group.created ASC'),
			'recursive' => 1,
		));
		$userIdArr = Hash::extract($groups, '{n}.GroupsUser.{n}.user_id');
		$groupUsers = $this->GroupsUser->getGroupUsers($userIdArr, $roomId);

		return $groupUsers;
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
		// 更新の場合はグループの存在チェック
		if (isset($this->data['Group']['id']) && !empty($this->data['Group']['id'])) {
			$params = array(
				'conditions' => array(
					'Group.id' => $this->data['Group']['id'],
				),
				'fields' => 'Group.id',
				'recursive' => -1,
			);
			if ($this->find('count', $params) !== 1) {
				return false;
			}
		}

		return true;
	}

/**
 * グループの登録処理
 *
 * @param array $data data
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function saveGroup($data) {
		//トランザクションBegin
		$this->begin();

		$this->loadModels([
			'Group' => 'Groups.Group',
			'GroupsUser' => 'Groups.GroupsUser',
		]);

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			$this->rollback();
			return false;
		}

		try {
			// Groupデータの登録
			$fields = array('name');
			if (! $group = $this->save($data, false, $fields)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$groupId = $group['Group']['id'];

			// 更新処理の場合は一旦GroupsUser情報を削除
			if (isset($data['Group']['id']) && !empty($data['Group']['id'])) {
				$conditions = array(
					'GroupsUser.group_id' => $data['Group']['id']
				);
				$this->GroupsUser->deleteAll($conditions, false);
			}

			// GroupsUserデータの登録
			foreach ($data['GroupsUser'] as $groupUser) {
				$groupUser['group_id'] = $groupId;
				$groupUser = array('GroupsUser' => $groupUser);
				$this->GroupsUser->create(false);
				$this->GroupsUser->saveGroupUser(Hash::merge($group, $groupUser));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * グループ削除処理
 *
 * @param int $groupId グループID
 * @return bool True if delete operation should continue, false to abort
 * @throws InternalErrorException
 */
	public function deleteGroup($groupId) {
		$this->loadModels([
			'GroupsUser' => 'Groups.GroupsUser',
		]);

		//トランザクションBegin
		$this->begin();

		try {
			// グループ情報を削除
			$conditions = array('group_id' => $groupId);
			if (!$this->delete($groupId) || !$this->GroupsUser->deleteAll($conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * グループ編集権限判定処理
 *
 * @param int $groupId グループID
 * @return bool True if edit/delete operation should continue, false to abort
 * @throws InternalErrorException
 */
	public function canEdit($groupId) {
		$groupCnt = $this->find('count', array(
			'conditions' => array(
				'Group.id' => $groupId,
				'Group.created_user' => Current::read('User.id'),
			),
		));
		if ($groupCnt === 0) {
			return false;
		}
		return true;
	}
}
