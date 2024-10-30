<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'فرم عضویت خبرنامه';
$url = plugins_url('../', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<div id="shortcode" style="display:none;">
<div style="direction:rtl;text-align:right;font-family:Yekan;">
	<h2>
		مستندات شرت کد پلاگین ملی پیامک
</h2>
     <p>
        برای استفاده از فرم عضویت در صفحه ها یا در نوشته ها می توانید کد زیر را در برگه یا نوشته خود قرار دهید:
        <div style="float:left;direction:ltr;text-align:left">
        <code style="cursor:default">[melipayamak mode='1' width='300']</code>
        </div>
        <div style="clear:both"></div>
        <h2>
	پارامتر های ورودی شرت کد
</h2>
<ul style="list-style: decimal;margin-right:10px;font-family:Mitra,Oxygen;font-size:16px">
	<li> <code style="cursor:default">mode</code>: مشخص کننده نوع فرم می باشد. نمونه فرم ها در همین صفحه موجود هست.</li>
	<li> <code style="cursor:default">width</code>: مشخص کننده width فرم عضویت می باشد. استفاده از دستورات CSS مجاز است.</li>
</ul>
     </p>
</div></div>
<div class="clear"></div>
<div style="margin-top:6px"></div>
<a href="admin.php?page=melipayamak_setting&tab=form" class="add-new">شخصی سازی فرم ثبت نام</a>
<a href="admin.php?page=melipayamak_setting&tab=newsletter" class="add-new">مدیریت تنظیمات خبرنامه</a>
<div style="text-align: center">
<div class="clear"></div><br/><br/>
برای قرار دادن فرم ثبت نام در سایت خود، می توانید شرت کد <a  href="#TB_inline?width=300&height=210&inlineId=shortcode" class="thickbox"><p style="display:inline"><code>[melipayamak]</code></p></a> را در نوشته های خود قرار دهید یا از طریق <a href="widgets.php">ابزارک ها</a> فرم را در سایدبار قرار دهید.
<br/><br/>
همچنین می توانید از کد های زیر نیز استفاده کنید:
<br/>
<h2>فرم یک</h2>
<h6>پیش نمایش:</h6>
<iframe src="<?php bloginfo('url') ?>/?melipayamak_mini=1" width="300px" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe>
<br/>
<h6>کد:</h6>
<br/>
<textarea onclick="this.select()" style="margin: 2px;width: 760px;height: 59px;border-radius:3px;direction:ltr;text-align:left"><iframe src="<?php bloginfo('url') ?>/?melipayamak_mini=1" width="300px" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe></textarea>
<br/>
<h2>فرم دو</h2>
<h6>پیش نمایش:</h6>
<iframe src="<?php bloginfo('url') ?>/?melipayamak_large=1" width="760px" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe>
<br/>
<h6>کد:</h6>
<br/>
<textarea onclick="this.select()" style="margin: 2px;width: 760px;height: 59px;border-radius:3px;direction:ltr;text-align:left"><iframe src="<?php bloginfo('url') ?>/?melipayamak_large=1" width="760px"  onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe></textarea>
<div class="clear"></div>
<br/>
<div class="note">
	توجه کنید که در کد های بالا برای تغییر width فرم باید در قسمت <code style="cursor: default">width="300px"</code> جای  <code style="cursor: default">300px</code> عدد مورد نظر یا دستور CSS دلخواه خود را وارد نمایید.
</div>
</div>
<?php
include dirname(__FILE__) . '/footer.php';
?>
