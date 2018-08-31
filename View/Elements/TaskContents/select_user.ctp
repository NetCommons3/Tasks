<?php
/**
 * TaskContents select user for view element
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
if (! $currentUserId) :
	$currentUserId = Current::read('User.id');
endif;
?>

<span class="btn-group task-index-space">
	<button type="button"
			class="btn btn-default dropdown-toggle"
			data-toggle="dropdown" aria-expanded="false">
		<span class="pull-left task-select-ellipsis nc-drop-down-ellipsis">
			<?php echo h($options['TaskContents.charge_user_id_' . $currentUserId]['label']); ?>
		</span>
		<span class="pull-right">
			<span class="caret"></span>
		</span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ($options as $key => $currentUserId) : ?>
			<li>
				<?php echo $this->NetCommonsHtml->link($currentUserId['label'],
					array_merge($url, array('user_id' => $currentUserId['user_id']))
				); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
