<?php
/**
 * Groupsのテストケース
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('GroupsUser4UsersTestFixture', 'Groups.Test/Fixture');
App::uses('GroupsUser', 'Groups.Model');


/**
 * Groupsのテストケース
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Controller
 */
class GroupsControllerTestCase extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'Group' => 'plugin.groups.group',
		'GroupsUser' => 'plugin.groups.groups_user',
		'plugin.groups.page4_groups_test',
		'plugin.groups.roles_rooms_user4_groups_test',
		'plugin.groups.room4_groups_test',
		'plugin.groups.user_attribute_layout4_groups_test',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'groups';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'groups';

/**
 * コントローラのグループモデル
 * 
 * @var object
 */
	protected $_group;

/**
 * GroupモデルClass
 * 
 * @var object
 */
	protected $_classGroup;

/**
 * GroupsUserモデルClass
 * 
 * @var object
 */
	protected $_classGroupsUser;

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

		$this->_group = $this->controller->Group;
		$this->_classGroup = ClassRegistry::init(Inflector::camelize($this->plugin) . '.Group');
		$this->_classGroupsUser = ClassRegistry::init(Inflector::camelize($this->plugin) . '.GroupsUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * 登録データ内容の確認
 *
 * @param $dbData	DBから取得したデータ
 * @param $inputData	入力したデータ
 * @param $expectedSaveResult	セーブ結果(想定)
 * @return void
 */
	protected function _assertGroupData($dbData, $inputData, $expectedSaveResult) {
		$isEdit = isset($inputData['Group']);
		$inputGroupUserData = isset($inputData['GroupsUser']) ? $inputData['GroupsUser'] : null;
		//登録データ詳細を取得
		$saveGroupsData = $dbData[0]['Group'];
		$saveGroupsUserData = $dbData[0]['GroupsUser'];
		//登録したユーザ数を確認
		$expectedGroupUserCnt = ($expectedSaveResult || $isEdit) ? count($inputGroupUserData) : 0;
		$this->assertCount($expectedGroupUserCnt, $saveGroupsUserData);
		//グループID・グループ名が正しく登録されているかを確認
		if ($isEdit) {
			$this->assertEquals($inputData['Group']['id'], $saveGroupsData['id']);
			$expectedUserName = $inputData['Group']['name'];
		} else {
			$expectedUserName = $inputData['name'];
		}
		$this->assertEquals($expectedUserName, $saveGroupsData['name']);
		//グループユーザが正しく登録されているかを確認
		$saveGroupId = $saveGroupsData['id'];
		foreach ($saveGroupsUserData as $index => $actualUserData) {
			$expectedUserId = $inputGroupUserData[$index]['user_id'];
			$actualUserId = $actualUserData['user_id'];
			$actualGroupId = $actualUserData['group_id'];
			$this->assertEquals($saveGroupId, $actualGroupId);
			$this->assertEquals($expectedUserId, $actualUserId);
		}
	}

/**
 * exceptionのエラーを返す
 * 
 * @param $exception
 */
	protected function _assertException($exception = null) {
			if (is_null($exception)) {
				return;
			}

			$errMessage = "Error:" . $exception->getCode() . "　" . $exception->getMessage() . "\r\n";
			$errMessage .= $exception->getFile() . "  Line:" . $exception->getLine() . "\r\n";
			//$errMessage .= "\r\n".$exception->getTraceAsString()."\r\n";

			$this->assertFalse(true, $errMessage);
	}

/**
 * paramにIDを入れるテストのdataProvider
 * 
 * ### 戻り値
 *  - id : ID
 *  - exception:	想定されるエラー
 */
	public function dataProviderParamId() {
		return array(
			array(
				'id' => 1,
				'exception' => null
			),
			array(
				'id' => 99,
				'exception' => 'NotFoundException'
			),
			array(
				'id' => null,
				'exception' => 'NotFoundException'
			)
		);
	}

/**
 * 取得予定のユーザ情報をフィクスチャから取得
 * 
 * @param $paramGroupId
 * @return array
 */
	protected function _getExpectedUserIds($paramGroupId) {
		$expectedUserIds = array();

		$groupUsers = new GroupsUser4UsersTestFixture();
		$expectedGroupIds = explode(',', array_pop($paramGroupId));
		foreach ($groupUsers->records as $record) {
			if (in_array($record['group_id'], $expectedGroupIds)) {
				$expectedUserIds[] = (int)$record['user_id'];
			}
		}

		sort($expectedUserIds);
		return array_values(array_unique($expectedUserIds));
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
		for ($i = 0; $i < $limitUserEntryNum + 2; ++$i) {
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
}