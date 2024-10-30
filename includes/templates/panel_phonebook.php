<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'دفترچه تلفن';
$url = plugins_url('/assets/', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<div id="new" style="display:none;">
<div style="direction:rtl;text-align:right;font-family:Yekan">
	<h2>
افزودن عضو جدید
</h2>
     <p>
         <form  action="" method="post">
         	<input type="text" name="name" value="<?php echo $fname; ?>" placeholder="نام کاربر" required/>
         	<br/><br/>
         	<input type="text" name="lname" value="<?php echo $lname; ?>" placeholder="نام خانوادگی کاربر" required/>
         	<br/><br/>
         	<input type="text" name="mobile" value="<?php echo $mobile ?>" placeholder="شماره موبایل" required/>
         	<br/><br/>
         	<select name="gender" required><option  <?php if($_POST['gender']==2) echo 'selected' ?> value="2">مرد</option><option <?php if($_POST['gender']==1) echo 'selected' ?> value="1">زن</option></select>
         	<br/><br/>
         	<?php echo $melipayamak -> fetch_phonebook_groups(true, $_POST['group']); ?>
         	<div style="clear:both"></div><br/><br/>
         	<input type="hidden" name="do" value="new" />
         	<?php wp_nonce_field('mppaaction', 'mppaactionf'); ?>
         	<br/><br/>
         	<input type="submit" value="ثبت عضو"/>
         </form>
     </p>
</div></div>
<div id="restore" style="display:none;">
<div style="direction:rtl;text-align:right;font-family:Yekan">
	<h2>
بازگردانی پشتیبان
</h2>
     <p>
         <form enctype="multipart/form-data" action="" method="post">
         	<input type="file" name="backup" required/>
         	<br/><br/>
         	<?php wp_nonce_field('mppaaction', 'mppaactionf'); ?>
         	<input type="submit" value="بازگردانی"/>
         </form>
     </p>
</div></div>
<div class="clear"></div>
<div style="margin-top:6px"></div>
<a  href="#TB_inline?width=300&height=440&inlineId=new" class="thickbox add-new">افزودن عضو جدید</a>
<a  href="index.php?melipayamak_backup=true" class="add-new">تهیه پشتیبان از دفترچه</a>
<a  href="#TB_inline?width=300&height=220&inlineId=restore" class="thickbox add-new">بازگردانی پشتیبان</a>
<div class="clear"></div>
<br/>
<div class="mksearch alignleft actions">
<form action="admin.php" method="get" >
<input type="hidden" name="page" value="melipayamak_phonebook" />
<input type="hidden" name="field" value="<?php echo $field_search ?>" />
<input type="search" name="search" value="<?php echo $search ?>" />
<input class="button button-primary button-large" type="submit" value="بگرد!" />
</form>
</div>
<div class="alignright actions pagination">
	<?php $pages->show() ?>
</div>
<?php
if ($zero !== true){
foreach($members as $key=>$value){
?>
<div id="edit<?php echo $value['id'] ?>" style="display:none;">
<div style="direction:rtl;text-align:right;font-family:Yekan">
	<h2>
ویرایش کاربر <?php echo $value['name'].' '.$value['lname'] ?>
</h2>
          <form action="" method="post">
         	<input type="text" name="name" value="<?php echo $value['name'] ?>" placeholder="نام کاربر" required/>
         	<br/><br/>
         	<input type="text" name="lname" value="<?php echo $value['lname'] ?>" placeholder="نام خانوادگی کاربر" required/>
         	<br/><br/>
         	<input type="text" name="mobile" value="<?php echo $value['mobile'] ?>" placeholder="شماره موبایل" required/>
         	<br/><br/>
         	<select name="gender"><option <?php if($value['gender']==2) echo 'selected' ?> value="2">مرد</option><option <?php if($value['gender']==1) echo 'selected' ?> value="1">زن</option></select>
         	<br/><br/>
         	<?php echo $melipayamak -> fetch_phonebook_groups(true, $value['gid']); ?>
         	<br/><br/>
         	<input type="hidden" name="do" value="edit" />
         	<input type="hidden" name="id" value="<?php echo $value['id'] ?>" />
         	<?php wp_nonce_field('mppaaction', 'mppaactionf'); ?>
         	<br/><br/>
         	<input type="submit" value="ویرایش عضو"/>
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
				<th scope="col">ردیف</th>
				<th scope="col">نام</th>
				<th scope="col">جنسیت</th>
				<th scope="col">شماره موبایل</th>
				<th scope="col">تاریخ عضویت</th>
				<th scope="col">گروه</th>
				<th scope="col">وضعیت سینک</th>
				<th scope="col">مدیریت کاربر</th>
			</tr>
		</thead>

		<tbody>
			<?php
if ($zero === true)
echo '<td colspan="9" style="text-align:center">هیچ عضوی یافت نشد.</td>';
else{
	$num=$pages->low;
foreach($members as $key=>$value){
	$num++;
	$gender=($value['gender']==1)? 'زن' : 'مرد';
	$sync=($value['sync']==1)? 'سینک شده':'سینک نشده';
			?>

			<tr>
				<th class="check-column" scope="row">
				<input type="checkbox" name="id[<?php echo $value['id'] ?>]">
				</th>
				<td><?php echo $num ?></td>
				<td><?php echo $value['name'].' '.$value['lname'] ?></td>
				<td><a title="فیلتر سازی این جنسیت" href="admin.php?page=melipayamak_phonebook&field=gender:<?php echo $value['gender'] ?>"><?php echo $gender ?></a></td>
				<td><?php echo $value['mobile'] ?></td>
				<td><?php echo $value['date'] ?></td>
				<td><a title="فیلتر سازی این گروه" href="admin.php?page=melipayamak_phonebook&field=gid:<?php echo $value['gid'] ?>"><?php echo $value['gname'] ?></a></td>
				<td><?php echo $sync ?></td>
				<td>
<a  href="#TB_inline?width=300&height=390&inlineId=edit<?php echo $value['id'] ?>" class="thickbox">ویرایش کاربر</a>
				</td>
			</tr>
			<?php  } } ?>
		</tbody>

		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column">
				<input type="checkbox" name="checkAll"/>
				</th>
				<th scope="col">ردیف</th>
				<th scope="col">نام</th>
				<th scope="col">جنسیت</th>
				<th scope="col">شماره موبایل</th>
				<th scope="col">تاریخ عضویت</th>
				<th scope="col">گروه</th>
				<th scope="col">وضعیت سینک</th>
				<th scope="col">مدیریت کاربر</th>
			</tr>
		</tfoot>
	</table>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action" required>
				<option value="" selected disabled>اعمال گروهی</option>
				<option value="delete">حذف</option>
				<option value="sync">سینک</option>
			</select>
			<?php wp_nonce_field('mppaction', 'mppactionf'); ?>
			<input value="اعمال کن!" class="button-secondary action" type="submit"/>
		</div>
	</div>
</form>
<div class="alignright actions mkpagination">
	صفحه
	<form style="display:inline" action="admin.php" method="get">
		<input type="hidden" name="page" value="melipayamak_phonebook" />
		<input type="hidden" name="field" value="<?php echo $field_search ?>" />
		<input type="hidden" name="search" value="<?php echo $search ?>"/>
		<input type="number" name="hpage" min="<?php echo $pages->min ?>" max="<?php echo $pages -> num_pages ?>" value="<?php echo $pages -> current_page ?>" required/>
	</form>
	از
	<input type="text"  value="<?php echo $pages -> num_pages ?>"  disabled/>
</div>
<?php
include dirname(__FILE__) . '/footer.php';
?>
