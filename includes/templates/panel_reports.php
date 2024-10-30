<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
$title = 'گزارش ها';
include dirname(__FILE__) . '/head.php';
wp_enqueue_script('chart-js', $url . 'charts.js', true, $melipayamak_version);
?>
<h2 style="font-family:Mitra">
    وضعیت پلاگین به صورت خلاصه به شرح زیر می باشد:
</h2>
<br/>
<div style="font-size:20px;text-align: center;font-family:Mitra">
    <div style="display:inline;">
        تعداد اعضای خبرنامه: <span class="one"><?php echo number_format($melipayamak->count); ?></span> نفر
    </div>
    <div style="display:inline;margin-right:10px">
        تعداد پیامک های ارسالی: <span class="one"><?php echo number_format($count); ?></span> پیامک
    </div>
    <div style="display:inline;margin-right:10px;">
        اعتبار شما: <span class="one"><?php echo number_format($melipayamak->credit) ?></span> پیامک
    </div>
</div>
<h2 style="font-family:Mitra">
    رسم نمودار های آماری
</h2>
<form action="" method="post">
    <?php wp_nonce_field('mpraction', 'mpractionf'); ?>
    <select style="height:38px" name="period" required>
        <option disabled selected value="">لطفا یک بازه زمانی انتخاب کنید.</option>
        <option <?php
        if ($_POST['period'] == '7')
            echo 'selected';
        ?> value="7">یک هفته
        </option>
        <option <?php
        if ($_POST['period'] == '31')
            echo 'selected';
        ?> value="31">یک ماه
        </option>
        <option <?php
        if ($_POST['period'] == '93')
            echo 'selected';
        ?> value="93">یک فصل
        </option>
        <option <?php
        if ($_POST['period'] == '365')
            echo 'selected';
        ?> value="365">یک سال
        </option>
    </select>
    <select style="height:38px" name="type" required>
        <option disabled selected value="">لطفا نوع داده هارا انتخاب کنید.</option>
        <option <?php
        if ($_POST['type'] == 'messages')
            echo 'selected';
        ?> value="messages">پیغام های ارسالی
        </option>
        <option <?php
        if ($_POST['type'] == 'members')
            echo 'selected';
        ?> value="members">کاربران عضو شده در خبرنامه
        </option>
    </select>
    <input style="height:38px" type="submit" value="رسم کن!" class="button button-primary button-large"/>
</form>
<?php if ($p) { ?>
    <br/>
    <div style="direction:ltr;text-align:left;text-align:center;margin:auto">
        <canvas id="container" width="300" height="100"></canvas>
        <script>
            jQuery(document).ready(function () {
                var ctx = document.getElementById('container').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['<?php echo implode("','", $dates) ?>'],
                        datasets: [{
                            label: 'نمودار <?php echo $chart_title ?>',
                            data: [<?php echo implode(",", $data) ?>],
                            backgroundColor: '#003171',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            });
        </script>
    </div>
<?php } ?>
<?php
include dirname(__FILE__) . '/footer.php';
?>
