<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'گروه های دفترچه تلفن';
$url = plugins_url('/assets/', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<div id="new" style="display:none;">
<div style="direction:rtl;text-align:right;font-family:Yekan">
	<h2>
افزودن گروه تازه
</h2>
     <p>
         <form action="" method="post">
         	<input maxlength="255" type="text" name="name" placeholder="نام گروه" required/>
         	<br/><br/>
         	<select name="show" required><option disabled selected>اجازه به کاربران برای انتخاب این گروه</option><option value="1">بلی</option><option value="0">خیر</option></select>
         	<br/><br/>
         	<input type="hidden" name="do" value="new" />
         	<?php wp_nonce_field('mpgaaction', 'mpgaactionf'); ?>
         	<input type="submit" value="ثبت گروه"/>
         </form>
     </p>
</div></div>
<div class="clear"></div>
<div style="margin-top:6px"></div>
<a  href="#TB_inline?width=300&height=280&inlineId=new" class="thickbox add-new">افزودن گروه جدید</a>
<div class="clear"></div>
<br/>
<div class="mksearch alignleft actions">
<form action="admin.php" method="get" >
<input type="hidden" name="page" value="melipayamak_groups" />
<input type="search" name="search" value="<?php echo $search ?>" />
<input class="button button-primary button-large" type="submit" value="بگرد!" />
</form>
</div>
<div class="alignright actions pagination">
<?php $pages->show() ?>
</div>
<?php
if ($zero !== true){
foreach($groups as $key=>$value){
?>

<div id="edit<?php echo $value['gid'] ?>" style="display:none;">
<div style="direction:rtl;text-align:right;font-family:Yekan">
	<h2>
ویرایش گروه <?php echo $value['gname'] ?>
</h2>
         <form action="" method="post">
         	<input type="text" name="name" value="<?php echo $value['gname'] ?>" required/>
         	<br/><br/>
         	<select name="show" required><option disabled selected>اجازه به کاربران برای انتخاب این گروه</option><option <?php if($value['gshow']==1) echo 'selected'; ?> value="1">بلی</option><option <?php if($value['gshow']==0) echo 'selected'; ?> value="0">خیر</option></select>
         	<br/><br/>
         	<input type="hidden" name="do" value="edit" />
         	<input type="hidden" name="id" value="<?php echo $value['gid'] ?>" />
         	<?php wp_nonce_field('mpgaaction', 'mpgaactionf'); ?>
         	<input type="submit" value="بروزرسانی"/>
         </form>
</div></div>
<?php } } ?>
<form action="" method="post">
	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-cb check-column">
				<input type="checkbox" name="checkAll"/>
				</th>
				<th scope="col" width="5%">ردیف</th>
				<th scope="col" width="25%">نام</th>
				<th scope="col" width="45%">تاریخ ایجاد</th>
				<th scope="col" width="10%">اجازه انتخاب</th>
				<th scope="col" width="15%">مدیریت گروه</th>
			</tr>
		</thead>

		<tbody>
			<?php
if ($zero === true)
echo '<td colspan="6" style="text-align:center">هیچ گروهی یافت نشد.</td>';
else{
	$num=$pages->low;
foreach($groups as $key=>$value){
	$num++;
	$show=($value['gshow']==1)? 'بلی':'خیر';
			?>

			<tr>
				<th class="check-column" scope="row">
				<input type="checkbox" name="id[<?php echo $value['gid'] ?>]">
				</th>
				<td><?php echo $num ?></td>
				<td><?php echo $value['gname'] ?></td>
				<td><?php echo $value['gdate'] ?></td>
				<td><?php echo $show ?></td>
				<td>
<a  href="#TB_inline?width=300&height=220&inlineId=edit<?php echo $value['gid'] ?>" class="thickbox">ویرایش گروه</a>
<br/>
<a  href="admin.php?page=melipayamak_phonebook&field=gid:<?php echo $value['gid'] ?>">مشاهده کاربران</a>
				</td>
			</tr>
			<?php  } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column">
				<input type="checkbox" name="checkAll"/>
				</th>
				<th scope="col" width="5%">ردیف</th>
				<th scope="col" width="25%">نام</th>
				<th scope="col" width="45%">تاریخ ایجاد</th>
				<th scope="col" width="10%">اجازه انتخاب</th>
				<th scope="col" width="15%">مدیریت گروه</th>
			</tr>
		</tfoot>
	</table>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action" required>
				<option value="" selected disabled>اعمال گروهی</option>
				<option value="delete">حذف</option>
			</select>
			<?php wp_nonce_field('mpgaction', 'mpgactionf'); ?>
			<input value="اعمال کن!" class="button-secondary action" type="submit"/>
		</div>
	</div>
</form>
<div class="alignright actions mkpagination">
	صفحه
	<form style="display:inline" action="admin.php" method="get">
		<input type="hidden" name="page" value="melipayamak_groups" />
		<input type="hidden" name="search" value="<?php echo $search ?>"/>
		<input type="number" name="hpage" min="<?php echo $pages->min ?>" max="<?php echo $pages -> num_pages ?>" value="<?php echo $pages -> current_page ?>" required/>
	</form>
	از
	<input type="text"  value="<?php echo $pages -> num_pages ?>"  disabled/>
</div>
<br/>
<div class="note">
توجه فرمایید که با حذف یک گروه کاربری، کاربران آن گروه نیز حذف خواهند شد.
<!-- اگر سینک دفترچه تلفن را فعال باشید ممکن است به نسبت تعداد مخاطبین هر گروه و سرور شما عملیات حذف از چند ثانیه تا چند دقیقه طول بکشد، پس شکیبا باشید -->
</div>
<?php
include dirname(__FILE__) . '/footer.php';
?>
