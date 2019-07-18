<?php

namespace Concrete\Package\HwHcaptcha;

use Concrete\Core\Logging\Logger;
use Concrete\Core\Package\Package;
use Concrete\Core\Captcha\Library as CaptchaLibrary;


class Controller extends Package
{
    protected $pkgHandle = 'hw_hcaptcha';
    protected $appVersionRequired = '8.3.0';
    protected $pkgVersion = '0.9.0';

    protected $logger;

    protected $pkgAutoloaderMapCoreExtensions = true;

    public function getPackageName()
    {
        return t('HW hCaptcha ');
    }

    public function getPackageDescription()
    {
        return t('hCaptcha an alternative to reCAPTCHA.');
    }


    public function install()
    {
        $pkg = parent::install();
        CaptchaLibrary::add('hwHcaptcha', t('hCaptcha'), $pkg);
        return $pkg;
    }

    public function uninstall()
    {
        parent::uninstall();
    }
}
