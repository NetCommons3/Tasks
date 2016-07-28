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
$url = Hash::merge(array(
	'controller' => 'task_contents',
	'action' => 'index'),
	$params);

if (!isset($params['user_id'])) :
	$currentChargeUser = 0;
else:
	$chargeUser = $params['user_id'];
endif;

$options = array();

// DBより内容を持ってくる
$options = array(
	'TaskContents.charge_user_' . 0 => array(
		'label' => __d('tasks', 'No person in charge'),
		'user_id' => 0,
	),
);
?>

<span class="btn-group">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
			style="width: auto">
		<?php echo h($options['TaskContents.charge_user_' . $currentChargeUser]['label']); ?>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ($options as $key => $chargeUser) : ?>
			<li>
				<?php echo $this->NetCommonsHtml->link($chargeUser['label'],
					Hash::merge($url, array('user_id' => $chargeUser['user_id']))
				); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</span>
