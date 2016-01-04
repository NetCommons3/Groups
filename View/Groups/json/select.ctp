<?php
/**
 * 検索結果出力JSON
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

$results['users'] = array();
if (!empty($users)) {
	foreach ($users as $user) {
		$result = $this->UserSearch->convertUserArrayByUserSelection($user, 'User');
		$results['users'][] = $result;
	}
}
echo $this->NetCommonsHtml->json($results);
