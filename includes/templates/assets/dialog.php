<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta charset="utf-8">
    <title>ملی پیامک</title>
    <link rel="stylesheet" href="../style.css" type="text/css" media="all"/>
    <style>
        select {
            border: 1px solid #ddd;
            -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .07);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, .07);
            background-color: #fff;
            color: #32373c;
            outline: 0;
            -webkit-transition: 50ms border-color ease-in-out;
            transition: 50ms border-color ease-in-out;
            border-radius: 0px !important;
        }
    </style>
</head>

<body>
<div id="TB_window" style="direction:rtl;text-align:right;font-family:Yekan">
    <h2 style="margin-top:-8px">اضافه کننده اتوماتیک شرت کد</h2>
    <form action="" onsubmit="insert()" method="post">
        <select id="mode" name="mode" required>
            <option value="1">فرم کوچک</option>
            <option value="2">فرم بزرگ</option>
        </select>
        <input type="text" id="width" style="height: 30px;" placeholder="عرض فرم" name="width" required/>
        <br/>
        <input style="margin-top:15px" type="submit" value="اضافه کن"/>
    </form>
    <script>
        function insert() {
            var shortcode = "[melipayamak mode='" + document.getElementById('mode').value + "' width='" + document.getElementById('width').value + "']";
            var parent = parent || top;
            parent.tinymce.execCommand('mceInsertContent', false, shortcode);
            parent.tinymce.EditorManager.activeEditor.windowManager.close(window);
        }
    </script>
</div>
</body>
</html>