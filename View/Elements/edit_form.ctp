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

$usersJson = array();
if (isset($this->request->data['GroupsUsersDetail']) && is_array($this->request->data['GroupsUsersDetail'])) {
	foreach ($this->request->data['GroupsUsersDetail'] as $groupUser) {
		$usersJson[] = $this->UserSearch->convertUserArrayByUserSelection($groupUser, 'User');
	}
}
?>
<h1>
	<?php echo h(__d('groups', 'グループ登録')); ?>
</h1>

<div id="groups-select-users" class="panel panel-default" ng-controller="GroupsAddGroup">
	<?php echo $this->NetCommonsForm->create('Group', array('type' => 'file')); ?>
<!--	<div class="panel-body" ng-controller="GroupsSelectGroup">-->
	<div class="panel-body">
		<!-- グループ名 -->
		<?php echo $this->NetCommonsForm->input('Group.name', array(
			'type' => 'text',
			'label' => __d('groups', 'Groups name'),
		)); ?>

		<!-- ユーザ選択 -->
		<!-- TODO hiddenを有効にする -->
<!--		--><?php //if ((int)$isModal): $className = 'hidden' ?>
		<?php if ((int)$isModal): $className = 'show' ?>
		<?php else: $className = 'show' ?>
		<?php endif; ?>
		<div class="<?php echo $className; ?>" ng-controller="GroupsSelectGroup">
<!--		<div class="--><?php //echo $className; ?><!--" ng-controller="GroupsSelectGroup">-->
			<?php echo $this->element('Groups.select_users', array('usersJson' => $usersJson)); ?>
			<?php echo $this->NetCommonsForm->error('GroupsUser.user_id'); ?>
		</div>
	</div>

	<?php echo $this->NetCommonsForm->hidden('Group.id', array(
		'value' => isset($this->request->data['Group']['id']) ? $this->request->data['Group']['id'] : null,
	)); ?>

	<!-- ボタン -->
	<div class="panel-footer text-center">
		<?php if ((int)$isModal): ?>
			<?php echo $this->Button->cancelAndSave(
				__d('net_commons', 'Cancel'), __d('net_commons', 'OK'), false,
				array('type' => 'button', 'ng-click' => 'cancel()'),
				array('type' => 'button', 'ng-click' => 'save()')
			); ?>

		<?php else: ?>
			<?php echo $this->Button->cancelAndSave(
				__d('net_commons', 'Cancel'),
				__d('net_commons', 'OK'),
				$this->NetCommonsHtml->url(
					array(
						'plugin' => 'users',
						'controller' => 'users',
						'action' => 'view' . '/' . Current::read('User.id') . '#/user-groups',
					)
				),
				array(),
				$this->NetCommonsHtml->url(
					array(
						'plugin' => 'users',
						'controller' => 'users',
						'action' => 'view' . '/' . Current::read('User.id') . '#/user-groups',
					)
				)
			); ?>
		<?php endif; ?>
		
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>
