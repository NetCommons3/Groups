<?php
/**
 * GroupsUser Model
 *
 * @property Group $Group
 * @property User $User
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kazutaka Yamada <yamada.kazutaka@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsAppModel', 'Groups.Model');

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

	public function saveGroupUser($data) {
		$this->begin();

		$this->set($data);
		if (!$this->validates()) {
			$this->rollback();
			return false;
		}

		try {
			if (!$this->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		} catch (Exception $ex) {
			$this->rollback($ex);
		}

		$this->commit();

		return true;
	}

	function getGroupUsers($id) {
		if (empty($id)) {
			return array();
		}

		$this->loadModels([
				'User' => 'Users.User',
				'UploadFile' => 'Files.UploadFile',
		]);
		$this->User->prepare();

		$groupDetail = $this->find('all', array(
				'recursive' => 0,
				'fields' => array('GroupsUser.*', 'User.*', 'UploadFile.*'),
				'conditions' => array(
						$this->alias . '.group_id' => $id,
						$this->User->alias . '.is_deleted' => false,
				),
				'joins' => array(
						array(
								'table' => $this->User->table,
								'alias' => $this->User->alias,
								'type' => 'INNER',
								'conditions' => array(
										$this->alias . '.user_id' . ' = ' . $this->User->alias . '.id',
								),
						),
						array(
								'table' => $this->UploadFile->table,
								'alias' => $this->UploadFile->alias,
								'type' => 'LEFT',
								'conditions' => array(
										$this->User->alias . '.id' . ' = ' . $this->UploadFile->alias . '.content_key',
										$this->UploadFile->alias . '.field_name' => User::$avatarField,
								),
						),
				),
				'order' => array($this->alias . '.created' => 'ASC'),
		));

		return $groupDetail;
	}





}
