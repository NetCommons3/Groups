<?php
/**
 * GroupsController::users()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsControllerTestBase', 'Groups.Test/Case');

/**
 * GroupsController::users()のテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Controller\GroupsController
 */
class GroupsControllerUsersTest extends GroupsControllerTestBase {

/**
 * Fixtures Setting
 *
 * @param string $name
 * @param array $data
 * @param string $dataName
 * @var array
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		$this->fixtures = array_merge(
			$this->fixtures,
			[
				'plugin.groups.group4_users_test',
				'plugin.groups.groups_user4_users_test'
			]
		);

		parent::__construct($name, $data, $dataName);
	}

/**
 * users()アクションのGetリクエストテスト
 *
 * @dataProvider dataProviderUsersGet
 * @param $paramGroupId 対象グループID
 * @param $existUserData ユーザ情報が返ってくるか否か
 * @return void
 */
	public function testUsersGet($paramGroupId, $existUserData) {
		//ログイン
		TestAuthGeneral::login($this);

		$paramArray = null;
		if (is_array($paramGroupId)) {
			$paramArray = array_merge(
				$paramGroupId,
				['room_id' => '2']
			);
		}
		//テスト実行
		$this->_testGetAction(
			array('action' => 'users', '?' => $paramArray),
			array('method' => 'assertNotEmpty'),
			null,
			'view'
		);
		$this->__assertJson($existUserData);
		if (!$existUserData) {
			return;
		}
		$actualUsers = json_decode($this->view)->users;
		//取得予定のユーザ情報をフィクスチャから取得し、データ数を比較
		$expectedUserIds = $this->_getExpectedUserIds($paramGroupId);
		$this->assertCount(
			count($expectedUserIds),
			$actualUsers
		);
		//データ内容を検証
		foreach ($expectedUserIds as $index => $expectedUserId) {
			//取得予定ユーザ情報を取得
			$dbUserData = $this->controller->View->UserSearch->convertUserArrayByUserSelection(
				$this->controller->User->findById($expectedUserId),
				'User'
			);
			//jsonで取得したユーザ情報を検証
			$actualUser = (array)$actualUsers[$index];
			$this->assertCount(
				count($dbUserData),
				$actualUser
			);
			foreach (array_keys($dbUserData) as $key) {
				if ($key === 'avatar') {
					$this->assertTextContains(
						$dbUserData[$key],
						$actualUser[$key]
					);
				} else {
					$this->assertEquals(
						$dbUserData[$key],
						$actualUser[$key]
					);
				}
			}
		}
	}

/**
 * users()アクションのGetリクエストテスト(RoomIdなし)
 *
 * @return void
 */
	public function testUsersGetNoRoomId() {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$this->_testGetAction(
			array('action' => 'users', '?' => [ 'group_id' => '1,2'] ),
			array('method' => 'assertNotEmpty'),
			null,
			'view'
		);
		$this->__assertJson(false);
	}

/**
 * users()アクションのGetリクエストテスト(異なるRoomId)
 *
 * @return void
 */
	public function testUsersGetDifferentRoomId() {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$this->_testGetAction(
			array('action' => 'users', '?' => [ 'group_id' => '1,2', 'room_id' => 999999998] ),
			array('method' => 'assertNotEmpty'),
			null,
			'view'
		);
		$this->__assertJson(false);
	}

/**
 * users()アクションのGetリクエストテスト(ログインなし)
 *
 * @return void
 */
	public function testUsersGetNotLogin() {
		$this->_assertNotLogin('users');
	}

/**
 * testUsersGet用dataProvider
 *
 * ### 戻り値
 *  - groupIds : 対象グループID
 *  - existUserData:	ユーザ情報が返ってくるか否か
 */
	public function dataProviderUsersGet() {
		return array(
			[ [ 'group_id' => '1,2'], true ],
			[ [ 'group_id' => '2,1'], true ],
			[ [ 'group_id' => '3,1'], true ],
			[ [ 'group_id' => '2,4'], true ],
			[ [ 'group_id' => 1], true ],
			[ [ 'group_id' => 2], true ],
			[ null, false ],
			[ [ 'group_id' => null], false ],
			[ [ 'group_id' => 35444], false ],
			[ [ 'group_id' => 'ああああ'], false ],
			[ [ 'errorKey' => 2 ], false ],
		);
	}

/**
 * 返ってきたjsonの確認
 *
 * @param bool $existUserData ユーザ情報が存在するか否か
 * @return void
 */
	private function __assertJson($existUserData = 1) {
		$actualJson = json_decode($this->view);

		//Jsonの値を確認
		$this->assertEquals(
			'OK', $actualJson->name
		);
		$this->assertEquals(
			200, $actualJson->code
		);
		//Userデータを取得しない場合には空データ確認
		if (!$existUserData) {
			$this->assertEquals(
				[], $actualJson->users, 'データが空ではありません'
			);
		}
	}
}
