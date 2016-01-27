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

<div class="panel panel-default">
	<div class="panel-heading">
		<?php echo h($title); ?>
	</div>
	<div class="panel-body">
		<?php echo $this->element('Groups.select_users'); ?>
		<div class="text-right" ng-controller="GroupsAddGroup">
			<?php
				echo $this->Button->addLink('上記の会員でグループを新規作成',
					'#',
					array(
						'tooltip' => __d('net_commons', 'Add'),
						'ng-click' => 'showGroupAddDialog('.Current::read('User.id').')',
						'style' => 'font-size: 10px;',
					)
				);
			?>
		</div>
	</div>
</div>
