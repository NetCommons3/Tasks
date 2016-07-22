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

<?php echo $this->element('Blocks.form_hidden'); ?>

<?php echo $this->Form->hidden('Task.id'); ?>
<?php echo $this->Form->hidden('Task.key'); ?>
<?php echo $this->Form->hidden('TaskSetting.id'); ?>
<?php echo $this->Form->hidden('TaskFrameSetting.id'); ?>
<?php echo $this->Form->hidden('TaskFrameSetting.frame_key'); ?>
<?php echo $this->Form->hidden('TaskFrameSetting.articles_per_page'); ?>

<?php echo $this->NetCommonsForm->input('Task.name', array(
	'type' => 'text',
	'label' => __d('tasks', 'Task list'),
	'required' => true,
)); ?>

<?php echo $this->element('Blocks.public_type'); ?>

<?php echo $this->NetCommonsForm->inlineCheckbox('TaskSetting.use_comment', array(
	'label' => __d('tasks', 'Use comment')
)); ?>

<?php
echo $this->element('Categories.edit_form', array(
	'categories' => isset($categories) ? $categories : null
));
?>
<?php echo $this->element('Blocks.modifed_info', array('displayModified' => true));
