<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'پلاگین وردپرس ملی پیامک نسخه ' . $melipayamak_version;
include dirname(__FILE__) . '/head.php';
$url = plugins_url('/assets/mainimg/', __FILE__);
?>
<br/>
<table id="f2">
	<tr>
		<td><a href="admin.php?page=melipayamak_setting"><img src="<?php echo $url ?>settings.png" alt="settings" /></a></td>
		<td><a href="admin.php?page=melipayamak_smessages"><img src="<?php echo $url ?>smessage.png" alt="smessages" /></a></td>
		<td><a href="admin.php?page=melipayamak_rmessages"><img src="<?php echo $url ?>rmessage.png" alt="rmessages" /></a></td>
		<td><a href="admin.php?page=melipayamak_send"><img src="<?php echo $url ?>send.png" alt="send" /></a></td>
		<td><a href="admin.php?page=melipayamak_groups"><img src="<?php echo $url ?>groups.png" alt="groups" /></a></td>
		<td><a href="admin.php?page=melipayamak_phonebook"><img src="<?php echo $url ?>phonebook.png" alt="phonebook" /></a></td>
		<td><a href="admin.php?page=melipayamak_reports"><img src="<?php echo $url ?>reports.png" alt="reports" /></a></td>
		<td><a href="admin.php?page=melipayamak_support"><img src="<?php echo $url ?>support.png" alt="support" /></a></td>
		<td><a href="admin.php?page=melipayamak_form"><img src="<?php echo $url ?>newsletter.png" alt="newsletter" /></a></td>
	</tr>
	<tr id="f2a">
		<td><a href="admin.php?page=melipayamak_setting">تنظیمات</a></td>
		<td><a href="admin.php?page=melipayamak_smessages">پیام های ارسالی</a></td>
		<td><a href="admin.php?page=melipayamak_rmessages">پیام های دریافتی</a></td>
		<td><a href="admin.php?page=melipayamak_send">ارسال پیام</a></td>
		<td><a href="admin.php?page=melipayamak_groups">گروه ها</a></td>
		<td><a href="admin.php?page=melipayamak_phonebook">دفترچه تلفن</a></td>
		<td><a href="admin.php?page=melipayamak_reports">گزارشات</a></td>
		<td><a href="admin.php?page=melipayamak_support">پشتیبانی</a></td>
		<td><a href="admin.php?page=melipayamak_form">فرم خبرنامه</a></td>
	</tr>
</table>
<!-- <br/>
<h2>امکانات پلاگین</h2>
<ul id="fstyle">
	<li>
		سادگی و زیبایی در عین قدرت و امکانات زیاد
	</li>
	<li>
		قرار دادن امضای پیامک ها
	</li>
	<li>
		سینک دفترچه تلفن
	</li>
	<li>
		ارسال پیامک به کاربران هنگام ارسال پست جدید
	</li>
	<li>
		ارسال پیامک به مدیر هنگام نام نویسی کاربر
	</li>
	<li>
		ارسال پیامک به مدیر هنگام ورود کاربر به سایت
	</li>
	<li>
		ارسال پیامک به مدیر هنگام نام نویسی کاربر در خبرنامه
	</li>
	<li>
		ارسال پیامک به مدیر هنگام ثبت دیدگاه تازه
	</li>
	<li>
		ارسال پیامک به مدیر هنگام ثبت سفارش در WooCommerce
	</li>
	<li>
		ارسال پیامک به مدیر هنگام ثبت سفارش جدید در Easy Digital Downloads
	</li>
	<li>
		ارسال پیامک به مدیر هنگام ارسال فرم جدید در افزونه فرم تماس
	</li>
	<li>
		ارسال پیغام خوش آمد گویی به کاربران هنگام ثبت نام در خبرنامه
	</li>
	<li>
		ارسال کد فعال سازی برای عضویت در خبرنامه
	</li>
	<li>
		آرشیو کلیه پیام های ارسالی
	</li>
	<li>
		مشاهده 100 پیامک آخر دریافتی
	</li>
	<li>
		تغییر متن کلیه پیامک های ارسالی توسط سیستم
	</li>
	<li>
		ارسال پیامک به صورت فلش
	</li>
	<li>
		ارسال پیامک به گروه ها با قابلیت درج نام، جنسیت و شماره تلفن در متن پیام
	</li>
	<li>
		گروه بندی دفترچه تلفن
	</li>
	<li>
		مدیریت کامل دفترچه تلفن
	</li>
	<li>
		تهیه پشتیبان و بازگردانی پشتیبان از دفترچه تلفن و گروه های دفترچه تلفن
	</li>
	<li>
		رسم نمودار های گزارشی برای پیامک های ارسالی و کاربران خبرنامه در بازه های زمانی مختلف
	</li>
	<li>
		ثبت تیکت جدید، مشاهده پاسخ های تیکت و ارسال پاسخ برای تیکت از پلاگین
	</li>
	<li>
		نمایش تعداد اعضای خبرنامه و میزان اعتبار در ادمین بار وردپرس
	</li>
	<li>
		صفحه بندی قدرمتند، ساده و جدید با قابلیت تعیین تعداد رکورد های موجود در هر صفحه
	</li>
	<li>
		جست و جوی قدرتمند و ساده در تمام قسمت های پلاگین
	</li>
	<li>
		بررسی کردن برای بروزرسانی پلاگین
	</li>
	<li>
		فرم عضویت در خبرنامه با قابلیت درج در سایت به صورت ابزارک، شرت کد یا iframe
	</li>
	<li>
		امکان شخصی سازی کامل فرم عضویت در خبرنامه
	</li>
	<li>
		برنامه نویسی فرم عضویت در خبرنامه به صورت ای جکس
	</li>
</ul> -->
<div id="f2c">
لطفا در صورت مشاهده هرگونه اشکال در پلاگین یا داشتن هرگونه سوال با ایمیل
<a style="text-decoration: none" href="mailto:support@melipayamak.com">
<code>support@melipayamak.com</code></a>
در ارتباط باشید.</div><?php
include dirname(__FILE__) . '/footer.php';
?>
