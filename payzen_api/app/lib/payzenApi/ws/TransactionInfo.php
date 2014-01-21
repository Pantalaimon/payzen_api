<?php
namespace PayzenApi\ws;

class TransactionInfo {

    public $errorCode, $extendedErrorCode, $transactionStatus, $timestamp, $signature, $transactionStatusLabel,
    $paymentGeneralInfo,     // TransactionPaymentGeneralInfo
    $cardInfo,     // TransactionCardInfo
    $threeDSInfo,     // TransactionThreeDSInfo
    $authorizationInfo,     // TransactionAuthorizationInfo
    $markInfo,     // TransactionMarkInfo
    $warrantyDetailsInfo,     // TransactionWarrantyDetailsInfo
    $captureInfo,     // TransactionCaptureInfo
    $customerInfo,     // TransactionCustomerInfo
    $shippingInfo,     // TransactionShippingInfo
    $extraInfo,     // TransactionExtraInfo
    $paymentOptionInfo,     // TransactionPaymentOptionInfo
    $boletoInfo;    // TransactionBoletoExtraInfo

}