<?php
/**
 * TaskContents select sort for view element
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

if (!isset($params['sort']) || !$params['direction']) :
	$currentSort = 'TaskContent.task_end_date.desc';
else:
	$currentSort = $params['sort'] . '.' . $params['direction'];
endif;
$options = array();

$options = array(
	'TaskContent.task_end_date.desc' => array(
		'label' => __d('tasks', 'Close of the deadline order'),
		'sort' => 'TaskContent.task_end_date',
		'direction' => 'asc'
	),
	'TaskContent.priority.desc' => array(
		'label' => __d('tasks', 'Priority order'),
		'sort' => 'TaskContent.priority',
		'direction' => 'desc'
	),
	'TaskContent.progress_rate.desc' => array(
		'label' => __d('tasks', 'High progress rate order'),
		'sort' => 'TaskContent.progress_rate',
		'direction' => 'desc'
	),
	'TaskContent.progress_rate.asc' => array(
		'label' => __d('tasks', 'Low progress rate order'),
		'sort' => 'TaskContent.progress_rate',
		'direction' => 'asc'
	),
);
?>

<span class="btn-group">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
			style="width: auto">
		<?php echo h($options[$currentSort]['label']); ?>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ($options as $key => $sort) : ?>
			<li>
				<?php echo $this->NetCommonsHtml->link($sort['label'],
					Hash::merge($url, array('sort' => $sort['sort'], 'direction' => $sort['direction']))
				); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
