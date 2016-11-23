<?php
/**
 * Group::getGroupUser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsModelTestBase', 'Groups.Test/Case');

/**
 * Group::getGroupUser()のテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Model\Group
 */
class GroupGetGroupUserTest extends GroupsModelTestBase {

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
 * getGroupUser()のテスト
 *
 * @dataProvider dataProviderGroupUser
 * @param int|array $groupIds
 * @return void
 */
	public function testGetGroupUser($groupIds = [1, 2]) {
		$actualGroupUser = $this->_classGroup->getGroupUser($groupIds);

		if (!is_array($groupIds)) {
			$this->_assertGroupsUsersDetail(
				$groupIds, $actualGroupUser
			);
			return;
		}
		$expectedUserIds = $this->_getExpectedUserIds([implode(',', $groupIds)]);
		$this->assertCount(
			count($expectedUserIds),
			$actualGroupUser
		);
		foreach ($expectedUserIds as $index => $userId) {
			$this->assertEquals(
				$this->controller->User->findById($userId)['User'],
				$actualGroupUser[$index]['User'],
				'想定と違う値が返っています'
			);
		}
	}

/**
 * testGetGroupUser用dataProvider
 *
 * ### 戻り値
 *  - id:	グループID
 */
	public function dataProviderGroupUser() {
		return array(
			[null],
			[],
			[1],
			[2],
			[[1, 2]],
			[[1, 9999]],
		);
	}
}
