<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'پیام های ارسالی';
$url = plugins_url('../', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<div class="mksearch alignleft actions">
<form action="admin.php" method="get" >
<input type="hidden" name="page" value="melipayamak_smessages" />
<input type="search" name="search" value="<?php echo $search ?>" />
<input class="button button-primary button-large" type="submit" value="بگرد!" />
</form>
</div>
<div class="alignright actions pagination">
<?php $pages->show() ?>
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
				<th scope="col" width="15%">تاریخ ارسال</th>
				<th scope="col" width="5%">فلش</th>
				<th scope="col" width="15%">شماره دریافت کننده</th>
			</tr>
		</thead>

		<tbody>
			<?php
if ($zero === true)
echo '<td colspan="7" style="text-align:center">هیچ پیامکی یافت نشد.</td>';
else{
	$num = $pages -> low;
foreach($sms as $key=>$value){
	$num++;
	$date=explode(' ',$value['date']);
	$date=$date[0];
	$to=explode(',',$value['recipient']);
	$flash=($value['flash']==1)? 'بلی' : 'خیر';
	if(!$to[1]){
		$to="<a href='index.php?mpsd={$value['id']}&width=600&height=400' class='thickbox'>{$to[0]} (مشاهده اطلاعات)</a>";
	}else{
		$to="<a href='index.php?mpsd={$value['id']}&width=400' class='thickbox'>شماره ها (مشاهده اطلاعات)</a>";
	}
			?>
			<tr class="<?php if($value['mode']==1) echo 'auto' ?>">
				<th class="check-column" scope="row">
				<input type="checkbox" name="id[<?php echo $value['id'] ?>]">
				</th>
				<td><?php echo $num ?></td>
				<td><a title="فیلتر سازی این فرستنده" href="admin.php?page=melipayamak_smessages&search=<?php echo $value['sender'] ?>"><?php echo $value['sender'] ?></a></td>
				<td><?php echo $melipayamak ->nl2br($value['message']) ?></td>
				<td><a title="فیلتر سازی این تاریخ" href="admin.php?page=melipayamak_smessages&search=<?php echo $date ?>"><?php echo $value['date'] ?></a></td>
				<td><?php echo $flash ?></td>
				<td><?php echo $to ?></td>
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
				<th scope="col" width="15%">تاریخ ارسال</th>
				<th scope="col" width="5%">فلش</th>
				<th scope="col" width="15%">شماره دریافت کننده</th>
			</tr>
		</tfoot>
	</table>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action" required>
				<option value="" selected disabled>اعمال گروهی</option>
				<option value="delete">حذف</option>
			</select>
			<?php wp_nonce_field('mpsaction', 'mpsactionf'); ?>
			<input value="اعمال کن!" class="button-secondary action" type="submit"/>
		</div>
	</div>
</form>
<div class="alignright actions mkpagination">
	صفحه
	<form style="display:inline" action="admin.php" method="get">
		<input type="hidden" name="page" value="melipayamak_smessages" />
		<input type="hidden" name="search" value="<?php echo $search ?>"/>
		<input type="number" name="hpage" min="<?php echo $pages->min ?>" max="<?php echo $pages -> num_pages ?>" value="<?php echo $pages -> current_page ?>" required/>
	</form>
	از
	<input type="text"  value="<?php echo $pages -> num_pages ?>"  disabled/>
</div>
<br/>
<div class="note">
پیام هایی که با رنگ قرمز مشخص شده اند، به صورت اتوماتیک توسط سیستم ارسال شده اند.
</div>
<?php
include dirname(__FILE__) . '/footer.php';
?>
