<?php
/**
 * Groups Modelのテストケース
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');
App::uses('GroupsTestBase', 'Groups.Test/Case');

/**
 * Groups Modelのテストケース
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Groups\Test\Case\Controller
 */
abstract class GroupsControllerTestBase extends GroupsTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//コントローラ内モデル
		$this->_group = $this->controller->Group;
		$this->_groupsUser = $this->controller->GroupsUser;
	}
}
