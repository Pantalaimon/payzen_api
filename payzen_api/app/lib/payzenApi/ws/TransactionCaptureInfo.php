<?php
namespace PayzenApi\ws;

class TransactionCaptureInfo {

    public $captureDate,     // xs:dateTime
    $captureNumber,     // xs:int
    $rapprochementStatut,     // xs:int
    $refundAmount,     // xs:long
    $refundCurrency;    // xs:int


}