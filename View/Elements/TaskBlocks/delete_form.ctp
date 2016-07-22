<?php
/**
 * Blocks edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="inline-block">
	<?php echo sprintf(__d('net_commons', 'Delete all data associated with the %s.'), __d('tasks', 'Task')); ?>
</div>

<?php echo $this->Form->hidden('Block.id', array(
	'value' => isset($block['id']) ? $block['id'] : null,
)); ?>
<?php echo $this->Form->hidden('Block.key', array(
	'value' => isset($block['key']) ? $block['key'] : null,
)); ?>
<?php echo $this->Form->hidden('Task.key', array(
	'value' => isset($task['key']) ? $task['key'] : null,
)); ?>
<?php echo $this->Form->button('<span class="glyphicon glyphicon-trash"> </span> ' . __d('net_commons', 'Delete'), array(
	'name' => 'delete',
	'class' => 'btn btn-danger pull-right',
	'onclick' => 'return confirm(\'' . sprintf(__d('net_commons', 'Deleting the %s. Are you sure to proceed?'), __d('tasks', 'Task')) . '\')'
));
