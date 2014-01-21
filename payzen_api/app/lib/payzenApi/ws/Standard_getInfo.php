<?php
namespace PayzenApi\ws;

class Standard_getInfo {

    public $siteId,     // xsd:string
    $transmissionDate,     // xsd:dateTime
    $transactionId,     // xsd:string
    $sequenceNumber,     // xsd:int
    $ctxMode,     // xsd:string
    $wsSignature;    // xsd:string

}