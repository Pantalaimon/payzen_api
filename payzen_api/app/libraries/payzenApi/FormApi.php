<?php
namespace PayzenApi;

use \Log;
use \Config;
use \Currency;
use \Curl;
use \URL;
use \Context;

class FormApi {

    const DATE_FORMAT = 'YmdHis';

    public $shop_id, $shop_key, $trans_date, $trans_time, $trans_id;

    /**
     * Initialize some necessary data
     *
     * @param string $shop_id
     * @param string $shop_key
     */
    function __construct($shop_id, $shop_key) {
        $this->shop_id = $shop_id;
        $this->shop_key = $shop_key;

        $this->trans_timestamp = time();
        $this->trans_date = gmdate(self::DATE_FORMAT, $this->trans_timestamp);
        $this->trans_id = $this->_generateTransId($this->trans_timestamp); // TODO if Cache driver manages it, Cache::increment()
    }

    /**
     * From contributions code<br/>
     * Generate a trans_id.
     * To be independent from shared/persistent counters, we use the number of 1/10seconds since midnight,
     * which has the appropriate format (000000-899999) and has great chances to be unique.
     *
     * @return string the generated trans_id
     * @access private
     */
    private function _generateTransId($timestamp) {
        list ($usec, $sec) = explode(" ", microtime()); // microseconds, php4 compatible
        $temp = ($timestamp + $usec - strtotime('today 00:00')) * 10;
        $temp = sprintf('%06d', $temp);

        return $temp;
    }

    /**
     * Return the url to redirect the client for payment
     *
     * @param \Context $context
     * @return string
     */
    public static function getRedirectUrl(\Context $context) {
        $url = Config::get("payzenapi.form_url");
        $url .= "exec.updateLocale.a";
        $url .= "?cacheId=" . $context->cache_id;
        $url .= "&newLocale=" . $context->locale;
        return $url;
    }

    /**
     * Calls payment form to create a payment context on payzen platform, and returns relevant data
     *
     * @param array $params
     * @param Currency $currency
     */
    public function createPaymentContext($params, Currency $currency) {
        $data = $this->buildFormData($params, $currency);

        $curl = new Curl();
        $curl->ssl(Config::get("payzenapi.ssl_verifypeer"));
        $html = $curl->simple_post(Config::get("payzenapi.form_url"), $data);
        if (! $html) {
            Log::error("createPaymentContext call failed : \n" . $curl->debug(true));
        }

        return new PageInfo($html, $curl->response_cookies);
    }

    private function buildFormData($params, Currency $currency) {
        $raw_amount = $params["amount"];
        $amount = intval(floatval($raw_amount) * $currency->multiplicator);

        $data = [
            "vads_version" => "V2",
            "vads_ctx_mode" => "TEST",
            "vads_page_action" => "PAYMENT",
            "vads_action_mode" => "INTERACTIVE",
            "vads_return_mode" => "GET",
            "vads_site_id" => $this->shop_id,
            "vads_trans_id" => $this->trans_id,
            "vads_trans_date" => $this->trans_date,
            "vads_payment_config" => "SINGLE",
            "vads_amount" => $amount,
            "vads_currency" => $currency->numeric,
            "vads_url_return" => URL::action("RedirectController@getReturn")
        ];

        $availables = array_get($params, "available_methods", null);
        if ($availables) {
            $cards = array_build($availables, function ($key, $value) {
                return $value["method"];
            });
            $data["vads_payment_cards"] = join(";", $cards);
        }

        ksort($data);
        $raw_sign = join("+", $data) . "+" . $this->shop_key;
        $data["signature"] = sha1($raw_sign);

        // Log::debug("Prepared data for payment page : " . print_r($data, true));

        return $data;
    }

    /**
     * Refresh an ongoing payment context (by calling updateLocale)
     *
     * @param Context $context
     */
    public static function reloadForm(Context $context) {
        $url = self::getRedirectUrl($context);

        $curl = new Curl();
        $curl->ssl(Config::get("payzenapi.ssl_verifypeer"));
        $html = $curl->simple_get($url);
        // Log::debug("Called redirect url :".$url."\nwith response : ".$html);
        return new PageInfo($html, $curl->response_cookies);
    }
}