<?php
/**
 * TaskContentEdit edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php
echo $this->NetCommonsHtml->script('/tasks/js/tasks.js');
echo $this->NetCommonsHtml->css('/tasks/css/tasks.css');
?>

<?php
//$checkMailClass = '';
//if (! isset($mailSetting['MailSetting']['is_mail_send'])
//		|| $mailSetting['MailSetting']['is_mail_send'] == TaskContent::TASK_CONTENT_NOT_IS_MAIL_SEND
//) {
//	$checkMailClass = 'hidden';
//}
//?>

<div class="taskContentEdit form"
	 ng-controller="TaskContentEdit"
	 ng-init="initialize(<?php echo h(json_encode($this->request->data)); ?>)">
	<article>

		<h1><?php echo h(__d('tasks', 'Task')); ?></h1>

		<div class="panel panel-default">

			<?php echo $this->NetCommonsForm->create(
				'TaskContent',
				array(
					'inputDefaults' => array(
						'div' => 'form-group',
						'class' => 'form-control',
						'error' => false,
					),
					'div' => 'form-control',
					'novalidate' => true
				)
			);
			?>
			<?php echo $this->NetCommonsForm->input('key', array('type' => 'hidden')); ?>
			<?php
			if(isset($taskContent['TaskContent']['id'])):
			echo $this->NetCommonsForm->hidden('TaskContent.id', array('value' => h($taskContent['TaskContent']['id'])));
			endif;
			?>
			<?php echo $this->NetCommonsForm->hidden('Frame.id', array('value' => Current::read('Frame.id'))); ?>
			<?php echo $this->NetCommonsForm->hidden('Block.id', array('value' => Current::read('Block.id'))); ?>

			<div class="panel-body">

				<fieldset>

					<?php echo $this->Form->hidden('TaskContent.progress_rate'); ?>
					<?php echo $this->NetCommonsForm->input(
						'TaskContent.title', array(
							'type' => 'text',
							'required' => 'required',
							'label' => __d('tasks', 'Title')
						)
					); ?>

					<?php echo $this->Category->select('TaskContent.category_id', array('empty' => true)); ?>

					<?php
					$priorityOptions = array(
						TaskContent::TASK_PRIORITY_UNDEFINED => __d('tasks', 'Undefined'),
						TaskContent::TASK_PRIORITY_LOW => __d('tasks', 'Low'),
						TaskContent::TASK_PRIORITY_MEDIUM => __d('tasks', 'Medium'),
						TaskContent::TASK_PRIORITY_HIGH => __d('tasks', 'High')
					);
					?>
					<?php echo $this->NetCommonsForm->input('TaskContent.priority',
						array(
							'label' => __d('tasks', 'Priority'),
							'type' => 'select',
							'options' => $priorityOptions,
							'class' => 'form-control task-margin-width-2',
						)
					); ?>

					<?php echo $this->element('TaskContentEdit/task_period_edit_form'); ?>

					<?php echo $this->element('TaskContentEdit/charge_edit_form'); ?>

					<div class="form-group">
						<?php echo $this->NetCommonsForm->wysiwyg('TaskContent.content', array(
								'label' => __d('tasks', 'Content'),
								'required' => true,
							)
						); ?>
					</div>

<!--	リマインダーメール設定項目				-->
<!--					<div class="form-group --><?php //echo $checkMailClass; ?><!--" data-calendar-name="checkMail">-->
<!--						--><?php
//						echo $this->NetCommonsForm->checkbox('TaskContent.is_enable_mail', array(
//							'class' => 'text-left pull-left'
//						));
//						?>
<!--						--><?php //echo $this->NetCommonsForm->error('TaskContent.is_enable_mail'); ?>
<!--						--><?php //echo $this->NetCommonsForm->label(
//							'TaskContent.email_send_timing',
//							__d('tasks', 'Inform in advance by mail')
//						); ?>

<!--						--><?php
//						$options = array(
//							'1' => __d('tasks', 'One day before the task period'),
//							'2' => __d('tasks', 'Two days before the task period'),
//							'7' => __d('tasks', 'One week before the task period'),
//						);
//
//						echo $this->NetCommonsForm->select('TaskContent.email_send_timing', $options, array(
//								'class' => 'form-control pull-left task-content-margin-1',
//								'empty' => false,
//							)
//						); ?>
<!--						--><?php //echo $this->NetCommonsForm->error('TaskContent.email_send_timing'); ?>
<!--					</div>-->

<!--	カレンダー連携登録チェックボックス				-->
<!--					--><?php
//					echo $this->NetCommonsForm->checkbox('TaskContent.use_calendar', array(
//						'class' => 'text-left pull-left',
//					));
//					?>
<!--					--><?php //echo $this->NetCommonsForm->label(
//						'TaskContent.use_calendar',
//						__d('tasks', 'Use calendar')
//					); ?>
<!--					--><?php //echo $this->NetCommonsForm->error('TaskContent.use_calendar'); ?>
				</fieldset>

				<hr/>

				<?php echo $this->Workflow->inputComment('TaskContent.status'); ?>

			</div>

			<?php echo $this->Workflow->buttons('TaskContent.status'); ?>

			<?php echo $this->NetCommonsForm->end(); ?>


			<?php if ($this->request->params['action'] === 'edit') : ?>
				<div class="panel-footer text-right">
					<?php echo $this->element('TaskContentEdit/delete_form'); ?>
				</div>
			<?php endif; ?>

		</div>

	</article>
</div>
