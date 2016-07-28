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
$url = Hash::merge(array(
	'controller' => 'task_contents',
	'action' => 'index'),
	$params);

if (!isset($params['is_completion'])) :
	$currentIsCompletion = 0;
else:
	$currentIsCompletion = $params['is_completion'];
endif;

$options = array();

$options = array(
	'TaskContents.is_completion.' . 0 => array(
		'label' => __d('tasks', 'Incomplete task'),
		'is_completion' => 0,
	),
	'TaskContents.is_completion.' . 1 => array(
		'label' => __d('tasks', 'Completed task'),
		'is_completion' => 1,
	),
	'TaskContents.is_completion.' . 'all' => array(
		'label' => __d('tasks', 'All task'),
		'is_completion' => 'all',
	),
);
?>

<span class="btn-group">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
			style="width: 125px">
		<?php echo h($options['TaskContents.is_completion.' . $currentIsCompletion]['label']); ?>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ($options as $key => $status) : ?>
			<li>
				<?php echo $this->NetCommonsHtml->link($status['label'],
					Hash::merge($url, array('is_completion' => $status['is_completion']))
				); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
