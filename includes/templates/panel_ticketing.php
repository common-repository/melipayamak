<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'پشتیبانی';
$url = plugins_url('/assets/', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<div id="new" style="display:none;">
<div style="direction:rtl;text-align:right;font-family:Yekan">
	<h2>
افزودن  تیکت تازه
</h2>
     <p>
         <form action="" method="post">
         	<input type="text" name="name" placeholder="عنوان تیکت" required/>
         	<br/><br/>
         	<textarea name="message" placeholder="متن تیکت" required></textarea>
         	<br/><br/>
         	<input type="hidden" name="do" value="new" />
         	<?php wp_nonce_field('mptaaction', 'mptaactionf'); ?>
         	<input type="submit" value="ثبت تیکت"/>
         </form>
     </p>
</div></div>
<div class="clear"></div>
<div style="margin-top:6px"></div>
<a  href="#TB_inline?width=300&height=380&inlineId=new" class="thickbox add-new">ثبت تیکت جدید</a>
<div class="clear"></div>
<br/>
<div class="mksearch alignleft actions">
<form action="admin.php" method="get" >
<input type="hidden" name="page" value="melipayamak_support" />
<input type="search" name="search" value="<?php echo $search ?>" />
<input class="button button-primary button-large" type="submit" value="بگرد!" />
</form>
</div>
<div class="alignright actions pagination">
نمایش <?php echo count($ticket) ?> مورد
</div><div class="clear"></div>
<form action="" method="post">
	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" width="5%">ردیف</th>
				<th scope="col" width="10%">عنوان</th>
				<th scope="col" width="40%">محتوا</th>
				<th scope="col" width="20%">تاریخ ارسال</th>
				<th scope="col" width="15%">وضعیت</th>
				<th scope="col" width="10%">ارسال جواب</th>
			</tr>
		</thead>

		<tbody>
			<?php
if ($zero === true)
echo '<td colspan="6" style="text-align:center">هیچ تیکتی یافت نشد.</td>';
else{
	$num=0;
foreach($ticket as $key=>$value){
	$num++;
	switch($value['TicketStatus']){
		case '1':
		$status ='جواب داده شده توسط مدیر';
		break;
		case '2':
		$status ='جواب داده شده توسط کاربر';
		break;
		case '3':
		$status ='بسته شده';
		break;
		default:
		$status='ملاحظه شده';
		break;
	}
	$date=$melipayamak -> date(strtotime($value['InsertDate']));
			?>
			<tr>
				<td><?php echo $num ?></td>
				<td><?php echo $value['Title'] ?></td>
				<td><?php echo $melipayamak -> nl2br2($value['Contents']) ?></td>
				<td><?php echo $date ?></td>
				<td><?php echo $status ?></td>
				<td><a href="index.php?melipayamak_ticket=true&tid=<?php echo $value['TicketID'] ?>" class="thickbox">ارسال جواب</a></td>
			</tr>
			<?php  } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th scope="col" width="5%">ردیف</th>
				<th scope="col" width="10%">عنوان</th>
				<th scope="col" width="40%">محتوا</th>
				<th scope="col" width="20%">تاریخ ارسال</th>
				<th scope="col" width="15%">وضعیت</th>
				<th scope="col" width="10%">ارسال جواب</th>
			</tr>
		</tfoot>
	</table>
<?php
include dirname(__FILE__) . '/footer.php';
?>
