jQuery(document).ready(function ($) {
    function content_check(id, focus) {
        if ($(id).val() != '' && $(id).val() != null) {
            $(id).removeClass('melipayamak_invalid');
            return true;
        } else {
            $(id).addClass('melipayamak_invalid');
            if (focus)
                $(id).focus();
            return false;
        }
    }

    function replaceNumbers(string) {
        string = string.toString();

        var arabicNumbers = ["١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩", "٠"],
            persianNumbers = ["۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"],
            englishNumbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];

        for (var i = 0, numbersLen = arabicNumbers.length; i < numbersLen; i++) {
            string = string.replace(new RegExp(arabicNumbers[i], "g"), englishNumbers[i]);
        }

        for (var i = 0, numbersLen = persianNumbers.length; i < numbersLen; i++) {
            string = string.replace(new RegExp(persianNumbers[i], "g"), englishNumbers[i]);
        }
        return string;
    }


    function tell_check(id, focus) {
        var re = /^09([0-9]{9})$/;
        if (re.test(replaceNumbers($(id).val()))) {
            $(id).removeClass('melipayamak_invalid');
            return true;
        } else {
            $(id).addClass('melipayamak_invalid');
            if (focus)
                $(id).focus();
            return false;
        }
    }

    $('.melipayamak_fname,.melipayamak_lname,.melipayamak_code').keyup(function () {
        content_check($(this), false);
    });
    $('.melipayamak_fname,.melipayamak_lname,.melipayamak_gender,.melipayamak_group,.melipayamak_code').focusout(function () {
        content_check($(this), false);
    });
    $('.melipayamak_gender,.melipayamak_group').change(function () {
        content_check($(this), false);
    });
    $('.melipayamak_mobile').keyup(function () {
        tell_check($(this), false);
    });
    $('.melipayamak_mobile').focusout(function () {
        tell_check($(this), false);
    });
    var mpcode = 0;
    $('#melipayamak').submit(function () {
        if (!$(this).hasClass('working')) {
            if (mpcode == 0) {
                content_check($('.melipayamak_fname'), false);
                content_check($('.melipayamak_lname'), false);
                tell_check($('.melipayamak_mobile'), false);
                content_check($('.melipayamak_gender'), false);
                content_check($('.melipayamak_group'), false);
                if (content_check($('.melipayamak_fname'), true)) {
                    if (content_check($('.melipayamak_lname'), true)) {
                        if (tell_check($('.melipayamak_mobile'), true)) {
                            if (content_check($('.melipayamak_gender'), true)) {
                                if (content_check($('.melipayamak_group'), true)) {
                                    $(this).addClass('working');
                                    $('#submit_melipayamak').val('لطفا صبر کنید...');
                                    $.ajax({
                                        type: "POST",
                                        url: "",
                                        data: {
                                            name: $('.melipayamak_fname').val(),
                                            lname: $('.melipayamak_lname').val(),
                                            mobile: replaceNumbers($('.melipayamak_mobile').val()),
                                            gender: $('.melipayamak_gender').val(),
                                            group: $('.melipayamak_group').val(),
                                            mpadn: 'true'
                                        }
                                    }).done(function (data) {
                                        var ok = false;
                                        if (data == 'remove/code') {
                                            mpcode = 1;
                                            $('#mpcode').empty().append('کد تایید که به موبایل شما ارسال شده است را جهت تایید لغو عضویت وارد کنید.');
                                            $('.mpnew').slideUp();
                                            $('.mpcode').slideDown();
                                            $('#melipayamak').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == 'add/code') {
                                            mpcode = 1;
                                            $('#mpcode').empty().append('کد تایید که به موبایل شما ارسال شده است را جهت تایید عضویت وارد کنید.');
                                            $('.mpnew').slideUp();
                                            $('.mpcode').slideDown();
                                            $('#melipayamak').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == 'added') {
                                            mpreset();
                                            $('#submit_melipayamak').val('عضویت شما انجام شد. با تشکر');
                                            $('#melipayamak').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == 'deleted') {
                                            mpreset();
                                            $('#submit_melipayamak').val('لغو عضویت شما انجام شد. با تشکر');
                                            $('#melipayamak').removeClass('working');
                                            ok = true;
                                        }
                                        if (data == '' || !ok) {
                                            $('#submit_melipayamak').val('اشتراک یا لغو اشتراک');
                                            alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                                            $('#melipayamak').removeClass('working');
                                        }
                                    }).fail(function (data) {
                                        $('#submit_melipayamak').val('اشتراک یا لغو اشتراک');
                                        alert('مشکلی پیش آمد. مجددا تلاش کنید.');
                                        alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                                    });
                                }
                            }
                        }
                    }
                }
            } else {
                if (mpcode != 2) {
                    if (content_check($('.melipayamak_code'), true)) {
                        $(this).addClass('working');
                        $('#submitcmp').val('لطفا صبر کنید...');
                        $.ajax({
                            type: "POST",
                            url: "",
                            data: {
                                name: $('.melipayamak_fname').val(),
                                lname: $('.melipayamak_lname').val(),
                                mobile: replaceNumbers($('.melipayamak_mobile').val()),
                                gender: $('.melipayamak_gender').val(),
                                group: $('.melipayamak_group').val(),
                                code: $('.melipayamak_code').val(),
                                mpadn: 'true'
                            }
                        }).done(function (data) {
                            var ok = false;
                            if (data == 'added') {
                                $('.melipayamak_code').val('');
                                $('#submitcmp').val('عضویت شما انجام شد. با تشکر »بازگشت');
                                mpcode = 2;
                                $('#melipayamak').removeClass('working');
                                ok = true;
                            }
                            if (data == 'deleted') {
                                $('.melipayamak_code').val('');
                                $('#submitcmp').val('لغو عضویت شما انجام شد. »بازگشت');
                                mpcode = 2;
                                $('#melipayamak').removeClass('working');
                                ok = true;
                            }
                            if (data == 'incorrect') {
                                $('.melipayamak_code').val('');
                                content_check($('.melipayamak_code'), true);
                                $('#submitcmp').val('کد وارد شده معتبر نیست.');
                                $('#melipayamak').removeClass('working');
                                ok = true;
                            }
                            if (data == '' || !ok) {
                                $('#submitcmp').val('ارسال کد تایید');
                                $('#melipayamak').removeClass('working');
                                alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                            }
                        }).fail(function (data) {
                            $('#submitcmp').val('ارسال کد تایید');
                            $('#melipayamak').removeClass('working');
                            alert('مشکلی پیش آمد. مجددا تلاش کنید.' + ' (' + data + ')');
                        });
                    }
                }
            }
        }
        return false;
    });
    $('#submitcmp').click(function () {
        if (mpcode == 2) {
            mpreset();
            $('.mpnew').slideDown();
            $('.mpcode').slideUp();
        }
    })

    function mpreset() {
        mpcode = 0;
        $('.melipayamak_fname').val('');
        $('.melipayamak_lname').val('');
        $('.melipayamak_mobile').val('');
        $('.melipayamak_gender').val('');
        $('.melipayamak_group').val('');
        $('#submit_melipayamak').val('اشتراک یا لغو اشتراک');
        $('#submitcmp').val('ارسال کد تایید');
    }

});
