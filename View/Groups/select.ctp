<?php
/**
 * Group select template
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

$groupListJson = array();
if (!empty($groupList)) {
	foreach ($groupList as $group) {
		$groupListJson[] = $group['Group'];
	}
}

$data = array(
	'User' => array('id' => Current::read('User.id')),
//	'Room' => array('id' => $roomId),
	'GroupSelect' => array('group_id' => array()),
);
//$tokenFields = Hash::flatten($data);
//$hiddenFields = $tokenFields;
//unset($hiddenFields['GroupSelect.group_id']);
//$hiddenFields = array_keys($hiddenFields);
//
//$this->request->data = $data;
//$this->Token->unlockField('GroupSelect.group_id');
//$tokens = $this->Token->getToken('Group', '/groups/groups/select/' . Current::read('User.id'), $tokenFields, $hiddenFields);
//$data += $tokens;

echo $this->NetCommonsHtml->css(array(
	'/groups/css/style.css',
	'/users/css/style.css',
));

?>

<?php $this->start('title_for_modal'); ?>
<?php echo __d('groups', 'Group select'); ?>
<?php $this->end(); ?>

<div ng-init="initialize(<?php echo h(json_encode($groupListJson)); ?>,
			<?php echo h(json_encode($data)); ?>)">
	<div class="panel panel-default">
		<div class="panel-body pre-scrollable user-selection-list-group">
			<div ng-if="groupList.length">
				<?php echo $this->element('Groups.select_groups', array('groupType' => 'groupList')); ?>
			</div>
			<div ng-if="!groupList.length">
				<?php echo __d('groups', 'Not found the group.'); ?>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body pre-scrollable user-selection-list-group">
			<div ng-if="selectors.length">
				<?php echo $this->element('Groups.select_groups', array('groupType' => 'selectors')); ?>
			</div>
			<div ng-if="!selectors.length">
				<?php echo __d('users', 'Not found the select user.'); ?>
			</div>
		</div>
	</div>
</div>

<?php
$this->start('footer_for_modal');
echo $this->Button->cancelAndSave(
	__d('net_commons', 'Cancel'), __d('net_commons', 'Select'), false,
	array('type' => 'button', 'ng-click' => 'cancel()'),
	array('type' => 'button', 'ng-click' => 'save()', 'ng-disabled' => '!selectors.length')
);
$this->end();
