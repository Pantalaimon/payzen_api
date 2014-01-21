<?php
namespace PayzenApi\ws;

use \Log;

/**
 * Adapted from contribution codes (soooooo much time saved !)<br/>
 * Class managing parameters checking, form and signature building, response analysis and more for calling WS
 *
 * @package WSApi
 */
class Client {

    /**
     * The key for signature calculation
     *
     * @var string key
     * @access private
     */
    private $key;

    /**
     * The SoapClient instance to call vads WS
     *
     * @var SoapClient client
     * @access private
     */
    private $client;

    // /**
    // * Constants that represents ws method names.
    // */
    // const METHOD_REFUND = 'refund';
    // const METHOD_GET_INFO = 'getInfo';
    // const METHOD_DUPLICATE = 'duplicate';
    // const METHOD_CREATE = 'create';
    // const METHOD_FORCE = 'force';
    // const METHOD_CANCEL = 'cancel';
    // const METHOD_VALIDATE = 'validate';
    // const METHOD_MODIFY_AND_VALIDATE = 'modifyAndValidate';
    // const METHOD_MODIFY = 'modify';
    /**
     * Initialize the ws api with key and wsdl url.
     *
     * @param string $key
     * @param string $wsdl
     */
    public function __construct($key, $wsdl) {
        // Set the certificate to use for signature
        $this->key = $key;
        $options = array(
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_BOTH,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS // <list><element/></list> renders as [element] and not element
                );

        try {
            $this->client = new SoapClient($wsdl, $options);
        } catch (SoapFault $fault) {
            Log::error($fault->getCode() . " - " . $fault->getMessage());
            die();
        }
    }
}