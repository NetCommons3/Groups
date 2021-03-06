<?php
/**
 * GroupsController::delete()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsControllerTestBase', 'Groups.Test/Case');

/**
 * GroupsController::delete()のテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Controller\GroupsController
 */
class GroupsControllerDeleteTest extends GroupsControllerTestBase {

/**
 * delete()アクションのGetリクエストテスト
 *
 * @dataProvider dataProviderParamId
 * @param $id ID
 * @param $exception	想定されるエラー
 * @return void
 */
	public function testDeleteGet($id, $exception) {
		//ログイン
		TestAuthGeneral::login($this);

		$this->__testNotAllowDelete('get', $id, $exception);
	}

/**
 * delete()アクションのPutリクエストテスト
 *
 * @dataProvider dataProviderParamId
 * @param $id ID
 * @param $exception	想定されるエラー
 * @return void
 */
	public function testDeletePut($id, $exception) {
		//ログイン
		TestAuthGeneral::login($this);

		$this->__testNotAllowDelete('put', $id, $exception);
	}

/**
 * delete()アクションのPostリクエストテスト
 *
 * @dataProvider dataProviderParamId
 * @param $id ID
 * @param $exception	想定されるエラー
 * @return void
 */
	public function testDeletePost($id, $exception) {
		//ログイン
		TestAuthGeneral::login($this);

		$this->__testNotAllowDelete('post', $id, $exception);
	}

/**
 * delete()アクションのDeleteリクエストテスト
 *
 * @dataProvider dataProviderParamId
 * @param $id ID
 * @param $exception	想定されるエラー
 * @return void
 */
	public function testDeleteDelete($id, $exception) {
		//ログイン
		TestAuthGeneral::login($this);

		$this->__testAllowDelete('delete', $id, $exception);
	}

/**
 *　データ削除テスト(ログインなし)
 *
 * @return void
 */
	public function testDeleteNotLogin() {
		$this->__testNotAllowDelete('delete', null, 'Exception');
	}

/**
 * delete()アクションの許可されているRESTのテスト
 *
 * @param $rest REST
 * @param $id ID
 * @param $exception	想定されるエラー
 * @return void
 */
	private function __testAllowDelete($rest, $id, $exception) {
		$this->__testDeleteAction($rest, $id, $exception);
		$this->_assertRedirect(true);
		//データが削除されているかを確認
		$this->assertCount(0, $this->_group->find('all'));
		$this->assertCount(0, $this->controller->GroupsUser->find('all'));
	}

/**
 * delete()アクションの許可されていないRESTのテスト
 *
 * @param $rest REST
 * @param $id ID
 * @param $exception	想定されるエラー
 * @return void
 */
	private function __testNotAllowDelete($rest, $id, $exception) {
		//存在するIDが入力されていた場合にはonlyAllowでエラーが出る
		if (is_null($exception)) {
			$exception = 'Exception';
		}
		$this->__testDeleteAction($rest, $id, $exception);
	}

/**
 * deleteテスト実行
 *
 * @param $rest REST
 * @param $id ID
 * @param $exception	想定されるエラー
 * @return void
 */
	private function __testDeleteAction($rest, $id, $exception) {
		$this->_testNcAction(
			array(
				'plugin' => $this->plugin,
				'controller' => $this->_controller,
				'action' => 'delete',
				$id
			),
			['method' => $rest, 'data' => ['_user' => ['redirect' => 'users/users/view/1#/user-groups']]],
			$exception,
			'view'
		);
	}

}
