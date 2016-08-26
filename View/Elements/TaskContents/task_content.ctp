<?php
/**
 * TaskContentstask task content for view element
 *
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
	<table class="table table-hover">
		<tbody>
		<?php foreach ($taskContents as $content): ?>
			<tr>
				<?php if ($content['TaskContent']['is_completion'] === true): ?>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1 task-index-content-text-middle">
						<div data-toggle="buttons">
							<?php echo $this->NetCommonsForm->input(
								'<span class="glyphicon glyphicon-ok text-success"></span>', array(
									'type' => 'button',
									'checked' => true,
									'disabled' => 'disabled',
									'class' => 'btn btn-default active',
									'div' => false,
								)
							); ?>
						</div>
					</td>
				<?php else: ?>
					<?php
					$url = array(
							'controller' => 'task_progress_rate',
							'action' => 'edit',
							'content_key' => h($content['TaskContent']['key']),
							'TaskContent' => array('progress_rate' => TaskContent::TASK_COMPLETION_PROGRESS_RATE),
					);
					echo $this->NetCommonsForm->create(
						'TaskProgressRate', array('type' => 'post', 'url' => $url)
					);
					$disabled = 'disabled';
					$charge = '';
					if (Current::read('User.id')) {
						$charge = Hash::extract($content, 'TaskCharge.{n}[user_id=' . Current::read('User.id') . ']');
					}
					if (($charge || $this->Workflow->canEdit('Tasks.TaskContent', $content))
							&& $content['TaskContent']['status'] === TaskContent::TASK_CONTENT_STATUS_PUBLISHED) {
						$disabled = '';
					}
					?>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1 task-index-content-text-middle">
						<div data-toggle="buttons">
							<?php echo $this->NetCommonsForm->input(
								'<span class="glyphicon glyphicon-ok text-muted color-un-active"></span>', array(
									'type' => 'button',
									'onClick' => 'submit();',
									'checked' => true,
									'class' => 'btn btn-default',
									'div' => false,
									$disabled
								)
							); ?>
						</div>
					</td>
					<?php echo $this->NetCommonsForm->end(); ?>
				<?php endif; ?>
	
				<?php
				$color = array(
					TaskContent::TASK_START_DATE_BEFORE => 'text-muted',
					TaskContent::TASK_DEADLINE_CLOSE => 'text-warning',
					TaskContent::TASK_BEYOND_THE_END_DATE => 'text-danger',
					TaskContent::TASK_BEING_PERFORMED => ''
				);
				?>
				<td class="col-xs-4 col-sm-2 col-md-2 col-lg-2 task-index-content-text-middle task-word-break
					<?php echo h($color[$content['TaskContent']['date_color']]); ?>">
					<?php if (empty($content['TaskContent']['priority'])): ?>
						<div class="task-date-font">
							<?php echo $this->TaskContent->displayDate($content['TaskContent']['task_end_date'], $content['TaskContent']['is_date_set']); ?>
						</div>
					<?php else: ?>
						<div class="task-index-priority-1 task-word-break task-date-font">
							<?php echo $this->TaskContent->displayDate($content['TaskContent']['task_end_date'], $content['TaskContent']['is_date_set']); ?>
						</div>
						<?php echo $this->element('TaskContents/priority_icon', array(
								'priority' => $content['TaskContent']['priority'],
								'class' => 'task-index-priority-2'
							)
						); ?>
					<?php endif; ?>
				</td>
	
				<td class="col-xs-7 col-sm-5 col-md-5 col-lg-5 task-index-content-text-middle task-word-break">
					<div>
						<?php echo $this->Workflow->label($content['TaskContent']['status']); ?>
					</div>
					<?php echo $this->NetCommonsHtml->link(
						h($content['TaskContent']['title']),
							array(
								'controller' => 'task_contents',
								'action' => 'view',
								'key' => $content['TaskContent']['key']
						)
					); ?>
				</td>
	
				<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1 task-index-content-text-middle text-right">
					<?php echo $content['TaskContent']['progress_rate'] . __d('tasks', 'Progress rate percent'); ?>
				</td>
	
				<td class="hidden-xs col-md-sm4 col-md-3 col-lg-3 task-index-content-text-middle">
					<span class="nc-groups-avatar-list">
						<?php $count = 0; ?>
						<?php foreach ($content['TaskCharge'] as $userInCharge): ?>
							<?php
							$count++;
							if ($count <= TaskContent::LIST_DISPLAY_NUM) {
								echo $this->DisplayUser->avatar($userInCharge, [], 'user_id');
							} else {
								echo '...';
								break;
							}
							?>
						<?php endforeach; ?>
					</span>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</article>