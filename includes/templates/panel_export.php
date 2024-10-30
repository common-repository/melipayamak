<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
$title = 'برون ریزی اطلاعات';
$url = plugins_url('/assets/', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<h2 style="font-family:Mitra"> انتخاب تنظیمات </h2>
<form action="index.php?mpexport=1" method="post">
    <?php wp_nonce_field('mpeaction', 'mpeactionf'); ?>
    <select style="height:38px" name="group" required>
        <option disabled selected value="">گروه را انتخاب نمایید.</option>
        <option <?php
        if ($_POST['group'] == 'all')
            echo 'selected';
        ?> value="all">تمام گروه ها
        </option>
        <?php echo $melipayamak->fetch_phonebook_groups(true, $_POST['group'], false); ?>
        <option value="users">کاربران سایت</option>
    </select>
    <select style="height:38px" name="format" required>
        <option disabled selected value="">فرمت را انتخاب کنید.</option>
        <option <?php
        if ($_POST['format'] == 'txt')
            echo 'selected';
        ?> value="txt">txt
        </option>
        <option <?php
        if ($_POST['format'] == 'csv')
            echo 'selected';
        ?> value="csv">csv
        </option>
    </select>
    <br/>
    <br/>
    <input style="height:38px" type="submit" value="خروجی" class="button button-primary button-large"/>
</form>
<?php
include dirname(__FILE__) . '/footer.php';
?>
