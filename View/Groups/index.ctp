<?php
/**
 * Groups index template
 *
 * @author Masaki Goto <go8ogle@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

echo $this->NetCommonsHtml->css(array(
	'/groups/css/style.css',
));
echo $this->NetCommonsHtml->script('/groups/js/groups.js');
?>

<div class="text-right">
	<?php echo $this->Button->addLink(); ?>
</div>

<div class="table-responsive">
	<table class="table table-condensed">
		<thead>
			<tr>
				<th></th>
				<th><?php echo __d('groups', 'Groups name'); ?></th>
				<th><?php echo __d('groups', 'Groups modified'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($groups as $index => $group): ?>
			<tr>
				<td>
					<?php echo ($index + 1); ?>
				</td>
				<td>
					<?php echo $this->Html->link(
							$group['Group']['name'],
							array(
								'controller' => 'groups',
								'action' => 'edit' . '/' . $group['Group']['id']
							),
							array(),
							false
					); ?>
				</td>
				<td>
					<?php echo $group['Group']['modified'] ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>