<?php
/**
 * Group select element
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
?>

<div class="row">
	<div class="col-xs-12 group-selection-list" ng-repeat="group in <?php echo $groupType; ?> track by $index" ng-class="{'group-selection-list-offset': $odd}">
		<?php if ($groupType === 'groupList') : ?>
			<?php echo $this->Button->add('', array(
				'type' => 'button',
				'class' => 'btn btn-success btn-xs user-select-button',
				'ng-click' => 'select($index)',
				'ng-disabled' => 'selected(groupList[$index])',
				'ng-class' => '{active: selected(groupList[$index])}',
			)); ?>
		<?php endif; ?>

		<span class="">
			{{group.name}}
		</span>

		<?php if ($groupType === 'selectors') : ?>
			<?php echo $this->Button->cancel('', false, array(
				'type' => 'button',
				'class' => 'btn btn-default btn-xs pull-right user-delete-button',
				'ng-click' => 'remove($index)'
			)); ?>
		<?php endif; ?>
	</div>

	<div class="clearfix visible-xs-block" ng-if="$odd"></div>
</div>
