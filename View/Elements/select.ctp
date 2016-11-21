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
?>

<?php
if (! isset($title)) {
	$title = __d('groups', 'User select');
}
if (! isset($pluginModel)) {
	$pluginModel = 'GroupsUser';
}
$usersJson = array();
if (isset($selectUsers) && is_array($selectUsers)) {
	foreach ($selectUsers as $selectUser) {
		$usersJson[] = $this->UserSearch->convertUserArrayByUserSelection($selectUser, 'User');
	}
}
if (! isset($roomId)) {
	$roomId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?php echo h($title); ?>
	</div>
	<div class="panel-body">
		<?php echo $this->element('Groups.select_users',
			array(
				'pluginModel' => $pluginModel,
				'usersJson' => $usersJson,
				'roomId' => $roomId,
			)); ?>
		<div class="text-right" ng-controller="GroupsAddGroup" ng-init="initialize()">
			<?php
				if (Current::read('Permission.group_creatable.value')) :
					echo $this->Button->addLink(__d('groups', 'Create a new group in the above-described member'),
						'#',
						array(
							'tooltip' => __d('net_commons', 'Add'),
							'ng-click' => 'showGroupAddDialog(' . Current::read('User.id') . ')',
							'class' => 'btn btn-success nc-groups-show-add-btn'
						)
					);
				endif;
			?>
		</div>
	</div>
</div>
