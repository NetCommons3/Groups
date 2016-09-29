<?php
/**
 * Element of block delete form
 *   - $model: Controller for delete request.
 *   - $action: Action for delete request.
 *   - $callback: Callback element for parameters and messages.
 *   - $callbackOptions: Callback options for element.
 *   - $options: Options array for Form->create()
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

echo $this->NetCommonsHtml->css(array(
	'/groups/css/style.css',
));
echo $this->NetCommonsHtml->script(array(
	'/users/js/user_select.js',
	'/groups/js/groups.js',
));
if (!isset($usersJson)) {
	$usersJson = array();
}
if (!isset($pluginModel)) {
	$pluginModel = 'GroupsUser';
}
$this->NetCommonsForm->unlockField($pluginModel . '.user_id');
?>

<div id="group-user-select" class="text-right clearfix"
		ng-controller="GroupsSelect" ng-init="initialize(<?php echo h(json_encode(array('users' => $usersJson))); ?>,
			'<?php echo h($pluginModel); ?>')" class="nc-groups-user-select">
	<div class="clearfix">
		<!-- 会員検索 -->
		<div class="pull-right" ng-controller="GroupsSelectUser" >
			<a href="" ng-click="showUserSelectionDialog(
				'<?php echo Current::read('User.id'); ?>',
				'<?php echo (int)$roomId; ?>'
			)">
				<span class="glyphicon glyphicon-search"></span>
				<?php echo __d('groups', 'User search'); ?>
			</a>
		</div>
		<?php if (Current::read('Permission.group_creatable.value')) : ?>
		<div class="pull-right nc-groups-search-separator">
			<?php echo __d('groups', 'Search separator'); ?>
		</div>
		<!-- グループ選択 -->
		<div class="pull-right" ng-controller="GroupsSelectGroup">
			<a href="" ng-click="showGroupSelectionDialog(
				'<?php echo Current::read('User.id'); ?>',
				'<?php echo (int)$roomId; ?>'
			)">
				<span class="glyphicon glyphicon-search"></span>
				<?php echo __d('groups', 'Group search'); ?>
			</a>
		</div>
		<?php endif; ?>
	</div>
	<div id="groups-select-user-frame" class="panel-body nc-groups-select-user-frame">
	<!--	ここに選択したユーザ情報を表示-->
		<span ng-if="users.length == 0"><?php echo __d('groups', 'Select user'); ?></span>
		<groups-selected-users ng-repeat="user in users"></groups-selected-users>
	</div>
</div>
