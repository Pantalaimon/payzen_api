<?php
namespace PayzenApi;

use \Log;

class PageInfo {

    public $cache_id;

    public $card_types;

    public $state;

    public $locale;

    const STATE_UNKNOWN = "unknown";

    const STATE_ENTRY = "entry";

    const STATE_CHOICE = "choice";

    const STATE_SUCCESS = "success";

    const STATE_FAILURE = "failure";

    const STATE_ERROR = "error";
}

/**
 *
 * @author alaind
 *
 */
class LegacyApi {

    public $shop_id, $shop_key, $trans_date, $trans_timestamp, $trans_id;

    private $locale_icons = [
        "fr" => "fr_FR",
        "de" => "de_DE",
        "en" => "en_GB",
        "es" => "es_ES",
        "it" => "it_IT",
        "nl" => "nl_NL",
        "pt" => "pt_PT",
        "se" => "sv_SE",
        "ru" => "ru_RU"
    ];

    /**
     * Initialize some necessary data
     *
     * @param string $shop_id
     * @param string $shop_key
     */
    function init($shop_id, $shop_key) {
        $this->shop_id = $shop_id;
        $this->shop_key = $shop_key;

        $this->trans_timestamp = time();
        $this->trans_date = gmdate('YmdHis', $this->trans_timestamp);
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

    function getRedirectUrl(Context $context) {
        $url = \Config::get("payzenapi.form_url");
        $url .= "exec.updateLocale.a";
        $url .= "?cacheId=" . $context->cache_id;
        $url .= "&newLocale=" . $context->locale;
    }

    /**
     * Prepare to call payment page
     *
     * @param array $params
     * @throws Exception
     * @return array
     */
    public function generateFormData($params,\Currency $currency) {
        $raw_amount = array_get($params, "amount", 0);
        $amount = intval(floatval($raw_amount) * $currency->multiplicator);
        if ($amount <= 0) {
            throw new \Exception("Invalid amount : " . $amount); // TODO better exception
        }

        $data = [
            "vads_version" => "V2",
            "vads_site_id" => $this->shop_id,
            "vads_trans_id" => $this->trans_id,
            "vads_trans_date" => $this->trans_date,
            "vads_payment_config" => "SINGLE",
            "vads_amount" => $amount,
            "vads_currency" => $currency->numeric,
            "vads_ctx_mode" => "TEST",
            "vads_page_action" => "PAYMENT",
            "vads_action_mode" => "INTERACTIVE",
            "vads_url_return" => $params["url_return"],
            "vads_return_mode" => "GET"
        ];

        $availables = array_get($params, "available_methods", []);
        if ($availables) {
            $cards = [];
            foreach ($availables as $available) {
                $cards[] = $available["method"];
            }
            $data["vads_payment_cards"] = join(";", $cards);
        }

        ksort($data);
        $raw_sign = join("+", $data) . "+" . $this->shop_key;
        $data["signature"] = sha1($raw_sign);

        Log::debug("Prepared data for payment page : " . print_r($data, true));

        return $data;
    }

    /**
     * Returns the payment page if successful, false otherwise
     *
     * @param array $data
     */
    function postForm($data, &$html = null, &$headers = null, &$cookies = null) {
        $curl = new \Curl();
        $curl->ssl(\Config::get("payzenapi.ssl_verifypeer"));
        $html = $curl->simple_post(\Config::get("payzenapi.form_url"), $data);
        if (! $html) {
            Log::error("postForm failed : \n" . $curl->debug(true));
        }
        $headers = $curl->response_headers;
        $cookies = $curl->response_cookies;
    }

    /**
     *
     * @param string $html
     * @return PayzenApi\PageInfo
     */
    function parsePage($html, $cookies) {
        $info = new PageInfo();
        $info->card_types = [];
        $info->state = PageInfo::STATE_UNKNOWN;

        // get cacheId from cookies
        foreach ($cookies as $cookie) {
            if (preg_match("#^\d+$#", $cookie['name'])) {
                $info->cache_id = $cookie['name'];
                break;
            }
        }

        // TODO optim multiple regex on all body
        $form_action = $this->extractData('#action="exec\.([A-Za-z_]+)\.a"#', $html);
        switch ($form_action) {
            case "card_input":
                $info->state = PageInfo::STATE_ENTRY;
                break;
            case "paymentChoice":
                $info->state = PageInfo::STATE_CHOICE;
                break;
            case "success":
                $info->state = PageInfo::STATE_SUCCESS;
                break;
            case "refused":
            case "referral":
                $info->state = PageInfo::STATE_FAILURE;
                break;

            default:
                break;
        }
        if ($info->state == PageInfo::STATE_CHOICE) {
            preg_match_all('#label for="([^"]+)" class="choiceLabel"#', $html, $matches, PREG_PATTERN_ORDER);
            $info->card_types = $matches[1];
        }

        if (! $info->cache_id) {
            $info->state = PageInfo::STATE_ERROR;
        }

        $flag = $this->extractData('#img src="\/static\/commons\/flags\/(..)\.#', $html);
        $info->locale = $this->locale_icons[$flag];//TODO db ?

        return $info;
    }

    private function extractData($regex, $html) {
        if (preg_match($regex, $html, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Retrieve status of an interactive payment
     *
     * @param Context $context
     */
    public function reloadForm(Context $context) {
        $curl = new \Curl();
        return $curl->simple_get($this->getRedirectUrl($context));
    }

    // TODO getInfo
}