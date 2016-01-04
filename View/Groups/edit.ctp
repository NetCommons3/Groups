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

<!-- 編集フォーム -->
<?php echo $this->element('Groups.edit_form'); ?>

<!-- 削除エリア -->
<?php if ($this->params['action'] === 'edit') : ?>
	<?php echo $this->element('Groups.delete_form'); ?>
<?php endif; ?>
