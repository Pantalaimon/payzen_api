<?php
namespace PayzenApi\ws;

class TransactionThreeDSInfo {

    public $threeDSTransactionCondition,     // xs:string
    $threeDSEnrolled,     // xs:string
    $threeDSStatus,     // xs:string
    $threeDSEci,     // xs:string
    $threeDSXid,     // xs:string
    $threeDSCavvAlgorithm,     // xs:string
    $threeDSCavv,     // xs:string
    $threeDSSignValid,     // xs:string
    $threeDSBrand;    // xs:string

}