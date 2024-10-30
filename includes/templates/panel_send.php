<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'ارسال پیامک';
$url = plugins_url('../', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<div id="flash" style="display:none;">
	<div style="direction:rtl;text-align:right;font-family:Yekan">
		<h2> اس ام اس فلش چیست؟ </h2>
		<p>
			ارسال پیامک Flash مشابه پیامک معمولی بوده با این تفاوت که پیامک به صورت خودکار بر روی گوشی گیرنده باز خواهد شده ، همچنین این نوع پیامک قابلیت ذخیره شدن در گوشی را ندارد
		</p>
	</div>
</div>
<form method="post" action="">
	<table class="form-table">
		<tbody>
			<tr>
				<td>ارسال کننده</td>
				<td>
				<input type="text" disabled readonly value="<?php echo $melipayamak -> tel ?>" />
				<p class="description">
					شماره ارسال کننده پیامک
				</p></td>
			</tr>
			<tr>
				<td>ارسال فلش</td>
				<td>
				<input type="checkbox" name="melipayamak_flash" id="melipayamak_flash">
				<label for="melipayamak_flash">پیام به صورت فلش ارسال شود؟ <a href="#TB_inline?width=300&height=130&inlineId=flash" class="thickbox">اطلاعات بیشتر</a></label></td>
			</tr>
			<tr>
				<td>دریافت کنندگان</td>
				<td>
				<select id="mpst" style='width: 200px;height: 34px;' name="to" required>
					<option disabled selected value="">انتخاب کنید.</option>
					<option value="all">تمام مشترکین</option>
					<?php echo $melipayamak -> fetch_phonebook_groups(true, '', false); ?>
					<?php if(get_option('melipayamak_mfield') == true){
						$utn = $wpdb -> get_results("SELECT count(meta_value) FROM {$table_prefix}usermeta WHERE meta_key = 'mpmobile' AND meta_value != ''",ARRAY_A);
						$utn=number_format($utn[0]['count(meta_value)']);
                    ?>
					<option value="users">کاربران سایت(<?php echo $utn ?> مورد)</option>
					<?php } ?>
					<option value="custom">شماره های دلخواه</option>
				</select>
				<p class="description">
					پیامک را چه کسانی دریافت کنند؟
				</p></td>
			</tr>
			<tr style="display:none" id="mpsn">

			</tr>
			<tr>
				<td>متن پیامک</td>
				<td>				<textarea id="mpsm" cols="50"  rows="7" name="text" required><?php echo $text ?></textarea>
				<p class="description">
					متن پیامک را وارد نمایید.
				</p>
				<div style="margin-top:6px;">
					<div style="display:inline;margin-top:2" class="lenght">
						طول پیام <span class="one">0</span> از <span class="two">160</span>
					</div>
					<div style="display:inline;margin-right:10px" class="count">
						تعداد پیامک ها: <span class="one">1</span>
					</div>
					<div style="display:inline;margin-right:10px;margin-bottom:10px" class="credit">
						اعتبار شما: <span class="one"><?php echo intval($melipayamak -> credit) ?></span> پیامک
					</div>
					</div>
				<p class="description variable">
					متغیر های قابل استفاده:						نام کامل کاربر:
					<code class="refresh">{name}</code>
					جنسیت به صورت خانم یا آقای:
					<code class="refresh">{gender}</code>
					شماره موبایل:
					<code class="refresh">{mobile}</code>
					<br/>
					<div style="margin-top:3px" class="note variable">
						توجه کنید که استفاده از متغیر ها موجب طول کشیدن ارسال خواهد شد و همچنین موجب می شود طول پیامک ها متفاوت باشد.
					</div>
				</p></td>
			</tr>
			<tr style="background:none">
				<td>
				<p class="submit">
					<input type="hidden" name="mcount" id="mcount" value="0" />
					<?php wp_nonce_field('mpsaction', 'mpsactionf'); ?>
					<input type="submit" class="button-primary" name="Submit" value="ارسال">
				</p></td>
			</tr>
		</tbody>
		</table>
</form>
<?php
if (isset($_GET['to'])) {
	echo '<script>jQuery("#mpst").val("custom").attr("data-to","' . melipayamak_clean($_GET['to']) . '");</script>';
}
include dirname(__FILE__) . '/footer.php';
?>
