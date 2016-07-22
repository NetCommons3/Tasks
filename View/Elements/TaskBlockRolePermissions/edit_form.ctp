<?php
/**
 * BlockRolePermissions edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->Form->hidden('Block.id', array(
	'value' => Current::read('Block.id'),
)); ?>

<?php echo $this->Form->hidden('TaskSetting.id', array(
	'value' => isset($taskSetting['id']) ? (int)$taskSetting['id'] : null,
)); ?>

<?php echo $this->Form->hidden('TaskSetting.key', array(
	'value' => isset($taskSetting['key']) ? $taskSetting['key'] : null,
)); ?>

<?php
echo $this->element('Blocks.block_creatable_setting', array(
		'settingPermissions' => array(
			'content_creatable' => __d('tasks', 'Task creatable roles'),
			'content_comment_creatable' => __d('blocks', 'Content comment creatable roles'),
		),
	)
);

echo $this->element('Blocks.block_approval_setting', array(
	'model' => 'TaskSetting',
	'useWorkflow' => 'use_workflow',
	'useCommentApproval' => 'use_comment_approval',
	'settingPermissions' => array(
		'content_comment_publishable' => __d('blocks', 'Content comment publishable roles'),
	),
	'options' => array(
		Block::NEED_APPROVAL => __d('blocks', 'Need approval in both %s and comments ', __d('tasks', 'ToDoの作成編集')),
		Block::NEED_COMMENT_APPROVAL => __d('blocks', 'Need only comments approval'),
		Block::NOT_NEED_APPROVAL => __d('blocks', 'Not need approval'),
	),
));
