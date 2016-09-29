<?php
/**
 * Groups Controller
 *
 * @property Group $Group
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2016, NetCommons Project
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
		'M17n.SwitchLanguage',
		'UserAttributes.UserAttributeLayout',
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'add,edit,delete' => 'group_creatable',
			),
		),
		'Groups.Groups',
	);

/**
 * use helper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token',
		'Users.UserSearch',
		'Groups.GroupUserList',
	);

/**
 * select method
 *
 * @return void
 * @throws NotFoundException
 */
	public function select() {
		$this->viewClass = 'View';

		// グループ一覧取得・設定
		$this->Groups->setGroupList($this);
		$this->layout = 'NetCommons.modal';
	}

/**
 * users method
 *
 * @return void
 * @throws NotFoundException
 */
	public function users() {
		$this->viewClass = 'View';

		$groupIds = Hash::get($this->request->query, 'group_id');
		$roomId = Hash::get($this->request->query, 'room_id');
		$groupIdArr = explode(',', $groupIds);
		$groupUsers = array();
		if (!empty($groupIdArr)) {
			$groupUsers = $this->Group->getGroupUser($groupIdArr, $roomId);
		}
		$this->set('users', $groupUsers);
		$this->view = 'Groups.Groups/json/select';
	}

/**
 * add method
 *
 * @param bool $isModal モーダル表示の有無
 * @return void
 */
	public function add($isModal = null) {
		$this->view = 'edit';
		if ((int)$isModal) {
			$this->viewClass = 'View';
			$this->layout = 'NetCommons.modal';
		} else {
			$this->PageLayout = $this->Components->load('Pages.PageLayout');
		}

		if ($this->request->is('post')) {
			if (array_key_exists('cancel', $this->request->data)) {
				return $this->__redirect();
			}

			// 登録処理
			$group = $this->Group->saveGroup($this->request->data);
			if ($group) {
				// 正常の場合
				if ($isModal) {
					return;
				} else {
					$this->NetCommons->setFlashNotification(
						__d('net_commons', 'Successfully saved.'), array('class' => 'success')
					);
					return $this->__redirect();
				}
			} else {
				if (isset($this->request->data['GroupsUser'])) {
					$userIdArr = Hash::extract($this->request->data['GroupsUser'], '{n}.user_id');
					$users = $this->GroupsUser->getGroupUsers($userIdArr);
					$this->set('users', $users);
				}
			}
			$this->NetCommons->handleValidationError($this->Group->validationErrors);
		} else {
			$redirectUrl = $this->request->referer(true);
			$this->set('redirectUrl', $redirectUrl);
		}
		$this->set('isModal', $isModal);
	}

/**
 * edit method
 *
 * @param string $id groups.id
 * @return void
 * @throws NotFoundException
 */
	public function edit($id = null) {
		if (!$this->Group->canEdit($id)) {
			return $this->throwBadRequest();
		}
		$this->PageLayout = $this->Components->load('Pages.PageLayout');

		if ($this->request->is(array('post', 'put'))) {
			if (array_key_exists('cancel', $this->request->data)) {
				return $this->__redirect();
			}

			// 更新処理
			$data = $this->request->data;
			$data['Group']['id'] = $id;
			$group = $this->Group->saveGroup($data);
			if ($group) {
				// 正常の場合
				$this->NetCommons->setFlashNotification(
					__d('net_commons', 'Successfully saved.'), array('class' => 'success')
				);
				return $this->__redirect();
			} else {
				if (isset($this->request->data['GroupsUser'])) {
					$userIdArr = Hash::extract($this->request->data['GroupsUser'], '{n}.user_id');
					$users = $this->GroupsUser->getGroupUsers($userIdArr);
					$this->set('users', $users);
				}
			}
		} else {
			// グループユーザ詳細情報を取得
			$query = array(
				'conditions' => array(
					'Group.id' => $id
				)
			);
			$this->Groups->setGroupList($this, $query);
			$redirectUrl = $this->request->referer(true);
			$this->set('redirectUrl', $redirectUrl);
		}
		$isModal = 0;
		$this->set('isModal', $isModal);
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
		if (!$this->Group->canEdit($id)) {
			return $this->throwBadRequest();
		}
		$this->request->onlyAllow('delete');
		if ($this->Group->deleteGroup($id)) {
			// 正常の場合
			$this->NetCommons->setFlashNotification(
				__d('net_commons', 'Successfully saved.'), array('class' => 'success')
			);
		}
		return $this->__redirect();
	}

/**
 * グループ管理にリダイレクト処理
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @return mixed リダイレクト
 */
	private function __redirect() {
		$redirectUrl = Hash::get($this->request->data, '_user.redirect');
		$this->NetCommons->setAppendHtml(
			'<div class="hidden" ng-controller="Users.controller" ' .
				'ng-init="showUser(null, ' . Current::read('User.id') . ', \'tab=user-groups\')"></div>'
		);
		return $this->redirect($redirectUrl);
	}
}
