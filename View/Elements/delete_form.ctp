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

<div class="nc-danger-zone" ng-init="dangerZone=false;">
	<?php echo $this->NetCommonsForm->create('Group', array(
			'type' => 'delete',
			'url' => $this->NetCommonsHtml->url(array('action' => 'delete/' . $this->data['Group']['id']))
		)); ?>

		<accordion close-others="false">
			<accordion-group is-open="dangerZone" class="panel-danger">
				<accordion-heading class="clearfix">
					<span style="cursor: pointer">
						<?php echo __d('net_commons', 'Danger Zone'); ?>
					</span>
					<span class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': dangerZone, 'glyphicon-chevron-right': ! dangerZone}"></span>
				</accordion-heading>

				<div class="pull-left">
					<?php echo sprintf(__d('net_commons', 'Delete all data associated with the %s.'), __d('groups', 'Group')); ?>
				</div>
				<?php echo $this->NetCommonsForm->hidden('User.id'); ?>

				<?php echo $this->Button->delete(
						__d('net_commons', 'Delete'),
						sprintf(__d('net_commons', 'Deleting the %s. Are you sure to proceed?'), __d('groups', 'Groups')),
						array('addClass' => 'pull-right')
					); ?>

			</accordion-group>
		</accordion>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>
