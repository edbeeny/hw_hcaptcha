<?php

namespace Concrete\Package\HwHcaptcha\Captcha;

use Concrete\Core\Captcha\CaptchaInterface as CaptchaInterface;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Permission\IPService;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Support\Facade\Log;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Http\Request;

class HwHcaptchaController implements CaptchaInterface

{

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::display()
     */
    public function display()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::showInput()
     */
    public function showInput()
    {

        $assetList = AssetList::getInstance();

        $assetUrl = 'https://hcaptcha.com/1/api.js?onload=hwHcaptcha&render=explicit';

        $assetList->register('javascript', 'hw_hcaptcha_api', $assetUrl, array('local' => false));
        $assetList->register('javascript', 'hw_hcaptcha_render', 'assets/js/render.js', array(), 'hw_hcaptcha');


        $assetList->registerGroup(
            'hw_hcaptcha',
            array(
                array('javascript', 'hw_hcaptcha_render'),
                array('javascript', 'hw_hcaptcha_api'),
            )
        );

        $responseAssets = ResponseAssetGroup::get();
        $responseAssets->requireAsset('hw_hcaptcha');

        echo '<input type="hidden" name="hcaptcha_Key" id="hcaptchaKey" value="' . Config::get('hw_hcaptcha.site_key') . '">';
        echo '<input type="hidden" name="hcaptcha_theme" id="badgeTheme" value="' . Config::get('hw_hcaptcha.theme') . '">';
        echo '<input type="hidden" name="hcaptcha_size" id="badgeSize" value="' . Config::get('hw_hcaptcha.size') . '">';
        echo '<div id="h-captcha"></div>';

    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::label()
     */
    public function label()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::check()
     */
    public function check()
    {

        $app = Application::getFacadeApplication();
        $r = Request::getInstance();


        $iph = (string)$app->make(IPService::class)->getRequestIPAddress();

        $qsa = http_build_query(
            array(
                'secret' => Config::get('hw_hcaptcha.secret_key'),
                'remoteip' => $iph,
                'response' => $r->request->get('h-captcha-response')
            )
        );

        $ch = curl_init('https://hcaptcha.com/siteverify?' . $qsa);

        if (Config::get('concrete.proxy.host') != null) {
            curl_setopt($ch, CURLOPT_PROXY, Config::get('concrete.proxy.host'));
            curl_setopt($ch, CURLOPT_PROXYPORT, Config::get('concrete.proxy.port'));

            // Check if there is a username/password to access the proxy
            if (Config::get('concrete.proxy.user') != null) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, Config::get('concrete.proxy.user') . ':' . Config::get('concrete.proxy.password')
                );
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Config::get('app.curl.verifyPeer'));

        $response = curl_exec($ch);

        if ($response !== false) {
            $data = json_decode($response, true);

            if (isset($data['error-codes']) && (in_array('missing-input-secret', $data['error-codes']) || in_array('invalid-input-secret', $data['error-codes']))) {
                Log::addError(t('The hCaptcha secret parameter is invalid or malformed.'));
            }

            if ($data['success'] == true) {
                return true;
            } else {

                return false;


            }
        } else {
            Log::addError(t('Error loading hCaptcha: %s', curl_error($ch)));
            return false;
        }
    }


    public
    function saveOptions($data)
    {

        Config::save('hw_hcaptcha.site_key', $data['site']);
        Config::save('hw_hcaptcha.secret_key', $data['secret']);
        Config::save('hw_hcaptcha.theme', $data['theme']);
        Config::save('hw_hcaptcha.size', $data['size']);
    }
}


