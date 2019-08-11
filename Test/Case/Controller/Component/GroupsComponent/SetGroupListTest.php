<?php
/**
 * GroupsComponent::setGroupList()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsTestBase', 'Groups.Test/Case');

/**
 * GroupsComponent::setGroupList()のテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\Controller\Component\GroupsComponent
 */
class GroupsComponentSetGroupListTest extends GroupsTestBase {

/**
 * UploadFileモデル名
 *
 * @var string
 */
	private $__modelUploadFile = 'UploadFile';

/**
 * Groupモデル名
 *
 * @var string
 */
	private $__modelGroup = 'Group';

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
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//ログイン
		TestAuthGeneral::login($this);
	}

/**
 * setGroupList()のテスト
 *
 * @dataProvider dataProviderSetGroupList
 * @param string $strGroupId
 * @return void
 */
	public function testSetGroupList($strGroupId = null) {
		$groupIds = explode(',', $strGroupId);
		$userIds = $this->_getExpectedUserIds([$strGroupId]);

		//テスト実行
		$instance = Current::getInstance();
		$instance->initialize($this->controller);

		$this->controller->Groups->setGroupList(
			$this->controller,
			$this->__createGroupCondition($groupIds)
		);
		//セットしたviewVarsに関する変数を設定
		$viewValueKeys = array(
			'users' => ['User', ['User', $this->__modelUploadFile ], $userIds],
			'groups' => ['Group', [$this->__modelGroup, 'GroupsUser'], $groupIds]
		);
		$viewValues = $this->controller->viewVars;
		//データ検証(全体)
		$this->assertCount(count($viewValueKeys), $viewValues);
		foreach ($viewValueKeys as $viewValueKey => $checkOption) {
			$this->assertArrayHasKey($viewValueKey, $viewValues);
			//データ検証（データ内容）
			if (empty($userIds)) {
				$this->assertEquals($viewValues[$viewValueKey], array());
			} else {
				$this->__validateViewValues($viewValues[$viewValueKey], $checkOption);
			}
		}
	}

/**
 * testSetGroupList用dataProvider
 *
 * ### 戻り値
 *  - strGroupId : データ検索するグループID
 */
	public function dataProviderSetGroupList() {
		return array(
			['1'], ['2'], ['3'], ['1,2'], ['2,1'],
		);
	}

/**
 * Groupモデルでの取得クエリに渡す条件配列を作成
 *
 * @param array $groupIds
 * @return array
 */
	private function __createGroupCondition($groupIds) {
		$orArray = array();
		foreach ($groupIds as $groupId) {
			$orArray[] = ['Group.id' => $groupId];
		}

		return array(
			'conditions' => [
				'or' => $orArray
			]
		);
	}

/**
 * セットした値が正しいかを確認
 *
 * @param array $actualDataArray　セットした値
 * @param array $checkOption 検索情報
 * @return void
 */
	private function __validateViewValues($actualDataArray, $checkOption) {
		$modelName = $checkOption[0];
		$checkKeys = $checkOption[1];
		$expectedIds = $checkOption[2];
		//データ数確認
		$this->assertCount(
			count($expectedIds),
			$actualDataArray
		);
		//データ詳細確認
		sort($expectedIds);
		foreach ($expectedIds as $index => $expectedId) {
			$actualData = $actualDataArray[$index];
			//データ項目数確認
			$this->assertCount(
				count($checkKeys),
				$actualData
			);
			//データ内容確認
			$dbData = $this->controller->$modelName->findById($expectedId);
			foreach ($checkKeys as $checkKey) {
				if ($checkKey === $this->__modelUploadFile) {
					if (!isset($dbData[$checkKey])) {
						continue;
					}
					$expectedData = array_merge(
						$dbData[$checkKey]['avatar'],
						$dbData[$checkKey]
					);
				} else {
					$expectedData = $dbData[$checkKey];
				}

				$expectedData = $this->_removeModified($expectedData);
				$actualData[$checkKey] = $this->_removeModified($actualData[$checkKey]);

				$this->assertEquals(
					$expectedData,
					$actualData[$checkKey]
				);
			}
		}
	}

}
