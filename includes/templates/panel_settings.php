<br/>
<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'تنظیمات ملی پیامک';
$url = plugins_url('../', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<script>
	var $ = jQuery;
$(document).ready(function() {
	//$('#melipayamak-ibg').wpColorPicker();
	$('#melipayamak-cp').wpColorPicker();
});
</script>
<h2 class="nav-tab-wrapper"><a href="?page=melipayamak_setting" class="nav-tab <?php
if ($_GET['tab'] == '') { echo "nav-tab-active";
}
?>">عمومی</a><a href="?page=melipayamak_setting&tab=webservice" class="nav-tab <?php
if ($_GET['tab'] == 'webservice') { echo "nav-tab-active";
}
?>">وب سرویس</a><a href="?page=melipayamak_setting&tab=notifications" class="nav-tab <?php
if ($_GET['tab'] == 'notifications') { echo "nav-tab-active";
}
?>">اطلاع رسانی ها</a><a href="?page=melipayamak_setting&tab=newsletter" class="nav-tab <?php
if ($_GET['tab'] == 'newsletter') { echo "nav-tab-active";
}
?>">خبرنامه</a><a href="?page=melipayamak_setting&tab=form" class="nav-tab <?php
if ($_GET['tab'] == 'form') { echo "nav-tab-active";
}
?>">شخصی سازی فرم خبرنامه</a>
<a href="?page=melipayamak_setting&tab=woocommerce" class="nav-tab <?php
if ($_GET['tab'] == 'woocommerce') { echo "nav-tab-active";
}
?>">ووکامروس</a></h2>
<?php
if(isset($_GET['settings-updated']))
echo '<div id="message" class="updated below-h2"><p>انجام شد.</p></div><br/>'
?>
<table class="form-table">
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<?php
switch($_GET['tab']) {
case 'webservice' :
		?>
		<tr>
			<td>نام کاربری ملی پیامک</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="melipayamak_username" value="<?php echo get_option('melipayamak_username'); ?>" required/>
			<p class="description">
				نام کاربری خود را در ملی پیامک وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>کلمه عبور ملی پیامک</td>
			<td>
			<input type="password" style="width: 200px;" name="melipayamak_password" value="<?php echo get_option('melipayamak_password'); ?>" required/>
			<p class="description">
				کلمه عبور خود را در ملی پیامک وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>شماره ارسال کننده پیامک</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="melipayamak_tel" value="<?php echo get_option('melipayamak_tel'); ?>" required/>
			<p class="description">
				شماره ارسال کننده پیامک را وارد نمایید.
			</p></td>
		</tr>
		<input type="hidden" name="page_options" value="melipayamak_tel,melipayamak_password,melipayamak_username" />
		<?php
		break;
		case 'newsletter' :
	    ?>
	    <tr>
			<td>تایید اشتراک یا لغو اشتراک با ارسال کد؟</td>
			<td>
			<input type="checkbox" name="melipayamak_code" id="melipayamak_code" <?php echo get_option('melipayamak_code') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_code">برای تایید اشتراک  یا لغو اشتراک کاربر، کد تایید ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_code') == false ? 'style="display:none"' : ''; ?> id="melipayamak_code_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_code_text"><?php echo get_option('melipayamak_code_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کامل کاربر: <code>{name}</code> جنسیت کاربر به صورت آقای یا خانم: <code>{gender}</code>
						کد فعالسازی: <code>{code}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیغام خوش آمد گویی</td>
			<td>
			<input type="checkbox" name="melipayamak_welcome" id="melipayamak_welcome" <?php echo get_option('melipayamak_welcome') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_welcome">پس از عضویت برای کاربر پیغام خوش آمد گویی ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_welcome') == false ? 'style="display:none"' : ''; ?> id="melipayamak_welcome_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_welcome_text"><?php echo get_option('melipayamak_welcome_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کامل کاربر: <code>{name}</code> جنسیت کاربر به صورت آقای یا خانم: <code>{gender}</code>
						تاریخ عضویت: <code>{date}</code> شماره موبایل: <code>{mobile}</code>
					</p>
				</td>
			</tr>
		<input type="hidden" name="page_options" value="melipayamak_code,melipayamak_jquery,melipayamak_welcome_text,melipayamak_welcome,melipayamak_code_text" />
		<?php
		break;
		case 'woocommerce' :
		?>
		<tr>
			<td>ارسال پیام به مدیر هنگام سفارش جدید</td>
			<td>
			<input type="checkbox" name="melipayamak_wc" id="melipayamak_wc" <?php echo get_option('melipayamak_wc') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_wc">هنگام ثبت سفارش جدید در WooCommerce به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_wc') == false ? 'style="display:none"' : ''; ?> id="melipayamak_wc_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_wc_text"><?php echo get_option('melipayamak_wc_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام سفارش جدید</td>
			<td>
			<input type="checkbox" name="melipayamak_wc2" id="melipayamak_wc2" <?php echo get_option('melipayamak_wc2') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_wc2">هنگام ثبت سفارش جدید در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_wc2') == false ? 'style="display:none"' : ''; ?> id="melipayamak_wc2_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_wc2_text"><?php echo get_option('melipayamak_wc2_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
                </p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام تغییر وضعیت سفارش به در حال پردازش</td>
			<td>
			<input type="checkbox" name="melipayamak_wc3" id="melipayamak_wc3" <?php echo get_option('melipayamak_wc3') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_wc3">هنگام تغییر وضعیت سفارش به در حال پردازش در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_wc3') == false ? 'style="display:none"' : ''; ?> id="melipayamak_wc3_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_wc3_text"><?php echo get_option('melipayamak_wc3_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
                </p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام تکمیل سفارش</td>
			<td>
			<input type="checkbox" name="melipayamak_wc4" id="melipayamak_wc4" <?php echo get_option('melipayamak_wc4') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_wc4">هنگام تکمیل سفارش در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_wc4') == false ? 'style="display:none"' : ''; ?> id="melipayamak_wc4_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_wc4_text"><?php echo get_option('melipayamak_wc4_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
                </p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام نوشتن یادداشت برای سفارش</td>
			<td>
			<input type="checkbox" name="melipayamak_wc5" id="melipayamak_wc5" <?php echo get_option('melipayamak_wc5') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_wc5">هنگام نوشتن یادداشت برای سفارش در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_wc5') == false ? 'style="display:none"' : ''; ?> id="melipayamak_wc5_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_wc5_text"><?php echo get_option('melipayamak_wc5_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code> متن پیغام: <code>{text}</code>
                </p>
				</td>
			</tr>
            <tr>
                <td>تایید شماره موبایل کاربران قبل از ثبت سفارش</td>
                <td>
                    <input type="checkbox" name="melipayamak_wc_mobile_verification" id="melipayamak_wc_mobile_verification" <?php echo get_option('melipayamak_wc_mobile_verification') == true ? 'checked="checked"' : ''; ?>/>
                    <label for="melipayamak_wc_mobile_verification">قبل از ثبت سفارش، شماره موبایل کاربر تایید شود؟</label></td>
            </tr>
            <tr <?php echo get_option('melipayamak_wc_mobile_verification') == false ? 'style="display:none"' : ''; ?> id="melipayamak_wc_mobile_verification_text">
                <td scope="row">
                    متن پیامک
                </td><td>
                    <textarea cols="50"  rows="7" name="melipayamak_wc_mobile_verification_text"><?php echo get_option('melipayamak_wc_mobile_verification_text'); ?></textarea>
                    <p class="description">متن پیامک را وارد نمایید.</p>
                    <p class="description">
                        متغیر های قابل استفاده:	  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> کد تایید: <code>{code}</code>
                    </p>
                </td>
            </tr>
				<input type="hidden" name="page_options" value="melipayamak_wc,melipayamak_wc_text,melipayamak_wc2,melipayamak_wc2_text,melipayamak_wc3,melipayamak_wc3_text,melipayamak_wc4,melipayamak_wc4_text,melipayamak_wc5,melipayamak_wc5_text,melipayamak_wc_mobile_verification,melipayamak_wc_mobile_verification_text" />
	    <?php
		break;
		case 'notifications' :
		?>
		<tr>
			<td>ارسال پست های جدید به کاربران</td>
			<td>
			<input type="checkbox" name="melipayamak_send" id="melipayamak_send" <?php echo get_option('melipayamak_send') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_send">پست های جدید به عضو های خبرنامه ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_send') == false ? 'style="display:none"' : ''; ?> id="melipayamak_send_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_send_text"><?php echo get_option('melipayamak_send_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						عنوان نوشته: <code>{title}</code> تاریخ نوشته: <code>{date}</code>
						آدرس نوشته: <code>{url}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیامک هنگام نام نویسی کاربر به مدیر سایت</td>
			<td>
			<input type="checkbox" name="melipayamak_register" id="melipayamak_register" <?php echo get_option('melipayamak_register') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_register">هنگام نام نویسی کاربر جدید به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_register') == false ? 'style="display:none"' : ''; ?> id="melipayamak_register_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_register_text"><?php echo get_option('melipayamak_register_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ عضویت: <code>{date}</code>
						ایمیل کاربر: <code>{email}</code>
					</p>
				</td>
			</tr>
			<?php if(get_option('melipayamak_mfield') == true){ ?>
			<tr>
			<td>ارسال پیامک هنگام نام نویسی کاربر به کاربر</td>
			<td>
			<input type="checkbox" name="melipayamak_register2" id="melipayamak_register2" <?php echo get_option('melipayamak_register2') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_register2">هنگام نام نویسی کاربر جدید به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_register2') == false ? 'style="display:none"' : ''; ?> id="melipayamak_register2_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_register2_text"><?php echo get_option('melipayamak_register2_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ عضویت: <code>{date}</code>
						ایمیل کاربر: <code>{email}</code> کلمه عبور: <code>{password}</code>
					</p>
				</td>
			</tr>
			<tr>
			<td>ارسال پیامک هنگام درخواست برای بازیابی رمز عبور</td>
			<td>
			<input type="checkbox" name="melipayamak_lostpw" id="melipayamak_lostpw" <?php echo get_option('melipayamak_lostpw') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_lostpw">ارسال لینک بازیابی رمز عبور هنگام درخواست کاربر برای بازیابی رمز عبور</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_lostpw') == false ? 'style="display:none"' : ''; ?> id="melipayamak_lostpw_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_lostpw_text"><?php echo get_option('melipayamak_lostpw_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ درخواست: <code>{date}</code>
						ایمیل کاربر: <code>{email}</code> لینک بازیابی: <code>{link}</code>  لینک کوتاه شده بازیابی: <code>{slink}</code>
					</p>
				</td>
			</tr>
			<?php } ?>
		<tr>
			<td>ارسال پیامک هنگام ورود کاربر</td>
			<td>
			<input type="checkbox" name="melipayamak_login" id="melipayamak_login" <?php echo get_option('melipayamak_login') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_login">هنگام ورود کاربر به سایت به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_login') == false ? 'style="display:none"' : ''; ?> id="melipayamak_login_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_login_text"><?php echo get_option('melipayamak_login_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ: <code>{date}</code>
					</p>
				</td>
			</tr>
            </tr>
            <tr>
                <td>ورود ۲مرحله‌ای</td>
                <td>
                    <input type="checkbox" name="melipayamak_2fa" id="melipayamak_2fa" <?php echo get_option('melipayamak_2fa') == true ? 'checked="checked"' : ''; ?>/>
                    <label for="melipayamak_2fa">فعال کردن ورود ۲مرحله‌ای برای کاربران با وارد کردن کد تایید<br/>جهت استفاده از این مورد، نصب بودن پلاگین <a target="_blank" href="https://wordpress.org/plugins/two-factor/">Two-Factor</a> لازم می‌باشد. همچنین از فعال بودن دریافت شماره موبایل کاربران اطمینان حاصل کنید.</label></td>
            </tr>
            <tr <?php echo get_option('melipayamak_2fa') == false ? 'style="display:none"' : ''; ?> id="melipayamak_2fa_text">
                <td scope="row">
                    متن پیامک
                </td><td>
                    <textarea cols="50"  rows="7" name="melipayamak_2fa_text"><?php echo get_option('melipayamak_2fa_text'); ?></textarea>
                    <p class="description">متن پیامک را وارد نمایید.</p>
                    <p class="description">
                        متغیر های قابل استفاده:						کد تایید: <code>{token}</code>  تاریخ: <code>{date}</code>
                    </p>
                </td>
            </tr>
			<tr>
			<td>ارسال پیامک هنگام ثبت نام کاربر جدید در خبرنامه</td>
			<td>
			<input type="checkbox" name="melipayamak_nregister" id="melipayamak_nregister" <?php echo get_option('melipayamak_nregister') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_nregister">هنگام ثبت نام کاربر جدید در خبرنامه به شما پیامک ارسال شود؟</label></td>
		<tr <?php echo get_option('melipayamak_nregister') == false ? 'style="display:none"' : ''; ?> id="melipayamak_nregister_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_nregister_text"><?php echo get_option('melipayamak_nregister_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربر: <code>{username}</code>  شماره موبایل: <code>{mobile}</code>  تاریخ: <code>{date}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیامک هنگام ثبت دیدگاه جدید</td>
			<td>
			<input type="checkbox" name="melipayamak_comment" id="melipayamak_comment" <?php echo get_option('melipayamak_comment') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_comment">هنگام ثبت دیدگاه تازه به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_comment') == false ? 'style="display:none"' : ''; ?> id="melipayamak_comment_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_comment_text"><?php echo get_option('melipayamak_comment_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نویسنده: <code>{author}</code> ایمیل: <code>{email}</code> آیپی: <code>{ip}</code> تاریخ: <code>{date}</code>
						 متن دیدگاه: <code>{comment}</code>  وبسایت: <code>{url}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>Easy Digital Downloads</td>
			<td>
			<input type="checkbox" name="melipayamak_edd" id="melipayamak_edd" <?php echo get_option('melipayamak_edd') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_edd">هنگام ثبت سفارش جدید در Easy Digital Downloads به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('melipayamak_edd') == false ? 'style="display:none"' : ''; ?> id="melipayamak_edd_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="melipayamak_edd_text"><?php echo get_option('melipayamak_edd_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p> <p class="description">
						متغیر های قابل استفاده:					  تاریخ: <code>{date}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>Contact form 7</td>
			<td>
			<input type="checkbox" name="melipayamak_cf7" id="melipayamak_cf7" <?php echo get_option('melipayamak_cf7') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_cf7">هنگامی که یکی از فرم های این پلاگین تکمیل می شود، به شما پیام کوتاه ارسال شود؟</label></td>

</tr>
            <tr>
                <td>Gravity Forms</td>
                <td>
                    <input type="checkbox" name="melipayamak_gravity_forms" id="melipayamak_gravity_forms" <?php echo get_option('melipayamak_gravity_forms') == true ? 'checked="checked"' : ''; ?>/>
                    <label for="melipayamak_gravity_forms">فعال‌سازی هماهنگی با Gravity Forms</label></td>

            </tr>
		<input type="hidden" name="page_options" value="melipayamak_lostpw,melipayamak_lostpw_text,melipayamak_register2_text,melipayamak_register2,melipayamak_nregister_text,melipayamak_nregister,melipayamak_send,melipayamak_register,melipayamak_login,melipayamak_comment,melipayamak_edd,melipayamak_cf7,melipayamak_send_text,melipayamak_register_text,melipayamak_login_text,melipayamak_comment_text,melipayamak_edd_text,melipayamak_2fa_text,melipayamak_2fa,melipayamak_gravity_forms" />
		<?php
		break;
        case 'form':
		?>
		<div id="cp" style="display:none;"><div style="direction:rtl;text-align:right;font-family:Yekan"><h2>انتخاب رنگ</h2><p><input id="melipayamak-cp" /><br/>رنگ را انتخاب کرده، سپس آن را کپی کنید و در جای مورد نظر وارد کنید.</p></div></div>
		<div id="cp2" style="display:none;"><div style="direction:rtl;text-align:right;font-family:Yekan"><h2>نمونه فرم کوچک</h2><p style="text-align:center;margin:auto"><iframe src="index.php?melipayamak_mini=1" width="300px" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe></p><br/><div class="note">توجه فرمایید که تغییرات انجام شده پس از ذخیره نمایان خواهد شد.</div></div></div>
		<div id="cp3" style="display:none;"><div style="direction:rtl;text-align:right;font-family:Yekan"><h2>نمونه فرم بزرگ</h2><p style="text-align:center;margin:auto"><iframe src="index.php?melipayamak_large=1" width="580px" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe></p><br/><div class="note">توجه فرمایید که تغییرات انجام شده پس از ذخیره نمایان خواهد شد.</div></div></div>
		<div style="float:left;margin-top: 8px;"><a href="#TB_inline?width=300&height=380&inlineId=cp" class="thickbox add-new">انتخاب رنگ مناسب</a>
			<a href="#TB_inline?width=940&height=320&inlineId=cp2" class="thickbox add-new">مشاهده نمونه فرم کوچک</a>
			<a href="#TB_inline?width=840&height=320&inlineId=cp3" class="thickbox add-new">مشاهده نمونه فرم بزرگ</a>
		</div>
		<div class="note">
	 برای بکگراند ها می توانید هم کد HEX رنگ هارا وارد کنید و هم از دستورات CSS استفاده کنید.
		</div>
		<div class="note" style="margin-top:4px">
		برای رنگ بردر ها فقط کد HEX رنگ هارا وارد کنید.
		</div>
		<div class="note" style="margin-top:4px">
		برای رنگ متن ها فقط کد HEX رنگ هارا وارد کنید.
		</div>
		<div class="note" style="margin-top:4px">
		برای انتخاب رنگ ها می توانید از لینک روبرو نیز استفاده کنید.
		</div>
		<div class="note" style="margin-top:4px">
		قسمت هایی که باید بر حسب پیکسل وارد شود را بدون هیچ عبارت اضافی و فقط عدد را وارد کنید.
		</div>
		<div class="note" style="margin-top:4px"> برای سایر تغییرات مورد نظر می توانید پرونده های <code style="cursor:default">wp-content/plugins/melipayamak/includes/templates/form_mini.php</code> و <code style="cursor:default">wp-content/plugins/melipayamak/includes/templates/form_large.php</code> را ویرایش کنید.
		</div>
		<tr>
			<td>بکگراند ورودی های فرم کوچک</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color=""  class="mp-color-field" name="melipayamak_ibg" value="<?php echo get_option('melipayamak_ibg'); ?>" required/>
			<p class="description">
				رنگ بکگراند ورودی های فرم کوچک را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند ورودی های فرم کوچک در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color=""  class="mp-color-field" name="melipayamak_ihbg" value="<?php echo get_option('melipayamak_ihbg'); ?>" required/>
			<p class="description">
				بکگراند ورودی های فرم کوچک در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند ورودی های فرم بزرگ</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#fff" class="mp-color-field" name="melipayamak_ilbg" value="<?php echo get_option('melipayamak_ilbg'); ?>" required/>
			<p class="description">
				رنگ بکگراند ورودی های فرم بزرگ را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند ورودی های فرم بزرگ در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#fff"  class="mp-color-field" name="melipayamak_ilhbg" value="<?php echo get_option('melipayamak_ilhbg'); ?>" required/>
			<p class="description">
				بکگراند ورودی های فرم بزرگ در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر ورودی ها</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#e0dfdf"  class="mp-color-field" name="melipayamak_iborder" value="<?php echo get_option('melipayamak_iborder'); ?>" required/>
			<p class="description">
				رنگ بردر ورودی ها را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر ورودی ها در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#83b2d8"  class="mp-color-field" name="melipayamak_ihborder" value="<?php echo get_option('melipayamak_ihborder'); ?>" required/>
			<p class="description">
				بردر ورودی ها در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند دکمه تایید</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#579bd3" class="mp-color-field" name="melipayamak_sbg" value="<?php echo get_option('melipayamak_sbg'); ?>" required/>
			<p class="description">
				رنگ بکگراند دکمه تایید را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند دکمه تایید در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#4887bc"  class="mp-color-field" name="melipayamak_shbg" value="<?php echo get_option('melipayamak_shbg'); ?>" required/>
			<p class="description">
				بکگراند دکمه تایید در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر دکمه تایید</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;"  data-default-color="#4887bc"  class="mp-color-field" name="melipayamak_sborder" value="<?php echo get_option('melipayamak_sborder'); ?>" required/>
			<p class="description">
				رنگ بردر دکمه تایید ها را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر دکمه تایید در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;"  data-default-color="#4887bc" class="mp-color-field" name="melipayamak_shborder" value="<?php echo get_option('melipayamak_shborder'); ?>" required/>
			<p class="description">
				بردر دکمه تایید در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>رنگ فونت ها</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#818181"  class="mp-color-field" name="melipayamak_fontc" value="<?php echo get_option('melipayamak_fontc'); ?>" required/>
			<p class="description">
				رنگ فونت ها را انتخاب نمایید.
			</p></td>
		</tr>
		<tr>
			<td>رنگ فونت ها در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#6b9ecc"   class="mp-color-field" name="melipayamak_cfontc" value="<?php echo get_option('melipayamak_cfontc'); ?>" required/>
			<p class="description">
				رنگ فونت ها در حالت focus را انتخاب نمایید.
			</p></td>
		</tr>
		<tr>
			<td>رنگ بکگراند فرم در سایز بزرگ</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;"  data-default-color="#f5f5f5"  class="mp-color-field" name="melipayamak_form" value="<?php echo get_option('melipayamak_form'); ?>" required/>
			<p class="description">
				رنگ بکگراند فرم در سایز بزرگ را انتخاب نمایید.
			</p></td>
		</tr>
		<tr>
			<td>ضخامت بردر ها</td>
			<td>
			<input type="number" min="0" max="5" dir="ltr" style="width: 200px;" name="melipayamak_border" value="<?php echo get_option('melipayamak_border'); ?>" required/>
			<p class="description">
				ضخامت بردر های ورودی ها و دکمه تایید را به پیسکل وارد کنید.
			</p></td>
		</tr>
		<tr>
			<td>سایز فونت</td>
			<td>
			<input type="number" dir="ltr" min="1" style="width: 200px;" name="melipayamak_fonts" value="<?php echo get_option('melipayamak_fonts'); ?>" required/>
			<p class="description">
				سایز فونت ها را به پیکسل وارد کنید.
			</p></td>
		</tr>
		<tr>
			<td>font family</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="melipayamak_fontm" value="<?php echo get_option('melipayamak_fontm'); ?>" required/>
			<p class="description">
				font family را وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>border radius</td>
			<td>
			<input type="number" dir="ltr" min="0" style="width: 200px;" name="melipayamak_radius" value="<?php echo get_option('melipayamak_radius'); ?>" required/>
			<p class="description">
				border radius اینپوت ها و دکمه تایید را وارد نمایید.
			</p></td>
		</tr>
		<input type="hidden" name="page_options" value="melipayamak_ilhbg,melipayamak_ilbg,melipayamak_radius,melipayamak_cfontc,melipayamak_form,melipayamak_border,melipayamak_fontc,melipayamak_fonts,melipayamak_fontm,melipayamak_ibg,melipayamak_ihbg,melipayamak_iborder,melipayamak_ihborder,melipayamak_sbg,melipayamak_shbg,melipayamak_sborder,melipayamak_shborder" />
		<?php
		break;
		default:
		?>
		<tr>
			<td>شماره موبایل مدیر سایت</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="melipayamak_admin" value="<?php echo get_option('melipayamak_admin'); ?>" required/>
			<p class="description">
				شماره موبایل مدیر سایت را وارد نمایید.
                <br/>
                شماره هارا با <code>;</code> از هم جدا کنید.
			</p></td>
		</tr>
		<tr>
			<td>امضای پیامک ها</td>

	<td>			<textarea style="width: 200px;height:100px" name="melipayamak_sig"><?php echo get_option('melipayamak_sig'); ?></textarea>
			<p class="description">
				امضای پیامک ها را وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>صفحه بندی پلاگین</td>
			<td>
			<input type="number" dir="ltr" min="1" max="30" style="width: 200px;" name="melipayamak_page" value="<?php echo get_option('melipayamak_page'); ?>" required/>
			<p class="description">
				در هر صفحه چند رکورد قرار بگیرد؟
			</p></td>
		</tr>
		<tr>
			<td>دوره زمانی برای آپدیت اعتبار و بررسی برای آپدیت پلاگین</td>
			<td>
			<input type="number" min="1" max="12" dir="ltr" style="width: 200px;" name="melipayamak_update_period" value="<?php echo get_option('melipayamak_update_period'); ?>" required/>
			<p class="description">
				دوره زمانی جهت بررسی برای آپدیت پلاگین و سینک میزان اعتبار به ساعت
			</p></td>
		</tr>
		 <tr>
			<td>اضافه کردن فیلد شماره موبایل هنگام ثبت نام کاربران</td>
			<td>
			<input type="checkbox" name="melipayamak_mfield" id="melipayamak_mfield" <?php echo get_option('melipayamak_mfield') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_mfield">دریافت شماره موبایل از کاربران هنگام عضویت و ویرایش اطلاعات</label></td>
		</tr>
            <tr>
                <td>ارسال پیام صوتی</td>
                <td>
                    <input type="checkbox" name="melipayamak_use_voice" id="melipayamak_use_voice" <?php echo get_option('melipayamak_use_voice') == true ? 'checked="checked"' : ''; ?>/>
                    <label for="melipayamak_use_voice">در صورتی که ارسال پیام متنی با مشکل مواجه شود، برای کاربر پیام صوتی ارسال خواهد شد.</label></td>
            </tr>
		 <tr>
			<td>سینک دفترچه تلفن با ملی پیامک؟</td>
			<td>
			<input type="checkbox" name="melipayamak_sync" id="melipayamak_sync" <?php echo get_option('melipayamak_sync') == true ? 'checked="checked"' : ''; ?>/>
			<label for="melipayamak_sync">شماره هایی که در دفترچه تلفن پلاگین شما اضافه می شود به دفترچه تلفن اصلی شما در ملی پیامک اضافه شود؟</label></td>
		</tr>
		<tr>
				<td>گروه دفترچه تلفن</td>
			<td><?php echo $melipayamak -> fetch_groups(true, get_option('melipayamak_group')); ?>
			<p class="description">
				کاربران سینک شده از پلاگین، در دفترچه تلفن اصلی شما در ملی پیامک در کدام گروه قرار بگیرند؟
	<br/>
	توجه کنید منظور از گروه در این قسمت گروه های ساخته شده در پنل SMS شما می باشد نه گروه های ساخته شده از پلاگین.
			</p></td>
			</tr>
		<input type="hidden" name="page_options" value="melipayamak_mfield,melipayamak_group,melipayamak_admin,melipayamak_sig,melipayamak_page,melipayamak_update_period,melipayamak_sync,melipayamak_use_voice" />
		<?php
		break;
		}
		?>
		<tr style="background:none">

			<td>
			<p class="submit">
				<input type="hidden" name="action" value="update" />
				<input type="submit" class="button-primary" name="Submit" value="بروزرسانی" />
			</p></td>
		</tr>
	</form>
</table>
<?php
include dirname(__FILE__) . '/footer.php';
?>