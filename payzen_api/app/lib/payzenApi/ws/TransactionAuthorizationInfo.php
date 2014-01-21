<?php
namespace PayzenApi\ws;

class TransactionAuthorizationInfo {

    public $authMode,     // xs:string
    $authAmount,     // xs:long
    $authCurrency,     // xs:int
    $authDate,     // xs:dateTime
    $authNumber,     // xs:string
    $authResult,     // xs:int
    $authCVV2_CVC2;    // xs:string


}

