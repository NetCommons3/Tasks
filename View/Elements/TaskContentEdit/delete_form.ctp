<?php
/**
 * TaskContent edit delete form element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->create('TaskContent', array(
		'type' => 'delete',
		'url' => NetCommonsUrl::blockUrl(array('controller' => 'task_content_edit', 'action' => 'delete', 'frame_id' => Current::read('Frame.id')))
	)
); ?>
<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
<?php echo $this->NetCommonsForm->hidden('Block.key'); ?>

<?php echo $this->NetCommonsForm->hidden('TaskContent.id'); ?>
<?php echo $this->NetCommonsForm->hidden('TaskContent.key'); ?>

<?php echo $this->Button->delete('',
	sprintf(__d('net_commons', 'Deleting the %s. Are you sure to proceed?'), __d('tasks', 'ToDo'))
); ?>
<?php echo $this->NetCommonsForm->end();
