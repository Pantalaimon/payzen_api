<?php

/**
 * Class representing a WS field to be sent to the Web Service
 * @package VadsWS
 */
class VadsWSField {
	var $name;
	var $value;
	var $description;
	var $required;

	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param string $description
	 * @param boolean $required
	 *
	 * @return VadsField
	 */
	function VadsWSField($name, $description, $required) {
		$this->name = $name;
		$this->description = $description;
		$this->required = $required;
	}

	/**
	 * Setter for value
	 *
	 * @param mixed $value
	 */
	function setValue ($value) {
		$this->value = $value;
	}

	/**
	 * Return the current value of the field.
	 *
	 * @return string
	 */
	function getValue () {
		return $this->value;
	}

	/**
	 * Return the current value of the field formatted for use in signature calculation.
	 *
	 * @return string
	 */
	function getValue4Sign () {
		return $this->value;
	}

	/**
	 * Return true if the field has to be used in signature calculation.
	 */
	function inSignature() {
		return true;
	}
}

/**
 * Class representing a WS field of type Date to be sent to the Web Service
 * @package VadsWS
 */
class VadsWSDateField extends VadsWSField {

	/**
	 * Setter for value
	 *
	 * @param mixed $value
	 */
	function setValue($value) {
    	if(is_numeric($value)) {
    		$this->value = $value;
  		} elseif (is_string($value)) {
    		$this->value = strtotime($value);
    	} else {
    		$this->value = null;
    		return false;
    	}

    	return true ;
   	}

	/**
	 * Return the current value of the field formatted according to W3C standard format.
	 *
	 * @return string
	 */
	function getValue () {
		if($this->value == null) {
			return null;
		}

		return gmdate(DateTime::W3C, $this->value);
	}

	function inSignature() {
		return false; // DateTime fields are ignored in V4
	}

	/**
	 * Return the current value of the field formatted according to yyyyMMdd format for use in signature calculation.
	 *
	 * @return string
	 */
	function getValue4Sign () {
		if($this->value == null) {
			return null;
		}

		return gmdate('Ymd', $this->value);
	}
}

/**
 * Class representing a WS field of type Int or Long to be sent to the Web Service
 * @package VadsWS
 */
class VadsWSNumberField extends VadsWSField {

	/**
	 * Return the current value of the field .
	 *
	 * @return string
	 */
	function setValue ($value) {
		if(!is_numeric($value)) {
			$this->value = '0';
			return false ;
		} else {
			if(is_string($value)){
				$this->value = preg_replace('#^0+#', '', $value);
				if($this->value === '') {
					$this->value = '0';
				}
			} else {
				$this->value = number_format($value, 0, '.', '');
			}

		  	return true;
		}
	}
}

/**
 * Class representing a WS field of type boolean be sent to the Web Service
 * @package VadsWS
 */
class VadsWSBooleanField extends VadsWSField {

	/**
	 * Set the current value of the field .
	 *
	 * @return string
	 */
	function setValue ($value) {
		if($value === false || $value === FALSE || $value === 0) {
			$this->value = "0";
		} else if($value === true || $value === TRUE || $value === 1) {
			$this->value = "1";
		} else {
			$this->value = "";
		}
	}
}

/**
 * Class representing a WS field of type 3 DS result to be sent to the Web Service
 * @package VadsWS
 */
class VadsWSThreeDsField extends VadsWSField {
	var $threeDsResult = array ('brand', 'enrolled', 'authStatus', 'eci', 'xid', 'cavv', 'cavvAlgorithm');

	/**
	 * Set the current value of the field .
	 *
	 * @return string
	 */
	function setValue($value) {
		if (is_a($value, 'object') || is_array($value)) {
			$this->value = $value;
			return true;
		} else {
			$this->value = null;
			return false ;
		}
	}

	/**
	 * Return the current value of the field formatted for use in signature calculation.
	 *
	 * @return string
	 */
	function getValue4Sign() {
		if($this->value == null) {
			return null;
		}

		$values = is_array($this->value) ? $this->value : get_object_vars($this->value);

		$toReturn = '';
		foreach($this->threeDsResult as $key) {
			$toReturn .= $values[$key] . '+';
		}

		$toReturn = substr($toReturn, 0, -1);
		return $toReturn;
	}
}

/**
 * Class managing parameters checking, form and signature building, response analysis and more for calling WS
 * @package WSApi
 */
class VadsWSApi {
	/**
	 * All the fields to call vads WS and process response
	 * @var array[string, VadsWSField]
	 * @access private
	 */
	var $wsParams;

	/**
	 * The key for signature calculation
	 * @var string key
	 * @access private
	 */
	var $key;

	/**
	 * The SoapClient instance to call vads WS
	 * @var SoapClient client
	 * @access private
	 */
	var $client;

	/**
	 * Constants that represents ws method names.
	 */
	const METHOD_REFUND = 'refund';
	const METHOD_GET_INFO = 'getInfo';
	const METHOD_DUPLICATE = 'duplicate';
	const METHOD_CREATE = 'create';
	const METHOD_FORCE = 'force';
	const METHOD_CANCEL = 'cancel';
	const METHOD_VALIDATE = 'validate';
	const METHOD_MODIFY_AND_VALIDATE = 'modifyAndValidate';
	const METHOD_MODIFY = 'modify';


	/**
	 * Initialize the ws api with key and wsdl url.
	 * @param string $key
	 * @param string $wsdl
	 */
	function initialize($key, $wsdl = '###WSDL_URL###') {
		$options = array(
					'trace' => 1,
		       	 	'exceptions' => true,
		        	'cache_wsdl' => WSDL_CACHE_BOTH,
			        'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
		);

		try {
			$this->client = new SoapClient($wsdl, $options);
		} catch(SoapFault $fault) {
			print_r($fault->getCode() . " - " . $fault->getMessage());
			die();
		};

		// Set the certificate to use for signature
		$this->key = $key;

		// Create simple data field objects.
		$this->_addField ('siteId', 'Identifiant de la boutique', true);
		$this->_addField ('transactionId', 'Identifiant de transaction', true);
		$this->_addField ('orderId','Référence de la commande', true);
		$this->_addField ('cardNumber', 'Numéro de carte', true);
		$this->_addField ('orderInfo', 'Description libre de la commande');
		$this->_addField ('orderInfo2',	'Description libre de la commande');
		$this->_addField ('orderInfo3','Description libre de la commande');
		$this->_addField ('customerId', 'Code client');
		$this->_addField ('customerTitle', 'Civilité');
		$this->_addField ('customerName', 'Nom client');
		$this->_addField ('customerPhone', 'Téléphone client');
		$this->_addField ('customerMail', 'Mail client');
		$this->_addField ('customerAddress', 'Adresse client');
		$this->_addField ('customerZipCode', 'Code postal client');
		$this->_addField ('customerCity', 'Ville client');
		$this->_addField ('customerCountry', 'Pays client');
		$this->_addField ('customerIP', 'Adresse IP');
		$this->_addField ('eci', 'ECI');
		$this->_addField ('xid', 'XID');
		$this->_addField ('cavv', 'CAVV');
		$this->_addField ('comment', 'Commentaire libre');
		$this->_addField ('cvv', 'Cryptogramme visuel');
		$this->_addField ('subReference', 'Ne pas renseigner');
		$this->_addField ('contractNumber', 'Numéro de contrat commerçant : ne pas renseigner à vide');
		$this->_addField ('customerLanguage', 'Langue client : code ISO 639-1, sur 2 caractères');
		$this->_addField ('brand', 'Brand de la carte : "VISA" ou "MASTERCARD"', true);
		$this->_addField ('ctxMode', 'Contexte de sollicitation : "TEST" ou "PRODUCTION")', true);
		$this->_addField ('cardNetwork', 'Réseau de carte : "AMEX", "CB", "MASTERCARD", "VISA", "MAESTRO", "E-CARTEBLEUE', true);
		$this->_addField ('enrolled', 'Statut enrôlement porteur : "Y" : Enrôlé, "N" : Non enrôlé, "U" : Inconnu');
		$this->_addField ('paymentMethod', 'Source du paiement : "EC" : E-Commerce, "BO" : Backoffice, "MOTO" : mail ou téléphone, "CC" : centre d’appel, "OTHER" : autres', true);
		$this->_addField ('authStatus', 'Statut authentification : "Y" : Authentifié 3DS, "N" : Erreur Authentification, "U" : Authentification impossible, "A" : Essai d’authentification');
		$this->_addField ('cavvAlgorithm', 'Algorithme CAVV : "0" : HMAC, "1" : CVV, "2" : CVV_ATN, "3" :  Mastercard SPA');
		$this->_addField ('autorisationNb');
		$this->_addField ('newTransactionId');
		$this->_addField ('cardType');
		$this->_addField ('extendedErrorCode');
		$this->_addField ('transactionCondition');
		$this->_addField ('vadsEnrolled');
		$this->_addField ('vadsStatus');
		$this->_addField ('vadsECI');
		$this->_addField ('vadsXID');
		$this->_addField ('vadsCAVVAlgorithm');
		$this->_addField ('vadsCAVV');
		$this->_addField ('vadsSignatureValid');
		$this->_addField ('directoryServer');
		$this->_addField ('authMode');
		$this->_addField ('markNb');
		$this->_addField ('markCVV2_CVC2');
		$this->_addField ('authNb');
		$this->_addField ('authCVV2_CVC2');
		$this->_addField ('warrantlyResult');

		// Create number data field objects.
		$this->_addNumberField('amount', 'Montant de la transaction : en plus petite unité monétaire', true);
		$this->_addNumberField('devise', 'Devise : code monnaie ISO 4217. Ex. Euro = 978', true);
		$this->_addNumberField('sequenceNumber', 'Numéro de séquence de la transaction', true);
		$this->_addNumberField('subPaymentType', 'Ne pas renseigner');
		$this->_addNumberField('subPaymentNumber', 'Ne pas renseigner');
		$this->_addNumberField('validationMode', 'Mode de validation : 0 = Automatique, 1 = Manuelle');
		$this->_addNumberField('errorCode');
		$this->_addNumberField('transactionStatus');
		$this->_addNumberField('initialAmount');
		$this->_addNumberField('cvAmount');
		$this->_addNumberField('cvDevise');
		$this->_addNumberField('type');
		$this->_addNumberField('multiplePaiement');
		$this->_addNumberField('cardCountry');
		$this->_addNumberField('markAmount');
		$this->_addNumberField('markDevise');
		$this->_addNumberField('markResult');
		$this->_addNumberField('authAmount');
		$this->_addNumberField('authDevise');
		$this->_addNumberField('authResult');
		$this->_addNumberField('captureNumber');
		$this->_addNumberField('rapprochementStatut');
		$this->_addNumberField('refoundAmount');
		$this->_addNumberField('refundDevise');
		$this->_addNumberField('timestamp');

		// Create boolean data field objects.
		$this->_addBooleanField('customerSendEmail', 'Envoi e-mail client souhaité : "0" = Non, "1" = Oui');
		$this->_addBooleanField('litige');

		// Create date data field objects.
		$this->_addDateField ('transmissionDate', 'Date de transaction');
		$this->_addDateField ('presentationDate', 'Date de remise demandée');
		$this->_addDateField ('cardExpirationDate',	'Date expiration de la carte');
		$this->_addDateField ('captureDate');
		$this->_addDateField ('markDate');
		$this->_addDateField ('authDate');
		$this->_addDateField ('autorisationDate');
		$this->_addDateField ('remiseDate');

		// Create 3ds result data field objects.
		$this->_addWSThreeDsField ('threeDsResult', 'Ne pas renseigner');

		// Parameters list for the refund ws function.
		$this->refund = array ('siteId', 'transmissionDate', 'transactionId', 'sequenceNumber','ctxMode',
					'newTransactionId',	'amount', 'devise', 'presentationDate', 'validationMode','comment');

		// Parameters list for the create ws function.
		$this->create = array('siteId', 'transmissionDate', 'transactionId', 'paymentMethod', 'orderId', 'orderInfo',
					'orderInfo2', 'orderInfo3','amount','devise', 'presentationDate', 'validationMode', 'cardNumber',
					'cardNetwork', 'cardExpirationDate', 'cvv', 'contractNumber','threeDsResult', 'subPaymentType',
					'subReference', 'subPaymentNumber','customerId','customerTitle','customerName', 'customerPhone',
					'customerMail', 'customerAddress', 'customerZipCode', 'customerCity', 'customerCountry','customerLanguage',
					'customerIP', 'customerSendEmail', 'ctxMode', 'comment'	);

		// Parameters list for the getInfo ws function.
		$this->getInfo = array ('siteId','transmissionDate', 'transactionId' , 'sequenceNumber', 'ctxMode');

		// Parameters list for the cancel ws function.
		$this->cancel= array ('siteId', 'transmissionDate', 'transactionId','sequenceNumber',
					'ctxMode', 'comment');

		// Parameters list for the validate ws function.
		$this->validate = array ('siteId','transmissionDate','transactionId','sequenceNumber','ctxMode',
					'comment');

		// Parameters list for the force ws function.
		$this->force = array ('siteId','transmissionDate','transactionId','sequenceNumber','ctxMode','autorisationNb',
					'autorisationDate','comment');

		// Parameters list for the modify ws function.
		$this->modify = array ('siteId','transmissionDate','transactionId','sequenceNumber','ctxMode',
					'amount','devise','remiseDate,$this->comment');

		// Parameters list for the modifyAndValidate ws function.
		$this->modifyAndValidate = array ('siteId','transmissionDate','transactionId','sequenceNumber','ctxMode',
					'amount','devise','remiseDate','comment');

		// Parameters list for the dulicate ws function.
		$this->duplicate = array ('siteId','transmissionDate','transactionId','sequenceNumber','ctxMode','orderId',
					'orderInfo','orderInfo2','orderInfo3','amount','devise','newTransactionId','presentationDate',
					'validationMode','comment');

		// Parameters list for the transactionInfo ws type.
		$this->transactionInfo = array ('errorCode', 'extendedErrorCode', 'transactionStatus', 'siteId', 'paymentMethod',
					'contractNumber', 'orderId', 'orderInfo', 'orderInfo2', 'orderInfo3', 'transmissionDate', 'transactionId',
					'sequenceNumber', 'amount', 'initialAmount', 'devise', 'cvAmount', 'cvDevise', 'presentationDate', 'type',
					'multiplePaiement', 'ctxMode', 'cardNumber', 'cardNetwork', 'cardType', 'cardCountry', 'cardExpirationDate',
					'customerId', 'customerTitle', 'customerName', 'customerPhone', 'customerMail', 'customerAddress',
					'customerZipCode', 'customerCity', 'customerCountry', 'customerLanguage', 'customerIP', 'transactionCondition',
					'vadsEnrolled', 'vadsStatus', 'vadsECI', 'vadsXID', 'vadsCAVVAlgorithm', 'vadsCAVV', 'vadsSignatureValid',
					'directoryServer', 'authMode', 'markAmount', 'markDevise', 'markDate', 'markNb', 'markResult', 'markCVV2_CVC2',
					'authAmount', 'authDevise', 'authDate', 'authNb', 'authResult', 'authCVV2_CVC2', 'warrantlyResult',
					'captureDate', 'captureNumber', 'rapprochementStatut', 'refoundAmount', 'refundDevise', 'litige', 'timestamp'
		);

		// Parameters list for the standardResponse ws type.
		$this->standardResponse = array ('errorCode', 'extendedErrorCode', 'transactionStatus', 'timestamp');
	}

	/**
	 * Shortcut function used in constructor to create stadard ws fields
	 * @param string $name
	 * @param string $description
	 * @param boolean $required
	 * @access private
	 */
	function _addField($name, $description = null, $required = false) {
		$this->wsParams[$name] = new VadsWSField($name, $description, $required);
	}

	/**
	 * Shortcut function used in constructor to create Number ws fields
	 * @param string $name
	 * @param string $description
	 * @param boolean $required
	 * @access private
	*/
	function _addNumberField($name, $description = null, $required = false) {
		$this->wsParams[$name] = new VadsWSNumberField($name, $description, $required);
	}

	/**
	 * Shortcut function used in constructor to create Boolean ws fields
	 * @param string $name
	 * @param string $description
	 * @param boolean $required
	 * @access private
	*/
	function _addBooleanField($name, $description = null, $required = false) {
		$this->wsParams[$name] = new VadsWSBooleanField($name, $description, $required);
	}

	/**
	 * Shortcut function used in constructor to create date ws fields
	 * @param string $name
	 * @param string $description
	 * @param boolean $required
	 * @access private
	 */
	function _addDateField($name, $description = null, $required = true) {
		$this->wsParams[$name] = new VadsWSDateField($name, $description, $required);
	}

	/**
	 * Shortcut function used in constructor to create 3ds result ws fields
	 * @param string $name
	 * @param string $description
	 * @param boolean $required
	 * @access private
	*/
	function _addWSThreeDsField($name, $description = null, $required = false) {
		$this->wsParams[$name] = new VadsWSThreeDsField($name, $description, $required);
	}

	/**
	 * Shortcut for setting multiple values with one array
	 * @param string $method
	 * @param array[string, mixed] $parameters
	 * @return boolean true on success
	 */
	function setFromArray($fieldSet, $parameters) {
		$ok = true;
		// Method parameters name list
		$fields = $this->$fieldSet;
		foreach ($fields as $field) {
			$value = key_exists($field, $parameters) ? $parameters[$field] : null;
			$ok &= $this->set($fieldSet, $field, $value);
		}
		return $ok;
	}

	/**
	 * General setter.
	 * @param string $method
	 * @param string $name
	 * @param mixed $value
	 * @return boolean true on success
	 */
	function set($fieldSet, $name, $value) {
		if (!$name || !in_array($name, $this->$fieldSet)) {
			return false;
		}
		$this->wsParams[$name]->setValue($value);
		return true;
	}

	/**
	 * Public static method to compute the signature. The parameters list to use is
	 * defined in $this->$method array
	 *
	 * @param string $method
	 * @access public
	 */
	function getSignature ($fieldSet) {
		$raw_sign =	'';
		// Method parameters name list
		$fields = $this->$fieldSet;
		foreach($fields as $field) {
			$paramObject = array_key_exists($field, $this->wsParams) ? $this->wsParams[$field] : null;
			if($paramObject && !$paramObject->inSignature()) {
				continue;
			}
			$value = $paramObject ? $paramObject->getValue4Sign() : null;
			if ($value == null) {
				$value = '';
			}
			$raw_sign .= $value . '+';
		}
		$raw_sign .= $this->key;
		return sha1($raw_sign);
	}

	/**
	 * Public static method to prepare data for ws call. The parameters list to use is
	 * defined in $this->$method array. This method adds signature to data.
	 *
	 * @param string $method
	 * @access public
	 */
	function formatRequest($methodName, $with_sign=true) {
		$result = array();
		// Method parameters name list
		$fields = $this->$methodName;
		foreach($fields as $field) {
			$value = array_key_exists($field, $this->wsParams) ? $this->wsParams[$field]->getValue() : '';
			$result[$field] = $value;
		}

		if($with_sign) {
			// Calculate and add signature to request array.
			$result['wsSignature'] = $this->getSignature($methodName);
		}
		return $result;
	}

	/**************************************************************************************************************
	 *                                  Web service calls
	 ***************************************************************************************************************/

	/**
	 * Return the type of the ws result as defined in wsdl file.
	 * defined in $this->$method array. This method adds signature to data.
	 *
	 * @param string $method_name
	 * @access private
	 */
	function _getResponseType ($method_name){
		if ($method_name == self::METHOD_REFUND
			|| $method_name == self::METHOD_GET_INFO
			|| $method_name == self::METHOD_DUPLICATE
			|| $method_name == self::METHOD_CREATE) {

			return "transactionInfo";
		} else {
			return "standardResponse";
		}
	}

	/**
	 * Compute return signature and compare it to received one.
	 *
	 * @param string $method_name
	 * @param string $raw_result
	 * @access public
	 */
	function isAuthentified($method_name, $raw_result) {
		$response_type = $this->_getResponseType($method_name);
		$this->setFromArray($response_type, get_object_vars($raw_result));

		// Compute signature.
		$sign = $this->getSignature($response_type);
		return $raw_result->signature == $sign;
	}

	/**
	 * Return text message according to error code.
	 *
	 * @param string $error_code
	 * @access public
	*/
	function getErrorMessage ($error_code, $lang = 'fr') {
		$error_translations = array(
			"fr" => array(
				"0" => "Action réalisée avec succès",
				"1" => "Action non autorisée",
				"2" => "Transaction non trouvée",
				"3" => "Transaction pas dans le bon statut",
				"4" => "La combinaison TransactionId / Sequence / TransmissionDate existe déjà",
				"5" => "Mauvaise signature",
				"10" => "Mauvais montant",
				"11" => "Mauvaise devise",
				"12" => "Type de carte inconnu",
				"13" => "La date d'expiration de la carte est incorrecte",
				"14" => "Le CVV est obligatoire",
				"15" => "Contrat inconnu",
				"16" => "Mauvais numéro de carte (longeur, luhn, ...)",
				"50" => "Paramètre invalide 'siteId'",
				"51" => "Paramètre invalide 'transmissionDate'",
				"52" => "Paramètre invalide 'transactionId'",
				"53" => "Paramètre invalide 'ctxMode'",
				"50" => "Paramètre invalide 'siteId'",
				"51" => "Paramètre invalide 'transmissionDate'",
				"52" => "Paramètre invalide 'transactionId'",
				"53" => "Paramètre invalide 'ctxMode'",
				"54" => "Paramètre invalide 'comment'",
				"57" => "Paramètre invalide 'presentationDate'",
				"58" => "Paramètre invalide 'newTransactionId'",
				"59" => "Paramètre invalide 'validationMode'",
				"60" => "Paramètre invalide 'orderId'",
				"61" => "Paramètre invalide 'orderInfo'",
				"62" => "Paramètre invalide 'orderInfo2'",
				"63" => "Paramètre invalide 'orderInfo3'",
				"64" => "Paramètre invalide 'PaymentMethod'",
				"65" => "Paramètre invalide 'cardNetwork'",
				"66" => "Paramètre invalide 'contractNumber'",
				"67" => "Paramètre invalide 'customerId'",
				"68" => "Paramètre invalide 'customerTitle'",
				"69" => "Paramètre invalide 'customerName'",
				"70" => "Paramètre invalide 'customerPhone'",
				"71" => "Paramètre invalide 'customerMail'",
				"72" => "Paramètre invalide 'customerAddress'",
				"73" => "Paramètre invalide 'customerZipCode'",
				"74" => "Paramètre invalide 'customerCity'",
				"75" => "Paramètre invalide 'customerCountry'",
				"76" => "Paramètre invalide 'customerLanguage'",
				"77" => "Paramètre invalide 'customerIP'",
				"78" => "Paramètre invalide 'customerSendMail'",
				"99" => "Erreur inconnue"
			),

			"en" => array(
				"0" => "Action completed successfully",
				"1" => "Unauthorized action",
				"2" => "Transaction not found",
				"3" => "Transaction in bad status",
				"4" => "The combination TransactionId / Sequence / TransmissionDate already exists",
				"5" => "Bad signature",
				"10" => "Bad amount",
				"11" => "Bad currency",
				"12" => "Unknown card type",
				"13" => "Card expiration date is incorrect",
				"14" => "CVV is mandatory",
				"15" => "Unknown contract",
				"16" => "Invalid card number (length, luhn, ...)",
				"50" => "Invalid parameter 'siteId'",
				"51" => "Invalid parameter 'transmissionDate'",
				"52" => "Invalid parameter 'transactionId'",
				"53" => "Invalid parameter 'ctxMode'",
				"50" => "Invalid parameter 'siteId'",
				"51" => "Invalid parameter 'transmissionDate'",
				"52" => "Invalid parameter 'transactionId'",
				"53" => "Invalid parameter 'ctxMode'",
				"54" => "Invalid parameter 'comment'",
				"57" => "Invalid parameter 'presentationDate'",
				"58" => "Invalid parameter 'newTransactionId'",
				"59" => "Invalid parameter 'validationMode'",
				"60" => "Invalid parameter 'orderId'",
				"61" => "Invalid parameter 'orderInfo'",
				"62" => "Invalid parameter 'orderInfo2'",
				"63" => "Invalid parameter 'orderInfo3'",
				"64" => "Invalid parameter 'PaymentMethod'",
				"65" => "Invalid parameter 'cardNetwork'",
				"66" => "Invalid parameter 'contractNumber'",
				"67" => "Invalid parameter 'customerId'",
				"68" => "Invalid parameter 'customerTitle'",
				"69" => "Invalid parameter 'customerName'",
				"70" => "Invalid parameter 'customerPhone'",
				"71" => "Invalid parameter 'customerMail'",
				"72" => "Invalid parameter 'customerAddress'",
				"73" => "Invalid parameter 'customerZipCode'",
				"74" => "Invalid parameter 'customerCity'",
				"75" => "Invalid parameter 'customerCountry'",
				"76" => "Invalid parameter 'customerLanguage'",
				"77" => "Invalid parameter 'customerIP'",
				"78" => "Invalid parameter 'customerSendMail'",
				"99" => "Unknown error"
			)
		);

		if(!array_key_exists($lang, $error_translations)) { // Language not supported, use fr
			$lang = 'fr';
		}

		if(array_key_exists($error_code, $error_translations[$lang])){
			return $error_translations[$lang][$error_code];
		}

		return $error_translations[$lang]["99"]; // Unknown error
	}

	/**************************************************************************************************************
	 *                                  Web service calls
	 ***************************************************************************************************************/

	/**
	 * Effective call to ws refund method.
	 *
	 * @param string $params
	 * @access public
	 */
	function refund ($params) {
		$this->setFromArray(self::METHOD_REFUND, $params);
		$soap_params = $this->formatRequest(self::METHOD_REFUND);

		$result = $this->client->__soapCall(self::METHOD_REFUND, $soap_params);

		return $result;
	}

	/**
	 * Effective call to ws create method.
	 *
	 * @param string $params
	 * @access public
	 */
	function create($params){
		$this->setFromArray(self::METHOD_CREATE, $params);
	    $soap_params = array (
	    	'createInfo' => $this->formatRequest(self::METHOD_CREATE, false),
	    	'wsSignature' => $this->getSignature(self::METHOD_CREATE)
	    );

	    $res = $this->client->__soapCall(self::METHOD_CREATE, $soap_params);
		return $res;
	}

    public function getInfoFromTransaction(Transaction $transaction) {
        $params =[
            'siteId' => $transaction->charge->shop_id,
            'transmissionDate' => DateTime::createFromFormat(PayzenApi\FormApi::DATE_FORMAT, $transaction->trans_date)->getTimestamp(),
            'transactionId' => $transaction->trans_id,
            'sequenceNumber' => "1", // FIXME hard-coded
            'ctxMode' => "TEST", // FIXME hard-coded
            // 'shop_key' => $transaction->charge->shop_key
        ];
//         $params = array_map(function ($val) {
//             return [
//                 '_' => $val
//             ];
//         }, $params);
        return $this->getInfo($params);
    }

	/**
	 * Effective call to ws getInfo method.
	 *
	 * @param string $params
	 * @access public
	 */
	function getInfo($params){
		$this->setFromArray(self::METHOD_GET_INFO, $params);
		$param_soap = $this->formatRequest(self::METHOD_GET_INFO);
        $res = $this->client->__soapCall(self::METHOD_GET_INFO, $param_soap);
        \Log::debug("soapCall getInfo failled with params : " . var_export($params, true) .
             "\nrequest : " . $this->client->__getLastRequest() . "\nresponse :" . $this->client->__getLastResponse()."\nResult : ".var_export((array)$res,true));
        return $res;
	}

	/**
	 * Effective call to ws modify method.
	 *
	 * @param string $params
	 * @access public
	 */
	function modify($params){
		$this->setFromArray(self::METHOD_MODIFY, $params);
		$param_soap = $this->formatRequest(self::METHOD_MODIFY);

		$res = $this->client->__soapCall(self::METHOD_MODIFY, $param_soap);
		return $res;
	}

	/**
	 * Effective call to ws modifyAndValidate method.
	 *
	 * @param string $params
	 * @access public
	 */
	function modifyAndValidate($params){
		$this->setFromArray(self::METHOD_MODIFY_AND_VALIDATE, $params);
		$param_soap = $this->formatRequest(self::METHOD_MODIFY_AND_VALIDATE);

		$res = $this->client->__soapCall(self::METHOD_MODIFY_AND_VALIDATE, $param_soap);
		return $res;
	}

	/**
	 * Effective call to ws validate method.
	 *
	 * @param string $params
	 * @access public
	 */
	function validate($params){
		$this->setFromArray(self::METHOD_VALIDATE, $params);
		$param_soap = $this->formatRequest(self::METHOD_VALIDATE);

		$res = $this->client->__soapCall(self::METHOD_VALIDATE, $param_soap);
		return $res;
	}

	/**
	 * Effective call to ws cancel method.
	 *
	 * @param string $params
	 * @access public
	 */
	function cancel($params){
		$this->setFromArray(self::METHOD_CANCEL, $params);
		$param_soap = $this->formatRequest(self::METHOD_CANCEL);

		$res = $this->client->__soapCall(self::METHOD_CANCEL, $param_soap);
		return $res;
	}

	/**
	 * Effective call to ws force method.
	 *
	 * @param string $params
	 * @access public
	 */
	function force($params){
		$this->setFromArray(self::METHOD_FORCE, $params);
		$param_soap = $this->formatRequest(self::METHOD_FORCE);

		$res = $this->client->__soapCall(self::METHOD_FORCE, $param_soap);
		return $res;
	}

	/**
	 * Effective call to ws duplicate method.
	 *
	 * @param string $params
	 * @access public
	 */
	function duplicate($params){
		$this->setFromArray(self::METHOD_DUPLICATE, $params);
		$param_soap = $this->formatRequest(self::METHOD_DUPLICATE);

		$res = $this->client->__soapCall(self::METHOD_DUPLICATE, $param_soap);
		return $res;
	}
}