<?php
/**
 * GroupsUser::getGroupUsers()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsModelTestBase', 'Groups.Test/Case');

/**
 * GroupsUser::getGroupUsers()のテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Model\GroupsUser
 */
class GroupsUserGetGroupUsersTest extends GroupsModelTestBase {

/**
 * getGroupUsers()のテスト
 *
 * @dataProvider dataProvidergetGroupUsers
 * @param array $inputData 入力データ
 * @return void
 */
	public function testGetGroupUsers($inputData = []) {
		$existUserIds = [1, 2];
		$expectedCount = 0;
		$getUsers = $this->_classGroupsUser->getGroupUsers($inputData);
		//何も渡していない時は空配列が返ってくる
		if (empty($inputData)) {
			$this->assertEquals(
				array(), $getUsers
			);
			return;
		}
		//データ内容確認
		if (!is_array($inputData)) {
			$inputData = [$inputData];
		}
		sort($inputData);
		$actualUsers = $getUsers;
		foreach ($inputData as $userId) {
			//データ数カウント
			if (in_array($userId, $existUserIds)) {
				++$expectedCount;
			} else {
				continue;
			}
			//データ詳細確認
			$expectedUser = $this->controller->User->findById($userId);
			$actualUser = array_shift($actualUsers);
			$this->assertEquals(
				$expectedUser['User'],
				$actualUser['User'],
				'データ内容が違います'
			);
			if (!isset($expectedUser['UploadFile'])) {
				foreach ($actualUser['UploadFile'] as $val) {
					$this->assertNull(
						$val,
						'データ内容が違います'
					);
				}
				continue;
			}
			$this->assertEquals(
				array_merge(
					$expectedUser['UploadFile']['avatar'],
					$expectedUser['UploadFile']
				),
				$actualUser['UploadFile'],
				'データ内容が違います'
			);
		}
		//データ数確認
		$this->assertCount(
			$expectedCount,
			$getUsers,
			'データ数が違います。'
		);
	}

/**
 * getGroupUsers()のテスト（異なるRoomId）
 *
 * @dataProvider dataProvidergetGroupUsers
 * @param array $inputData 入力データ
 * @return void
 */
	public function testGetGroupUsersDifferentRoomId($inputData = []) {
		$this->assertEmpty(
			$this->_classGroupsUser->getGroupUsers($inputData, 1236516)
		);
	}

/**
 * getGroupUsers()のテスト（RoomIdなし）
 *
 * @dataProvider dataProvidergetGroupUsers
 * @param array $inputData 入力データ
 * @return void
 */
	public function testGetGroupUsersNoRoomId($inputData = []) {
		$this->assertEmpty(
			$this->_classGroupsUser->getGroupUsers($inputData, null)
		);
	}

/**
 * testGetGroupUsers用dataProvider
 *
 * ### 戻り値
 *  - inputData:	入力データ
 *  - saveResult:	セーブ結果
 */
	public function dataProviderGetGroupUsers() {
		return array(
			array(null),
			array([]),
			array(1),
			array([2]),
			array([1, 2]),
			array([2, 1]),
			array([1, 999999]),
			array([5616516516, 1651651651])
		);
	}

}
