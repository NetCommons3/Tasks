<?php
/**
 * TaskContents select is_completion for view element
 *
 * @author Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$params = $this->params['named'];
$url = array_merge(array(
	'controller' => 'task_contents',
	'action' => 'index'),
	$params);
if (! $currentIsCompletion):
	$currentIsCompletion = TasksComponent::TASK_CONTENT_INCOMPLETE_TASK;
endif;
?>

<span class="btn-group task-index-space">
	<button type="button"
			class="btn btn-default category-dropdown-toggle dropdown-toggle"
			data-toggle="dropdown" aria-expanded="false">
		<div class="clearfix">
			<div class="pull-left">
			<?php echo h($options['TaskContents.is_completion.' . $currentIsCompletion]['label']); ?>
		</div>
		<div class="pull-right">
			<span class="caret"></span>
		</div>
		</div>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ($options as $key => $status) : ?>
			<li>
				<?php echo $this->NetCommonsHtml->link($status['label'],
					array_merge($url, array('is_completion' => $status['is_completion']))
				); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
