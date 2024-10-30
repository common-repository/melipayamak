<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
?>
<div class="meta-box-sortables ui-sortable">
	<div id="maildiv" class="postbox">
		<div class="handlediv">
			<br/>
		</div>
		<h3 class="hndle"><span>ارسال پیامک با استفاده از ملی پیامک</span></h3>
		<div class="inside">
			<div class="mail-field">
				<label>متن پیام کوتاه به مدیر:
				</label>
				<br/>
				<textarea name="wpcf7_melipayamak_admin" cols="100" rows="2"><?php echo $admin_message ?></textarea>
			</div>
            <div class="mail-field">
                <label>فیلد شماره‌موبایل کاربر:
                </label>
                <br/>
                <input name="wpcf7_melipayamak_mobile_field" value="<?php echo $mobile_field ?>" />
            </div>
            <div class="mail-field">
                <label">متن پیام کوتاه به کاربر:
                </label>
                <br/>
                <textarea name="wpcf7_melipayamak_user" cols="100" rows="2"><?php echo $user_message ?></textarea>
            </div>
		</div>
	</div>
</div>