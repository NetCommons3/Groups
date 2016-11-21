<?php
/**
 * GroupUserListHelper::select()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');

/**
 * GroupUserListHelper::select()のテスト
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @package NetCommons\Groups\Test\Case\View\Helper\GroupUserListHelper
 */
class GroupUserListHelperSelectTest extends NetCommonsHelperTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'groups';

/**
 * select()のテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @return void
 */
	public function testSelect() {
		ClassRegistry::init('Rooms.Room');

		//Helperロード
		$this->loadHelper('Groups.GroupUserList');

		//データ生成
		$title = '';
		$pluginModel = 'GroupsUser';
		$roomId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);
		$selectUsers = array();

		//テスト実施
		$result = $this->GroupUserList->select($title, $pluginModel, $roomId, $selectUsers);

		//チェック
		$this->assertTextContains('group', $result);
	}
}
