<?php
/**
 * GroupUserList Helper
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2016, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * GroupUserList Helper
 *
 * @package NetCommons\Groups\View\Helper
 */
class GroupUserListHelper extends AppHelper {

/**
 * 使用するヘルパー
 * ただし、Roomヘルパーを使用する場合は、RoomComponentを呼び出している必要がある。
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
		'NetCommons.Date',
		'Rooms.Rooms',
		'Users.UserSearch'
	);

/**
 * UserAttributes data
 *
 * @var array
 */
	public $userAttributes;

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
	}

/**
 * ユーザ情報を画面表示用に変換する処理
 *
 * @param array $groupUsers ユーザ配列
 * @return array 画面表示用ユーザ配列
 */
	public function convertGroupUserListForDisplay($groupUsers) {
		$result = array();
		foreach ($groupUsers as $user) {
			if (! isset($user['User']['id'])) {
				continue;
			}
			$result[$user['User']['id']] = $this->UserSearch->convertUserArrayByUserSelection($user, 'User');
		}
		return $result;
	}

/**
 * ユーザ・グループ検索機能を提供します
 *
 * @param string $title 項目名として表示させる文字列
 * @param string $pluginModel モデル名
 * @param int $roomId ルームID
 * @param array $selectUsers 選択済みユーザ配列
 * @return string HTML tags
 */
	public function select($title = '', $pluginModel = 'GroupsUser', $roomId = null,
			$selectUsers = array()) {
		if (! isset($roomId)) {
			$roomId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);
		}
		if ($title === '') {
			$title = __d('groups', 'User select');
		}
		return $this->_View->element('Groups.select', array(
			'title' => $title,
			'pluginModel' => $pluginModel,
			'roomId' => $roomId,
			'selectUsers' => $selectUsers
		));
	}
}
