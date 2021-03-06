<?php
/**
 * View/Elements/delete_formのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsViewTestBase', 'Groups.Test/Case');

/**
 * View/Elements/delete_formのテスト
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\View\Elements\DeleteForm
 */
class GroupsViewElementsDeleteFormTest extends GroupsViewTestBase {

/**
 * View/Elements/delete_formのテスト
 *
 * @return void
 */
	public function testDeleteForm() {
		$this->_makeElementView(
			'Groups.delete_form',
			[],
			array (
				'Group' => ['id' => 1 ]
			)
		);
	}
}
