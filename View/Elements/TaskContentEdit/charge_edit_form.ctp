<?php
/**
 * task edit charge element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.Yuto@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="form-group" ng-controller="TaskCharge">
	<label class="control-label">
		<?php echo h(__d('tasks', 'Charge of person in task')); ?>
	</label>

	<div class="form-group">
		<div>
			<?php
			$title = __d('tasks', 'Person in charge of user selection');
			$pluginModel = 'TaskCharge';
			$roomId = Current::read('Room.id');
			$selectUsers = (isset($this->request->data['selectUsers'])) ? $this->request->data['selectUsers'] : null;
			echo $this->GroupUserList->select($title, $pluginModel, $roomId, $selectUsers);
			?>
		</div>
		<div class="has-error">
			<?php echo $this->NetCommonsForm->error('TaskCharge.user_id', null, array('class' => 'help-block')); ?>
		</div>
	</div>
</div>
