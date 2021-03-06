<?php
/**
 * GroupsApp Controller
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2016, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * GroupsApp Controller
 *
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @package NetCommons\Groups\Controller
 */
class GroupsAppController extends AppController {

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Security',
	);
}
