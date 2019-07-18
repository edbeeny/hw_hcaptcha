<?php
defined('C5_EXECUTE') or die('Access denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Config;

$app = Application::getFacadeApplication();
$form = $app->make('helper/form');

?>

<p><?=  t('A site key and secret key must be provided. They can be obtained from the <a href="%s" target="_blank">hCaptcha website</a>.', 'https://hcaptcha.com') ?></p>

<div class="form-group">
    <?php  echo  $form->label('site', t('Site Key')) ?>
    <?php  echo  $form->text('site', Config::get('hw_hcaptcha.site_key', '')) ?>
</div>

<div class="form-group">
    <?php  echo  $form->label('secret', t('Secret Key')) ?>
    <?php  echo  $form->text('secret', Config::get('hw_hcaptcha.secret_key', '')) ?>
</div>

<div class="form-group">
    <?php  echo  $form->label('theme', t('Theme')) ?>
    <?php  echo  $form->select(
        'theme',
        array(
            'light' => t('Light'),
            'dark' => t('Dark'),
        ),
        Config::get('hw_hcaptcha.theme', 'light')) ?>
</div>

<div class="form-group">
    <?php  echo  $form->label('size', t('Size')) ?>
    <?php  echo  $form->select(
        'size',
        array(
            'normal' => t('Normal'),
            'compact' => t('Compact'),
        ),
        Config::get('hw_hcaptcha.size', 'normal')) ?>
</div>