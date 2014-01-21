<?php
namespace PayzenApi\ws;

class TransactionPaymentGeneralInfo {

    public $siteId,     // type="xs:string"
    $paymentSource,     // type="xs:string"
    $orderId,     // type="xs:string"
    $orderInfo,     // type="xs:string"
    $orderInfo2,     // type="xs:string"
    $orderInfo3,     // type="xs:string"
    $transmissionDate,     // type="xs:dateTime"
    $transactionId,     // type="xs:string"
    $sequenceNumber,     // type="xs:int"
    $amount,     // type="xs:long"
    $initialAmount,     // type="xs:long"
    $currency,     // type="xs:int"
    $effectiveAmount,     // type="xs:long"
    $effectiveCurrency,     // type="xs:int"
    $presentationDate,     // type="xs:dateTime"
    $type,     // type="xs:int"
    $multiplePayment,     // type="xs:int"
    $effectiveCreationDate,     // type="xs:dateTime"
    $extTransId;    // type="xs:string"

}