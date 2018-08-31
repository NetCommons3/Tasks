<?php
/**
 * TaskCharge Behavior
 *
 * @author   Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('ModelBehavior', 'Model');

/**
 * TaskCharge Behavior
 *
 * @author   Yuto Kitatsuji <kitatsuji.yuto@withone.co.jp>
 * @package NetCommons\Tasks\Model\Behavior
 */
class TaskChargeBehavior extends ModelBehavior {

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		$model->loadModels(array(
				'TaskCharge' => 'Tasks.TaskCharge',
				'User' => 'Users.User'
			)
		);

		// ToDo担当者のバリデーション処理
		if (! isset($model->data['TaskCharge'])) {
			$model->data['TaskCharge'] = array();
		}
		$model->TaskCharge->set($model->data['TaskCharge']);

		$userIdArr = [];
		foreach ($model->data['TaskCharge'] as $datum) {
			$userIdArr[] = $datum['user_id'];
		}
		if (! $model->TaskCharge->validates()) {
			$model->validationErrors =
				array_merge($model->validationErrors, $model->TaskCharge->validationErrors);
			return false;
		}
		if (count($userIdArr) > 0 && ! $model->User->existsUser($userIdArr)) {
			$model->TaskCharge->validationErrors['user_id'][] =
				sprintf(__d('net_commons', 'Failed on validation errors. Please check the input data.'));
			$model->validationErrors =
				array_merge($model->validationErrors, $model->TaskCharge->validationErrors);
			return false;
		}

		return true;
	}

}
