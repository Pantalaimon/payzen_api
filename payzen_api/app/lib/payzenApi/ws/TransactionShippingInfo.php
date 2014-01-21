<?php
namespace PayzenApi\ws;

class TransactionShippingInfo {

    public $shippingCity,     // xs:string
    $shippingCountry,     // xs:string
    $shippingDeliveryCompanyName,     // xs:string
    $shippingName,     // xs:string
    $shippingPhone,     // xs:string
    $shippingSpeed,     // tns:deliverySpeed
    $shippingState,     // xs:string
    $shippingStatus,     // TODO tns:custStatus
    $shippingStreetNumber,     // xs:string
    $shippingStreet,     // xs:string
    $shippingStreet2,     // xs:string
    $shippingDistrict,     // xs:string
    $shippingType,     // TODO tns:deliveryType
    $shippingZipCode;    // xs:string


}