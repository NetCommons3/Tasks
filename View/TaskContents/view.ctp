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
echo $this->Html->css(
	array(
		'/tasks/css/tasks.css'
	),
	array(
		'plugin' => false,
		'once' => true,
		'inline' => false
	)
);
?>
<div class="taskContents form">
	<div class="ng-scope">

		<div class="clearfix" style="margin-bottom: 10px">
			<header class="clearfix">
				<div class="pull-left" style="">
					<?php echo $this->Workflow->label($taskContent['TaskContent']['status']); ?>
				</div>
				<div class="pull-right" style="margin: 5px 0 15px 5px">
					<?php echo $this->Button->editLink('',
						array(
							'controller' => 'task_contents',
							'key' => $taskContent['TaskContent']['key']
						),
						array(
							'tooltip' => true,
						)
					); ?>
				</div>
			</header>

			<div class="clearfix">
				<div class="pull-left">
					<h1 style="margin-top: 0;" class="pull-left">
						<?php echo $taskContent['TaskContent']['title']; ?>
					</h1>
				</div>
			</div>
		</div>

		<div class="clearfix" style="margin-bottom: 10px;">
			<div class="pull-left">
				<?php echo h(__d('tasks', 'Implementation period') . __d('tasks', 'Colon')); ?>
				<?php if (empty($taskContent['TaskContent']['task_start_date'])): ?>
					<?php echo h('--:--'); ?>
				<?php else: ?>
					<?php echo $this->Date->dateFormat(date("Y-m-d H:i:s", strtotime($taskContent['TaskContent']['task_start_date']))); ?>
				<?php endif; ?>
				
				<?php echo h(__d('tasks', 'Till')); ?>
				
				<?php if (empty($taskContent['TaskContent']['task_end_date'])): ?>
					<?php echo h('--:--'); ?>
				<?php else: ?>
					<?php echo $this->Date->dateFormat(date("Y-m-d H:i:s", strtotime($taskContent['TaskContent']['task_end_date']))); ?>
				<?php endif; ?>
			</div>
			<div class="pull-left" style="margin-left: 10px">
				<?php echo h(__d('tasks', 'Priority') . __d('tasks', 'Colon')); ?>
				<?php if ($taskContent['TaskContent']['priority']) : ?>
					<?php echo $this->element('TaskContents/priority_icon', array(
						'priority' => $taskContent['TaskContent']['priority'],
						'style' => false,
					)); ?>
				<?php else : ?>
					<?php echo h(__d('tasks', 'Undefined')); ?>
				<?php endif; ?>
			</div>
			<div class="pull-left" style="margin-left: 10px">
				<?php echo h(__d('tasks', 'Category') . __d('tasks', 'Colon')); ?>
				<?php if ($taskContent['Category']['name']): ?>
					<?php
					$url = array(
							'controller' => 'task_contents',
							'action' => 'index');
					?>
					<?php echo $this->NetCommonsHtml->link($taskContent['Category']['name'],
							Hash::merge($url, array('category_id' => $taskContent['Category']['id'])));
					?>
				<?php else : ?>
					<?php echo h(__d('tasks', 'Not selected')); ?>
				<?php endif; ?>
			</div>
			<div class="pull-right">
				<div style="display: table">
					<?php echo h(__d('tasks', 'Created_user')); ?>
					<div class="table-cell-1">
						<?php echo $this->DisplayUser->handlelink(
							$taskContent, array('avatar' => true)
						); ?>
					</div>
					<?php echo $this->Date->dateFormat($taskContent['TaskContent']['created']); ?>
				</div>
			</div>
		</div>
		<div class="clearfix">
			<div class="pull-left">
				<div class="pull-left">
					<div style="display: table">
						<div style="display: table-cell">
							<?php echo h(__d('tasks', 'Progress rate')); ?>
						</div>
						<div style="display: table-cell">
							<?php echo $this->element(
								'TaskContents/select_progress', array(
								'progressRate' => $taskContent['TaskContent']['progress_rate']
							)); ?>
						</div>
					</div>
				</div>
				<div class="pull-left">
					<div class="progress-min-scale-xs" style="display: table-cell">
						<div class="progress progress-min-width-xs"
							 style="width: 300px; height: 32px; margin-left: 5px">
							<div class="progress-bar progress-bar-success"
								 style="width: <?php echo $taskContent['TaskContent']['progress_rate']; ?>%;">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pull-right ">
				<div style="display: table">
					<?php echo h(__d('tasks', 'Modified user')); ?>
					<div class="table-cell-1">
						<?php echo $this->DisplayUser->handlelink(
							$taskContent, array('avatar' => true), array(), 'TrackableUpdater'
						); ?>
					</div>
					<?php echo $this->Date->dateFormat($taskContent['TaskContent']['modified']); ?>
				</div>
			</div>
		</div>

		<div class="clearfix">
			<div class="pull-left">
				<div style="display: table">
					<div class="table-cell-2" style="vertical-align: top">
						<?php echo h(__d('tasks', 'Person in charge')); ?>
					</div>
					<div class="">
					<span class="nc-groups-avatar-list" style="text-align: left">
						<?php if (! $this->request->data['selectUsers']) : ?>
							<?php echo h(__d('tasks', 'Not selected')); ?>
						<?php else : ?>
							<?php foreach ($this->request->data['selectUsers'] as $selectUsers): ?>
								<?php
								echo $this->DisplayUser->handlelink(
									$selectUsers, array('avatar' => true), array(), 'User'
								);
								?>
							<?php endforeach; ?>
						<?php endif; ?>
					</span>
					</div>
				</div>
			</div>
		</div>

		<hr style="border: 0 none transparent;">

		<div>
			<?php echo $taskContent['TaskContent']['content']; ?>
		</div>

		<hr style="border: 0 none transparent;">

		<?php echo $this->ContentComment->index($taskContent); ?>

	</div>
</div>
