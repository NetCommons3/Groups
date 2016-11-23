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

if (isset($groups[0])) {
	$this->request->data = $groups[0];
}
$groupUsersList = array();
if (!empty($users)) {
	$groupUsersList = $this->GroupUserList->convertGroupUserListForDisplay($users);
}
$groupUsers = array();
if (isset($this->request->data['GroupsUser'])) {
	foreach ($this->request->data['GroupsUser'] as $groupsUser) {
		if (isset($groupUsersList[$groupsUser['user_id']])) {
			$groupUsers[] = $groupUsersList[$groupsUser['user_id']];
		}
	}
}
$roomId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);

if (! isset($redirectUrl)) {
	$redirectUrl = null;
}
?>

<div id="groups-select-users" class="panel panel-default" ng-controller="GroupsAddGroup">
	<?php echo $this->NetCommonsForm->create('Group'); ?>
	<div class="panel-body">
		<!-- グループ名 -->
		<div id="groups-input-name-<?php echo Current::read('User.id'); ?>">
			<?php echo $this->NetCommonsForm->input('Group.name', array(
				'type' => 'text',
				'label' => __d('groups', 'Group name'),
				'requred' => true,
			)); ?>
		</div>
		<?php echo $this->NetCommonsForm->error('GroupsUser.user_id'); ?>

		<!-- ユーザ選択 -->
		<?php if ((int)$isModal): $className = 'hidden' ?>
		<?php else: $className = 'show' ?>
		<?php endif; ?>
		<div class="<?php echo $className; ?>" ng-controller="GroupsSelectGroup">
			<?php echo $this->element('Groups.select_users', array(
				'usersJson' => $groupUsers,
				'roomId' => $roomId,
			)); ?>
		</div>
	</div>

	<?php echo $this->NetCommonsForm->hidden('Group.id'); ?>

	<!-- ボタン -->
	<div class="panel-footer text-center">
		<?php if ((int)$isModal): ?>
			<?php echo $this->Button->cancelAndSave(
				__d('net_commons', 'Cancel'), __d('net_commons', 'OK'), false,
				array('type' => 'button', 'ng-click' => 'cancel()'),
				array('type' => 'button', 'ng-click' => 'save()')
			); ?>

		<?php else: ?>
			<?php echo $this->NetCommonsForm->hidden('_user.redirect', ['value' => $redirectUrl]); ?>
			<?php echo $this->Button->cancelAndSave(
				__d('net_commons', 'Cancel'),
				__d('net_commons', 'OK'),
				false,
				array('type' => 'submit', 'ng-click' => null)
			); ?>
		<?php endif; ?>

	</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>
