<?php
/**
 * GroupsController::add()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsControllerTestBase', 'Groups.Test/Case');

/**
 * GroupsController::add()のテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Controller\GroupsController
 */
class GroupsControllerAddTest extends GroupsControllerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * add()アクションのPostリクエストテスト
 *
 * @dataProvider dataProviderAddPost
 * @param $isModal	モーダル表示の有無
 * @param $inputData	入力するデータ
 * @param $expectedSaveResult	セーブ結果(想定)
 * @param $errMessage　画面に表示されるエラーメッセージ
 * @return void
 */
	public function testAddPost($isModal = null, $inputData = [], $expectedSaveResult = 1, $errMessage = '') {
		//ログイン
		TestAuthGeneral::login($this);

		//データを全削除
		$this->_group->deleteAll(true);
		//モーダルウィンドウで登録に成功する場合はモーダルが閉じるので設定する必要はないのだが、テストでエラーが出るため対処
		if ($expectedSaveResult && $isModal) {
			$this->controller->viewVars['isModal'] = null;
		}
		//データ登録
		$this->_group->data['Group'] = array();
		try {
			$this->_testPostAction(
				'post',
				$inputData,
				array('action' => 'add', $isModal),
				null,
				'view'
			);
		} catch(exception $e){
			$this->_assertException($e);
		}

		$dbData = $this->_group->find('all');
		//登録データ数を確認
		$expectedCount = $expectedSaveResult ? 1 : 0;
		$this->assertCount($expectedCount, $dbData);
		//表示ページ確認
		$this->_assertRedirect($expectedSaveResult && !$isModal, $errMessage);
		//登録データ内容の確認
		if ($expectedSaveResult) {
			$this->_assertGroupData($dbData, $inputData, $expectedSaveResult);
		}
	}

/**
 * add()アクションのCancelリクエストテスト
 *
 * @return void
 */
	public function testAddCancel() {
		//ログイン
		TestAuthGeneral::login($this);

		//データ登録
		$this->_testPostAction(
			'post',
			array(
				'cancel' => null,
				'name' => 'test1',
				'GroupsUser' => [['user_id' => '1'], ['user_id' => '2']],
				'_user' => ['redirect' => 'users/users/view/1#/user-groups']
			),
			array('action' => 'add'),
			null,
			'view'
		);

		//表示ページ確認
		$this->_assertRedirect(true);
	}

/**
 * add()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testAddGet() {
		//ログイン
		TestAuthGeneral::login($this);

		$this->_testGetAction(
			array('action' => 'add'),
			array('method' => 'assertNotEmpty'),
			null,
			'view'
		);
		$this->_assertContainDeleteButton(false);
	}

/**
 * add()アクションのGetリクエストテスト(ログインなし)
 *
 * @return void
 */
	public function testAddGetNotLogin() {
		$result = $this->_testGetAction(
			array('action' => 'add'),
			array('method' => 'assertNotEmpty'),
			'ForbiddenException',
			'view'
		);
		$this->assertNotEmpty($result);
	}

/**
 * testAddPost用dataProvider
 *
 * ### 戻り値
 *  - isModal:	モーダル表示の有無
 *  - inputData:	入力データ
 *  - expectedSaveResult:	セーブ結果
 */
	public function dataProviderAddPost() {
		return array(
			array(
				'isModal' => false,
				'inputData' => [
					'name' => 'test1',
					'GroupsUser' => [['user_id' => '1'], ['user_id' => '2']],
					'_user' => ['redirect' => 'users/users/view/1#/user-groups']
				],
				'expectedSaveResult' => true,
			),
			array(
				'isModal' => false,
				'inputData' => [
					'name' => 'test2',
					'GroupsUser' => [['user_id' => '3'], ['user_id' => '1']],
					'_user' => ['redirect' => 'users/users/view/1#/user-groups']
				],
				'expectedSaveResult' => true,
			),
			array(
				'isModal' => false,
				'inputData' => [
					'name' => 'test3',
					'_user' => ['redirect' => 'users/users/view/1#/user-groups']
				],
				'expectedSaveResult' => false,
				'errMessage' => 'ユーザを選択してください。',
			),
			array(
				'isModal' => false,
				'inputData' => [
					'GroupsUser' => [['user_id' => '1']],
					'_user' => ['redirect' => 'users/users/view/1#/user-groups']
				],
				'expectedSaveResult' => false,
				'errMessage' => 'グループ名を入力してください。',
			),
			array(
				'isModal' => true,
				'inputData' => [
					'name' => '',
					'GroupsUser' => [['user_id' => '1'], ['user_id' => '3'], ['user_id' => '4'], ['user_id' => '2'], ['user_id' => '5']],
					'_user' => ['redirect' => 'users/users/view/1#/user-groups']
				],
				'expectedSaveResult' => false,
				'errMessage' => 'グループ名を入力してください。',
			),
			array(
				'isModal' => true,
				'inputData' => [
					'name' => 'test5',
					'GroupsUser' => [['user_id' => '4']],
					'_user' => ['redirect' => 'users/users/view/1#/user-groups']
				],
				'expectedSaveResult' => true,
			),
			array(
				'isModal' => true,
				'inputData' => [
					'name' => 'test5',
					'GroupsUser' => [['user_id' => '3'], ['user_id' => '99999']],
					'_user' => ['redirect' => 'users/users/view/1#/user-groups']
				],
				'expectedSaveResult' => false,
				'errMessage' => 'ユーザを選択してください。',
			),
		);
	}

}
