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
abstract class GroupsTestBase extends NetCommonsControllerTestCase {

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
		'plugin.groups.user4_validation_test',
		'plugin.groups.roles_room4_groups_test',
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
 * コントローラのGroupモデル
 *
 * @var object
 */
	protected $_group;

/**
 * コントローラのGroupsUserモデル
 *
 * @var object
 */
	protected $_groupsUser;

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
				'exception' => 'BadRequestException'
			),
			array(
				'id' => null,
				'exception' => 'BadRequestException'
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
 * リダイレクト処理確認
 *
 * @param bool $isRedirect
 * @param string $errMessage
 * @return void
 */
	protected function _assertRedirect($isRedirect = 1, $errMessage = '') {
		if ($isRedirect) {
			$this->assertTextContains(
				'users/users/view/1#/user-groups',
				$this->headers['Location'],
				'リダイレクトがされていません。'
			);
		} else {
			$this->assertFalse(isset($this->headers['Location']), '表示されているページが違います');
			if (!empty($errMessage)) {
				$this->assertTextContains($errMessage, $this->view, 'エラーメッセージが表示されていません');
			}
		}
	}

/**
 * ログインしていない時の表示テスト
 *
 * @param string $methodName
 * @return void
 */
	protected function _assertNotLogin($methodName = '') {
		$result = $this->_testGetAction(
			array('action' => $methodName, 1),
			null,
			null,
			'view'
		);

		$this->assertNull($result, 'ログインしていないのにページが表示されています');
	}

/**
 * 削除処理のリンクが表示されているか否かを検証
 *
 * @param bool 削除処理のリンクが表示されるべきか否か
 * @return void
 */
	protected function _assertContainDeleteButton($isView = 1) {
		$deleteHTML = "削除処理";

		if ($isView) {
			$this->assertTextContains(
				$deleteHTML,
				$this->view,
				'削除処理が表示されていません'
			);
		} else {
			$this->assertTextNotContains(
				$deleteHTML,
				$this->view,
				'削除処理が表示されています'
			);
		}
	}

/**
 * elementの表示を取得
 *
 * @param string $path elementのパス
 * @param array $data Elementの変数
 * @param array $requestData リクエストdata
 * @return string 表示文字列
 */
	protected function _makeElementView($path, $data = [], $requestData = []) {
			$view = $this->_createViewClass($requestData);
			return $view->element($path, $data);
	}

/**
 * テストに使うViewクラスを作成
 *
 * @param array $requestData リクエストdata
 * @return object Viewクラス
 */
	protected function _createViewClass($requestData = []) {
		$this->controller->set('userAttributes', []);
		$this->controller->request->data = $requestData;
		$View = new View($this->controller);
		$View->Room = ClassRegistry::init('Rooms.Room');
		$View->plugin = Inflector::camelize($this->plugin);
		$View->helpers = $this->controller->helpers;
		$View->loadHelpers();
		return $View;
	}

/**
 * modifiedやcreatedカラムを除去する
 *
 * @param array $data データ
 * @return array
 */
	protected function _removeModified($data) {
		if (isset($data['created_user'])) {
			unset($data['created_user']);
		}
		if (isset($data['created'])) {
			unset($data['created']);
		}
		if (isset($data['modified_user'])) {
			unset($data['modified_user']);
		}
		if (isset($data['modified'])) {
			unset($data['modified']);
		}
		return $data;
	}

}
