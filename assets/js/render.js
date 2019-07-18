function hwHcaptcha() {
    var siteKey = document.getElementById("hcaptchaKey").value;
    var badgeTheme = document.getElementById("badgeTheme").value;
    var badgeSize = document.getElementById("badgeSize").value;
    var clientId = hcaptcha.render('h-captcha', {
        'sitekey': siteKey,
        'theme': badgeTheme,
        'size': badgeSize,
        'error-callback':'onError'
    });

}


