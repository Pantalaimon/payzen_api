<?php
namespace PayzenApi;

use \Log;

class PageInfo {

    public $cache_id, $card_types, $state, $locale;

    const STATE_UNKNOWN = "unknown";

    const STATE_ENTRY = "entry";

    const STATE_CHOICE = "choice";

    const STATE_SUCCESS = "success";

    const STATE_FAILURE = "failure";

    const STATE_ERROR = "error";

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

    public function __construct($html, $cookies) {
        // Log::debug($html);
        $this->card_types = [];
        $this->state = self::STATE_UNKNOWN;

        // get cacheId from cookies
        foreach ($cookies as $cookie) {
            if (preg_match("#^\d+$#", $cookie['name'])) {
                $this->cache_id = $cookie['name'];
                break;
            }
        }
        // payzen may not have sent the cookie header
        /* FIXME eclipse formatter explodes the uber "?:" operator @formatter:off */
        $this->cache_id = $this->cache_id
            ?: $this->extractData('#name="cacheId" value="(\d+)#', $html)
            ?: $this->extractData('#name="cacheId" value="(\d+)#', $html);
        /* @formatter:on */

        // TODO optim multiple regex on all body ?
        $form_action = $this->extractData('#action="exec\.([A-Za-z_]+)\.a"#', $html);
        \Log::debug("PageInfo action found : " . $form_action);
        switch ($form_action) {
            case "card_input":
                $this->state = self::STATE_ENTRY;
                break;
            case "paymentChoice":
                $this->state = self::STATE_CHOICE;
                $this->card_types = $this->extractData('#label for="([^"]+)" class="choiceLabel"#', $html, false);
                break;
            case "success":
                $this->state = self::STATE_SUCCESS;
                break;
            case "refused":
            case "referral":
                $this->state = self::STATE_FAILURE;
                break;
        }

        if (! $this->cache_id) {
            $this->state = self::STATE_ERROR;
        }

        $flag = $this->extractData('#img src="\/static\/commons\/flags\/(..)\.#', $html);
        $this->locale = $flag ? $this->locale_icons[$flag] : null;
    }

    /**
     * Shortcut to get the match(es) of the first capturing parenthesis of a regex
     */
    private function extractData($regex, $html, $single = true) {
        if (preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER)) {
            return $single ? $matches[1][0] : $matches[1];
        }
        return false;
    }
}