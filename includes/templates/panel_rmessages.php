<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = '100 پیامک آخر دریافتی';
$url = plugins_url('../', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<div class="mksearch alignleft actions">
<form action="admin.php" method="get" >
<input type="hidden" name="page" value="melipayamak_rmessages" />
<input type="search" name="search" value="<?php echo $search ?>" />
<input class="button button-primary button-large" type="submit" value="فیلتر بر اساس گیرنده" />
</form>
</div>
<div class="alignright actions pagination">
نمایش <?php echo count($sms) ?> مورد
</div>
<form action="" method="post">
	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-cb check-column">
				<input type="checkbox" name="checkAll"/>
				</th>
				<th scope="col" width="5%">ردیف</th>
				<th scope="col" width="10%">ارسال کننده</th>
				<th scope="col" width="50%">متن پیامک</th>
				<th scope="col" width="20%">تاریخ ارسال</th>
				<th scope="col" width="15%">شماره دریافت کننده</th>
			</tr>
		</thead>

		<tbody>
			<?php
if ($zero === true)
echo '<td colspan="6" style="text-align:center">هیچ پیامکی یافت نشد.</td>';
else{
	$num=0;
	date_default_timezone_set('Asia/Tehran');
foreach($sms as $key=>$value){
	$num++;
	$date=$melipayamak -> date(strtotime($value['SendDate']));
	$to="<a href='index.php?mprd={$value['Sender']}&id={$value['MsgID']}&width=600&height=100' class='thickbox'>{$value['Sender']} (مشاهده اطلاعات)</a>";
			?>
			<tr>
				<th class="check-column" scope="row">
				<input type="checkbox" data-to="<?php echo $value['Sender'] ?>" name="id[<?php echo $value['MsgID'] ?>]">
				</th>
				<td><?php echo $num ?></td>
				<td><?php echo $to ?><br/><a target="_blank" href="admin.php?page=melipayamak_send&to=<?php echo $value['Sender']; ?>">ارسال پاسخ</a></td>
				<td><?php echo $melipayamak ->nl2br($value['Body']) ?></td>
				<td><?php echo $date ?></td>
				<td><a title="فیلتر سازی این گیرنده" href="admin.php?page=melipayamak_rmessages&search=<?php echo $value['Receiver'] ?>"><?php echo $value['Receiver'] ?></a></td>
			</tr>
			<?php  } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column">
				<input type="checkbox" name="checkAll"/>
				</th>
				<th scope="col" width="5%">ردیف</th>
				<th scope="col" width="10%">ارسال کننده</th>
				<th scope="col" width="50%">متن پیامک</th>
				<th scope="col" width="20%">تاریخ ارسال</th>
				<th scope="col" width="15%">شماره دریافت کننده</th>
			</tr>
		</tfoot>
	</table>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action" required>
				<option value="" selected disabled>اعمال گروهی</option>
				<option value="delete">حذف</option>
				<option value="reply">ارسال‌ پاسخ</option>
			</select>
			<?php wp_nonce_field('mpraction','mpractionf'); ?>
			<input value="اعمال کن!" class="button-secondary action" type="submit"/>
		</div>
	</div>
</form>
<br/>
<div class="note">
توجه فرمایید که اعمال گروهی در این حالت فشار زیادی به سرور شما می آورد و ممکن است تا چند دقیقه طول بکشد. پیشنهاد ما این است که از پنل پیامک اقدام به پاک کردن پیام ها فرمایید یا حداکثر 5 یا 6 پیامک را از اینجا انتخاب کنید.
</div>
<?php
include dirname(__FILE__) . '/footer.php';
?>
