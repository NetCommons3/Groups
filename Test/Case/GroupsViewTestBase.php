<?php
/**
 * Groups Viewのテストケース
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsTestBase', 'Groups.Test/Case');

/**
 * Groups Viewのテストケース
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\
 */
abstract class GroupsViewTestBase extends GroupsTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		//ログイン
		TestAuthGeneral::login($this);

		CakeSession::write('Auth.User.UserRoleSetting.use_private_room', true);
		Current::initialize($this->controller);

		//コントローラ内モデル
		$this->_group = $this->controller->Group;
		$this->_groupsUser = $this->controller->GroupsUser;

		//テスト用モデルclass
		$this->_classGroup = ClassRegistry::init(Inflector::camelize($this->plugin) . '.Group');
		$this->_classGroupsUser = ClassRegistry::init(Inflector::camelize($this->plugin) . '.GroupsUser');
	}

/**
 * phpmdエラー回避用メソッド
 *
 * @return void
 */
	public function testDummy() {
	}
}
