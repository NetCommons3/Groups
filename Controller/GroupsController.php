<?php
/**
 * Groups Controller
 *
 * @property Group $Group
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2015, NetCommons Project
 */

App::uses('GroupsAppController', 'Groups.Controller');

/**
 * Groups Controller
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
class GroupsController extends GroupsAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Groups.Group',
		'Groups.GroupsUser',
		'Users.User',
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
//		'ControlPanel.ControlPanelLayout',
//		'Files.FileUpload',
		'M17n.SwitchLanguage',
		'UserAttributes.UserAttributeLayout',
//		'Users.UserSearch',
	);

	public $helpers = array(
//		'NetCommons.Token',
		'Users.UserSearch'
	);

/**
 * index method
 *
 * @return void
 */
	public function index() {

		// TODO 取得方法変更（アバター表示）
		// グループ一覧取得
		$groups = $this->Group->find('all',array(
			'fields' => array('Group.id', 'Group.name', 'Group.modified'),
			'conditions' => array('Group.created_user' => Current::read('User.id')),
			'order' => array('Group.created ASC'),
			'recursive' => -1,
		));
		$this->set('groups', $groups);
	}

/**
 * view method
 *
 * @param string $id groups.id
 * @return void
 * @throws NotFoundException
 */
	public function view($id = null) {
		if (!$this->Group->exists($id)) {
			throw new NotFoundException(__('Invalid group'));
		}
		$options = array('conditions' => array('Group.' . $this->Group->primaryKey => $id));
		$this->set('group', $this->Group->find('first', $options));
	}

/**
 * view method
 *
 * @return void
 * @throws NotFoundException
 */
	public function select() {
		$this->viewClass = 'View';

		if ($this->request->isPost()) {
			$data = array_map(function ($groupId) {
				return $groupId;
			}, $this->request->data['GroupSelect']['group_id']);
			if (!empty($data)) {
				$groupUsers = array();
				foreach ($data as $groupId) {
					$groupUserData = $this->GroupsUser->getGroupUsers($groupId);
					$groupUsers = array_merge_recursive($groupUsers, $groupUserData);
				}
			}
			$this->set('users', $groupUsers);
			$this->view = 'Groups.Groups/json/select';
		} else {
			// TODO 取得方法変更（アバター表示）
			// グループ一覧取得
			$groups = $this->Group->find('all',array(
				'fields' => array('Group.id', 'Group.name', 'Group.modified'),
				'conditions' => array('Group.created_user' => Current::read('User.id')),
				'order' => array('Group.created ASC'),
				'recursive' => -1,
			));
			$this->set('groupList', $groups);
			$this->layout = 'NetCommons.modal';
		}
	}

	/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->view = 'edit';

		if ($this->request->isPost()) {
			// 登録処理
			$group = $this->Group->saveGroup($this->request->data);
			if ($group) {
				// 正常の場合
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array('class' => 'success'));
				$this->redirect('/groups/groups/index/');
				return;
			}
		}
	}

/**
 * edit method
 *
 * @param string $id groups.id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null) {
		if (!$this->Group->exists($id)) {
			throw new NotFoundException(__('Invalid group'));
		}
		if ($this->request->is(array('post', 'put'))) {

			// 更新処理
			$data = $this->request->data;
			$data['Group']['id'] = $id;
			$group = $this->Group->saveGroup($data);
			if ($group) {
				// 正常の場合
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array('class' => 'success'));
				$this->redirect('/groups/groups/index/');
				return;
			}
		} else {
			$options = array('conditions' => array('Group.' . $this->Group->primaryKey => $id));
			$this->request->data = $this->Group->find('first', $options);
			// グループユーザ詳細情報を取得
			$groupUsers = $this->GroupsUser->getGroupUsers($id);
			$this->request->data['GroupsUsersDetail'] = $groupUsers;
		}
	}

/**
 * delete method
 *
 * @param string $id groups.id
 * @return void
 * @throws NotFoundException
 */
	public function delete($id = null) {

		$this->Group->id = $id;
		if (!$this->Group->exists()) {
			throw new NotFoundException(__('Invalid group'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Group->delete()) {
			// 正常の場合
			$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array('class' => 'success'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
