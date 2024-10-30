<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
?>
<div id="melipayamak_footer">
	 © <?php echo $melipayamak -> date('','Y') ?> کلیه حقوق برای تیم توسعه ملی پیامک محفوظ است.
</div>
</div>