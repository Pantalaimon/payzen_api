<?php
namespace PayzenApi\ws;

class TransactionCardInfo {

    public $cardNumber,     // xs:string
    $cardNetwork,     // xs:string
    $cardBrand,     // xs:string
    $cardCountry,     // xs:long
    $cardProductCode,     // xs:string
    $cardBankCode,     // xs:string
    $expiryMonth,     // xs:int
    $expiryYear,     // xs:int
    $contractNumber;    // xs:string

}