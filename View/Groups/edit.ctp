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
echo $this->NetCommonsHtml->script(array(
		'/groups/js/groups.js',
		'/users/js/user_select.js',
));
?>

<ul class="nav nav-tabs" role="tablist">
	<li class="disabled">
		<a href="">
			<?php echo __d('users', 'User information'); ?>
		</a>
	</li>

	<li class="disabled">
		<a href="">
			<?php echo __d('users', 'Rooms'); ?>
		</a>
	</li>

	<li class="active">
		<a href="#user-groups" aria-controls="user-groups" role="tab" data-toggle="tab">
			<?php echo __d('groups', 'Groups management'); ?>
		</a>
	</li>
</ul>
<br>

<!-- 編集フォーム -->
<?php echo $this->element('Groups.edit_form'); ?>

<!-- 削除エリア -->
<?php if ($this->params['action'] === 'edit') : ?>
	<?php echo $this->element('Groups.delete_form'); ?>
<?php endif; ?>
