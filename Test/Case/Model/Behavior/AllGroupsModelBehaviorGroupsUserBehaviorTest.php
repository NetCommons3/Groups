<?php
/**
 * All GroupsUserBehavior Test suite
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuna Miyashita <butackle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsTestSuite', 'NetCommons.TestSuite');

/**
 * All GroupsUserBehavior Test suite
 *
 * @author Yuna Miyashita <butackle@gmail.com>
 * @package NetCommons\Groups\Test\Case\GroupsUserBehavior
 */
class AllGroupsModelBehaviorGroupsUserBehaviorTest extends NetCommonsTestSuite {

/**
 * All GroupsUserBehavior Test suite
 *
 * @return NetCommonsTestSuite
 * @codeCoverageIgnore
 */
	public static function suite() {
		$name = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new NetCommonsTestSuite(sprintf('All %s tests', $name));
		$suite->addTestDirectoryRecursive(__DIR__ . DS . 'GroupsUserBehavior');
		return $suite;
	}

}
