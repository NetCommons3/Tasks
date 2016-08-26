<?php
/**
 * TaskContent index template
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
<article>
	<h1 class="tasks_taskTitle"><?php echo $listTitle ?></h1>

	<?php if (Current::permission('content_creatable')) : ?>

		<div class="pull-right task-index-space">
			<?php
			$addUrl = array(
				'controller' => 'task_content_edit',
				'action' => 'add',
				'frame_id' => Current::read('Frame.id')
			);
			echo $this->Button->addLink('',
				$addUrl,
				array('tooltip' => __d('tasks', 'ToDo Add'))
			);
			?>
		</div>
	<?php endif ?>

	<div class="clearfix task-content-margin-1">
		<div class="pull-left">
			<?php echo $this->element('TaskContents/select_is_completion', array(
				'options' => $isCompletionOptions,
				'currentIsCompletion' => $currentIsCompletion,
			)); ?>
			<span class="btn-group task-index-space">
				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true">
					<span class="pull-left nc-drop-down-ellipsis">
						<?php echo $categoryLabel ?>
					</span>
					<span class="pull-right">
						<span class="caret"></span>
					</span>
				</button>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
					<?php echo $this->Category->dropDownToggle(array(
						'empty' => h(__d('tasks', 'No category assignment')),
						'displayMenu' => false,
							'url' => NetCommonsUrl::actionUrlAsArray(Hash::merge(array(
								'action' => 'index',
								'block_id' => Current::read('Block.id'),
								'frame_id' => Current::read('Frame.id')
							), $this->params['named'])),
					)); ?>
				</ul>
			</span>
			<?php echo $this->element('TaskContents/select_user', array(
				'options' => $userOptions,
				'currentUserId' => $currentUserId
			)); ?>
			<?php echo $this->element('TaskContents/select_sort', array(
				'options' => $sortOptions,
				'currentSort' => $currentSort,
			)); ?>
		</div>
	</div>
	<div class="nc-content-list">
		<?php if (!$taskContents): ?>
			<div>
				<?php echo h(__d('tasks', 'Not task')); ?>
			</div>
		<?php else: ?>

			<?php $isNotShow = false; ?>
			<?php if (isset($params['user_id']) && $params['user_id'] !== Current::read('User.id')): ?>
				<?php $isNotShow = true; ?>
			<?php endif; ?>
			<?php $params = $this->params['named']; ?>

			<?php if ($deadLineTasks && empty($params['category_id']) && $isNotShow === false): ?>
				<div class="clearfix">
					<div class="pull-left task-word-break">
						<?php echo __d('tasks', 'Deadline close Expiration'); ?>
					</div>
				</div>

				<?php echo $this->element('TaskContents/task_content', array(
						'taskContents' => $deadLineTasks,
					)
				); ?>
			<?php endif; ?>

			<?php foreach ($taskContents as $taskContent): ?>

				<div class="clearfix task-content">
					<div class="pull-left task-category-name-margin task-word-break">
						<?php echo h($taskContent['Category']['name']); ?>
					</div>

					<?php if ($taskContent['Category']['name'] !== __d('tasks', 'No category')): ?>
						<div class="pull-right">
							<div class="clearfix">
								<div class="pull-left progress-min-scale-xs">
									<div class="progress progress-min-width-xs task-index-progress task-progress-width-index">
										<div class="progress-bar progress-bar-info"
											 style="width: <?php echo h($taskContent['Category']['category_priority']); ?>%;">
											<?php echo h($taskContent['Category']['category_priority']) . h(__d('tasks', 'Progress rate percent')); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</div>

				<?php echo $this->element('TaskContents/task_content', array(
					'taskContents' => $taskContent['TaskContents'],
				)); ?>

			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</article>
