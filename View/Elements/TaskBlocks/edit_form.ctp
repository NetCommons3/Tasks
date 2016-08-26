<?php
/**
 * TaskBlocks edit form template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->element('Blocks.form_hidden'); ?>

<?php echo $this->Form->hidden('Task.id'); ?>
<?php echo $this->Form->hidden('Task.key'); ?>
<?php echo $this->Form->hidden('TaskSetting.id'); ?>
<?php echo $this->Form->hidden('TaskSetting.use_workflow'); ?>
<?php echo $this->Form->hidden('TaskSetting.use_comment_approval'); ?>
<?php echo $this->Form->hidden('old.Categories', array('value' => json_encode(h($this->request->data('Categories'))))); ?>
<?php echo $this->NetCommonsForm->input('Task.name', array(
		'type' => 'text',
		'label' => __d('tasks', 'Task list'),
		'required' => true,
	)
); ?>

<?php echo $this->element('Blocks.public_type'); ?>

<?php echo $this->NetCommonsForm->inlineCheckbox('TaskSetting.use_comment', array(
		'label' => __d('tasks', 'Use comment')
	)
); ?>

<?php
echo $this->element('Categories.edit_form', array(
		'categories' => isset($categories) ? $categories : null
	)
); ?>
<?php echo $this->element('Blocks.modifed_info', array('displayModified' => true));
