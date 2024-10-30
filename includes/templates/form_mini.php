<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$url = plugins_url('/form/', __FILE__);
$inc_url = includes_url();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <script src="<?php echo $inc_url ?>js/jquery/jquery.js"></script>
		<script src="<?php echo $url ?>main.js"></script>
		<style>
			body {
				direction: rtl;
				text-align: right;
			}
			@font-face {
				font-family: 'Yekan';
				src: url('<?php echo $url ?>fonts/YekanWeb-Regular.woff') format('woff');
				font-weight: normal;
				font-style: normal;
			}
			input, select {
				outline: none;
			}
			.mpsidebar {
				width: 100%;
				padding: 10px 0px;
			}
			.textbox_melipayamak {
				transition: 0.2s;
				padding-right: 50px;
				box-sizing: border-box;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				line-height: 32px;
				width: 90%;
				margin: 5px auto 0px auto;
				border: <?php echo get_option('melipayamak_border') ?>px solid <?php echo get_option('melipayamak_iborder') ?>;
				border-radius: <?php echo get_option('melipayamak_radius') ?>px;
				color: <?php echo get_option('melipayamak_fontc') ?>;
				font-family: <?php echo get_option('melipayamak_fontm') ?>;
				font-size: <?php echo get_option('melipayamak_fonts') ?>px;
				height:44px;
			}

			.textbox_melipayamak:focus {
				color: <?php echo get_option('melipayamak_cfontc') ?>;
				border: <?php echo get_option('melipayamak_border') ?>px solid <?php echo get_option('melipayamak_ihborder') ?>;
			}
			.submit_melipayamak {
				cursor: pointer;
				transition: 0.2s;
				box-sizing: border-box;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				line-height: 32px;
				width: 90%;
				margin: 5px auto 0px auto;
				background: <?php echo get_option('melipayamak_sbg') ?>;
				border: <?php echo get_option('melipayamak_border') ?>px solid <?php echo get_option('melipayamak_sborder') ?>;
				border-radius: <?php echo get_option('melipayamak_radius') ?>px;
				color: #fff;
				font-family: <?php echo get_option('melipayamak_fontm') ?>;
				font-size: <?php echo get_option('melipayamak_fonts') ?>px;
				height:44px;
			}
		    .submit_melipayamak:hover {
				background: <?php echo get_option('melipayamak_shbg') ?>;
				border: <?php echo get_option('melipayamak_border') ?>px solid <?php echo get_option('melipayamak_shborder') ?>;
			}
			<?php $color=(get_option('melipayamak_ibg') != 'none')? get_option('melipayamak_ibg') : ''; ?>
			.melipayamak_name{
				background: url(<?php echo $url ?>images/melipayamak_user.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.melipayamak_gender {
				background: url(<?php echo $url ?>images/melipayamak_gender.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.melipayamak_mobile {
				background: url(<?php echo $url ?>images/melipayamak_phone.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.melipayamak_group {
				background: url(<?php echo $url ?>images/melipayamak_group.png) right 8px center no-repeat <?php echo $color ?>;
			}
			<?php $color=(get_option('melipayamak_ihbg') != 'none')? get_option('melipayamak_ihbg') : ''; ?>
			.melipayamak_name:focus {
				background: url(<?php echo $url ?>images/melipayamak_user_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.melipayamak_gender:focus {
				background: url(<?php echo $url ?>images/melipayamak_gender_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.melipayamak_mobile:focus {
				background: url(<?php echo $url ?>images/melipayamak_phone_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.melipayamak_group:focus {
				background: url(<?php echo $url ?>images/melipayamak_group_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.melipayamak_invalid{
				border: <?php echo get_option('melipayamak_border') ?>px solid #c0392b !important;
				color:#c0392b !important;
			}
			select.melipayamak_invalid{
				color: <?php echo get_option('melipayamak_fontc') ?> !important;
			}
			#mpcode{
				font-family: <?php echo get_option('melipayamak_fontm') ?>;
				font-size: 13px;
				color: <?php echo get_option('melipayamak_fontc') ?> !important;
				text-align:center;
				margin-left: 23px;
			}
			.melipayamak_code{
				text-align:center;
				padding-right:0px;
			}
			select{
			     -webkit-appearance: none;
			      -moz-appearance: none;
			      appearance: none;
			}
		</style>
	</head>
	<body>
		<form method="post" id="melipayamak" action="">
      <section class="mpsidebar">
        <div class="mpnew">
        	<input maxlength="255" type="text" class="textbox_melipayamak melipayamak_fname melipayamak_name" placeholder="نام">
			<input maxlength="255" type="text" class="textbox_melipayamak melipayamak_name melipayamak_lname" placeholder="نام خانوادگی">
			<input type="text" class="textbox_melipayamak melipayamak_mobile" placeholder="موبایل">
			<select class="textbox_melipayamak melipayamak_gender">
				<option disabled selected value="">جنسیت</option>
				<option value="1">زن</option>
				<option value="2">مرد</option>
			</select>
			<select class="textbox_melipayamak melipayamak_group">
				<option disabled selected value="">گروه کاربری</option>
				<?php echo $melipayamak -> fetch_phonebook_groups(true,'',false,true) ?>
			</select>
			<input type="submit" id="submit_melipayamak" class="submit_melipayamak" value="اشتراک یا لغو اشتراک">
        </div>
			<div style="display:none" class="mpcode">
				<div id="mpcode"></div>
					<input type="text" class="textbox_melipayamak melipayamak_code" placeholder="کد تایید">
				<input type="submit" id="submitcmp" class="submit_melipayamak" value="ارسال کد تایید">
			</div>
		</section>
		</form>
	</body>
</html>
