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

<div class="ng-scope">

	<h1 class="tasks_taskTitle"><?php echo $listTitle ?></h1>

	<div class="clearfix" style="margin-top: 10px; margin-bottom: 10px;">

		<div class="pull-right">
			<span class="nc-tooltip" tooltip="<?php echo h(__d('net_commons', 'Add')); ?>" style="margin-right: 8px">
				<?php
				$addUrl = $this->NetCommonsHtml->url(array(
					'controller' => 'task_contents',
					'action' => 'add',
				));
				echo $this->Button->addLink('',
					$addUrl,
					array('tooltip' => __d('tasks', 'ToDo Add')));
				?>
			</span>
		</div>
	</div>

	<div class="clearfix" style="margin-bottom: 20px">
		<div class="pull-left">
			<?php echo $this->element('TaskContents/select_status'); ?>
			<?php echo $this->Category->dropDownToggle(array(
				'empty' => h(__d('tasks', 'No category assignment')),
				'displayMenu' => true,
				$this->NetCommonsHtml->url(array('action' => 'index')),
			)); ?>
			<?php echo $this->element('TaskContents/select_user'); ?>
			<?php echo $this->element('TaskContents/select_sort'); ?>
		</div>
	</div>

	<?php if (empty(count($taskContents))): ?>
		<div>
			<?php echo h(__d('tasks', 'Not task')); ?>
		</div>
	<?php else: ?>

		<?php foreach ($taskContents as $taskContent): ?>

			<div class="clearfix" style="height: 25px;">
				<div style="border-bottom-width: 5px;" class="pull-left">
					<?php echo $taskContent['Category']['name']; ?>
				</div>

				<?php if ($taskContent['Category']['name'] !== __d('tasks', 'No category')): ?>
					<?php echo $this->element('TaskContents/achievement_rate_progress_bar', array(
						'progressWidth' => 250,
						'achievedValue' => $taskContent['Category']['category_priority']
					)); ?>
				<?php endif; ?>

			</div>

			<?php echo $this->element('TaskContents/task_content', array(
				'taskContents' => $taskContent['TaskContents'],
			)); ?>

		<?php endforeach; ?>

	<?php endif; ?>
</div>
