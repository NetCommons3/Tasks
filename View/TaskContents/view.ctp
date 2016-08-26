<?php
/**
 * TaskContent view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
echo $this->NetCommonsHtml->css('/tasks/css/tasks.css');
?>
<div class="taskContents form">
	<div class="ng-scope">

		<header class="clearfix">
			<div class="pull-left">
				<?php echo $this->Workflow->label($taskContent['TaskContent']['status']); ?>
			</div>
			<div class="pull-right">
				<?php if ($this->Workflow->canEdit('Tasks.TaskContent', $taskContent)) : ?>
					<div class="text-right">
						<?php echo $this->Button->editLink('',
							array(
								'controller' => 'task_content_edit',
								'key' => $taskContent['TaskContent']['key']
							),
							array(
								'tooltip' => true,
							)
						); ?>
					</div>
				<?php endif; ?>
			</div>
		</header>

		<article>
			<div class="clearfix task-word-break">
				<?php echo $this->NetCommonsHtml->blockTitle($taskContent['TaskContent']['title']); ?>
			</div>

			<div class="clearfix task-content-margin-2">
				<div class="pull-left task-content-margin-3">
					<div class="task-view-table-cell-3">
						<?php echo h(__d('tasks', 'Implementation period') . __d('tasks', 'Colon')); ?>
					</div>
					<div class="task-view-table-cell">
						<?php echo $this->TaskContent->displayDate($taskContent['TaskContent']['task_start_date'], $taskContent['TaskContent']['is_date_set']); ?>
						<?php echo h(__d('tasks', 'Till')); ?>
						<?php echo $this->TaskContent->displayDate($taskContent['TaskContent']['task_end_date'], $taskContent['TaskContent']['is_date_set']); ?>
					</div>
				</div>
				<div class="task-view-table-cell pull-left task-content-margin-3">
					<?php echo h(__d('tasks', 'Priority') . __d('tasks', 'Colon')); ?>
					<?php if ($taskContent['TaskContent']['priority']) : ?>
						<?php echo $this->element('TaskContents/priority_icon', array(
							'priority' => $taskContent['TaskContent']['priority'],
							'class' => false,
						)); ?>
					<?php else : ?>
						<?php echo h(__d('tasks', 'Undefined')); ?>
					<?php endif; ?>
				</div>
				<div class="task-view-table-cell pull-left task-content-margin-3 task-word-break">
					<?php echo h(__d('tasks', 'Category') . __d('tasks', 'Colon')); ?>
					<?php if (h($taskContent['Category']['name'])): ?>
						<?php
						$url = array(
							'controller' => 'task_contents',
							'action' => 'index');
						?>
						<?php echo $this->NetCommonsHtml->link(h($taskContent['Category']['name']),
							Hash::merge($url, array('category_id' => $taskContent['Category']['id'])));
						?>
					<?php else : ?>
						<?php echo h(__d('tasks', 'Not selected')); ?>
					<?php endif; ?>
				</div>
				<div class="pull-right">
					<div class="task-view-table">
						<?php echo h(__d('tasks', 'Created_user')); ?>
						<div class="task-view-table-cell-1">
							<?php echo $this->DisplayUser->handlelink(
								$taskContent, array('avatar' => true)
							); ?>
						</div>
						<?php echo $this->Date->dateFormat($taskContent['TaskContent']['created']); ?>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div class="pull-right ">
					<div class="task-view-table">
						<?php echo h(__d('tasks', 'Modified user')); ?>
						<div class="task-view-table-cell-1">
							<?php echo $this->DisplayUser->handlelink(
									$taskContent, array('avatar' => true), array(), 'TrackableUpdater'
							); ?>
						</div>
						<?php echo $this->Date->dateFormat($taskContent['TaskContent']['modified']); ?>
					</div>
				</div>
				<div class="pull-left">
					<div class="pull-left">
						<div class="task-view-table">
							<div class="task-view-table-cell-2 task-view-user-top task-view-padding-top">
								<?php echo h(__d('tasks', 'Progress rate')); ?>
							</div>
							<?php
							$disabled = 'disabled';
							if ((Hash::extract($taskContent, 'TaskCharge.{n}[user_id=' . Current::read('User.id') . ']')
									|| $this->Workflow->canEdit('Tasks.TaskContent', $taskContent))
									&& $taskContent['TaskContent']['status'] === TaskContent::TASK_CONTENT_STATUS_PUBLISHED
							):
								$disabled = '';
							endif;
							?>
							<div class="task-view-table-cell">
								<?php echo $this->element(
									'TaskContents/select_progress', array(
									'progressRate' => $taskContent['TaskContent']['progress_rate'],
									'disabled' => $disabled
								)); ?>
							</div>
						</div>
					</div>
					<div class="pull-left">
						<div class="progress-min-scale-xs task-view-table-cell">
							<div class="progress progress-min-width-xs task-view-progress task-progress-width-view">
								<div class="progress-bar progress-bar-success"
									 style="width: <?php echo h($taskContent['TaskContent']['progress_rate']); ?>%;">
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="clearfix">
				<div class="pull-left">
					<div class="task-view-table">
						<div class="task-view-table-cell-2 task-view-user-top">
							<?php echo h(__d('tasks', 'Person in charge')); ?>
						</div>
						<?php if (! $this->request->data['selectUsers']) : ?>
							<?php echo h(__d('tasks', 'Not selected')); ?>
						<?php else : ?>
							<?php foreach ($this->request->data['selectUsers'] as $selectUsers): ?>
								<div class="task-view-table-cell pull-left task-content-margin-2 task-content-margin-3 task-word-break">
									<?php
									echo $this->DisplayUser->handlelink(
										$selectUsers, array('avatar' => true), array(), 'User'
									);
									?>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="task-view-content-top task-word-break">
				<?php echo $taskContent['TaskContent']['content']; ?>
			</div>

			<div class="task-word-break">
				<?php echo $this->ContentComment->index($taskContent); ?>
			</div>
		</article>
	</div>
</div>
