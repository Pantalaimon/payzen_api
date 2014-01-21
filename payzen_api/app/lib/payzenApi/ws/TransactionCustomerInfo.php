<?php
namespace PayzenApi\ws;

class TransactionCustomerInfo {

    public $customerId,     // xs:string
    $customerTitle,     // xs:string
    $customerStatus,     // tns:custStatus
    $customerName,     // xs:string
    $customerPhone,     // xs:string
    $customerEmail,     // xs:string
    $customerAddressNumber,     // xs:string
    $customerAddress,     // xs:string
    $customerDistrict,     // xs:string
    $customerZip,     // xs:string
    $customerCity,     // xs:string
    $customerCountry,     // xs:string
    $language,     // xs:string
    $customerIP,     // xs:string
    $customerCellPhone,     // xs:string
    $extInfo; // TODO tns:extInfo
}