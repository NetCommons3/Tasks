<?php
/**
 * メール設定 template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Yuto Kitatsuji <kitatsuji.yuto@wihtone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>

	<div class="tab-content">
		<?php echo $this->BlockTabs->block(BlockTabsHelper::MAIN_TAB_MAIL_SETTING); ?>

		<?php echo $this->MailForm->editFrom(
			array(array(
				'mailBodyPopoverMessage' => __d('cabinets', 'MailSetting.mail_fixed_phrase_body.popover'),
			)),
			NetCommonsUrl::backToIndexUrl('default_setting_action'),
			0,
			1
		); ?>
	</div>
</div>
