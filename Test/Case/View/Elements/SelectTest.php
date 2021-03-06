<?php
/**
 * View/Elements/selectのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsViewTestBase', 'Groups.Test/Case');

/**
 * View/Elements/selectのテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\View\Elements\Select
 */
class GroupsViewElementsSelectTest extends GroupsViewTestBase {

/**
 * View/Elements/selectのテスト
 *
 * @return void
 */
	public function testSelect() {
		$this->_makeElementView(
			'Groups.select',
			[
				'selectUsers' => [
					['User' => ['id' => 1]],
					['User' => ['id' => 2]],
				]
			]
		);
	}

}
