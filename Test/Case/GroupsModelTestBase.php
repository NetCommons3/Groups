<?php
/**
 * Groups Modelのテストケース
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');
App::uses('GroupsTestBase', 'Groups.Test/Case');

/**
 * Groups Modelのテストケース
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Controller
 */
abstract class GroupsModelTestBase extends GroupsTestBase {

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
		$instance = Current::getInstance();
		$instance->initialize($this->controller);

		//コントローラ内モデル
		$this->_group = $this->controller->Group;
		$this->_groupsUser = $this->controller->GroupsUser;

		//テスト用モデルclass
		$this->_classGroup = ClassRegistry::init(Inflector::camelize($this->plugin) . '.Group');
		$this->_classGroupsUser = ClassRegistry::init(Inflector::camelize($this->plugin) . '.GroupsUser');
	}

/**
 * testValidates用dataProvider
 *
 * @param bool $errorNameEmpty グループ名NULLの際のバリデーション結果
 * ### 戻り値
 *  - inputData:	入力データ
 *  - expectedValidationErrors:	バリデーション結果
 */
	public function dataProviderValidates($errorNameEmpty = 0) {
		//ユーザ登録限界を作成
		$limitUserEntryArray = array();
		$limitUserEntryNum = GroupsUser::LIMIT_ENTRY_NUM;
		for ($i = 1; $i < $limitUserEntryNum + 2; ++$i) {
			$limitUserEntryArray[] = array(
				'user_id' => $i
			);
		}
		//グループ名NULLの際のバリデーション結果
		$resultNameEmpty = [];
		if ($errorNameEmpty === true) {
			$resultNameEmpty = [
				"name" => [ __d('groups', 'Please enter group name') ]
			];
		}

		return array(
			array(
				[
					'Group' => [ 'name' => 'test1' ],
					'GroupsUser' => [['user_id' => '1'], ['user_id' => '2']]
				],
				array()
			),
			array(
				[
					'Group' => [ 'name' => 'test1' ],
				],
				[
					'user_id' => [
						__d('groups', 'Select user')
					]
				]
			),
			array(
				[
					'Group' => [ 'name' => 'test1' ],
					'GroupsUser' => [['user_id' => '99999999']]
				],
				[
					'user_id' => [
						__d('net_commons', 'Failed on validation errors. Please check the input data.')
					]
				]
			),
			array(
				[
					'Group' => [ 'name' => 'test1' ],
					'GroupsUser' => $limitUserEntryArray
				],
				[
					'user_id' => [
						sprintf(__d('groups', 'Can be registered upper limit is %s'), $limitUserEntryNum)
					]
				]
			),
			array(
				[
					'Group' => [ 'name' => '' ],
					'GroupsUser' => [['user_id' => '4'], ['user_id' => '2']]
				],
				$resultNameEmpty
			),
		);
	}

/**
 * バリデーションテストの際の処理
 *
 * @param array $inputData 入力データ
 * @param array $validationErrors バリデーション結果
 * @param object $checkClass 確認するModelクラス
 * @param array $option
 * @return void
 */
	protected function _templateTestBeforeValidation($inputData, $validationErrors, $checkClass, $option = []) {
		$checkClass->set($inputData);
		$checkClass->validates();

		$this->assertEquals(
			$validationErrors,
			$checkClass->validationErrors,
			"バリデーション結果が違います"
		);
	}

/**
 * バリデーションテストの際の処理
 *
 * @param array $inputData 入力データ
 * @param array $validationErrors バリデーション結果
 * @param object $checkClass 確認するModelクラス
 * @param array $option
 * @return void
 */
	protected function _templateTestBeforeSave($inputData, $validationErrors, $checkClass, $option = []) {
		$checkClass->set($inputData);

		$this->assertEquals(
			$validationErrors,
			$checkClass->beforeSave(),
			"バリデーション結果が違います"
		);
	}

/**
 * GroupsUsersDetailの値を確認する
 *
 * @param int $groupId
 * @param array $detailGroupUsers
 * @return void
 */
	protected function _assertGroupsUsersDetail($groupId, $detailGroupUsers) {
		$expectedUserIds = $this->_getExpectedUserIds([$groupId]);
		$this->assertCount(
			count($expectedUserIds),
			$detailGroupUsers,
			'想定と違う値が返っています'
		);
		foreach ($expectedUserIds as $index => $userId) {
			$this->assertEquals(
				$this->controller->User->findById($userId)['User'],
				$detailGroupUsers[$index]['User'],
				'想定と違う値が返っています'
			);
		}
	}
}
