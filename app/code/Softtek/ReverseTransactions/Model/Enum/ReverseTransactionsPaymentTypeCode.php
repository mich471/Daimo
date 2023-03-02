<?php


namespace Softtek\ReverseTransactions\Model\Enum;


abstract class ReverseTransactionsPaymentTypeCode
{
    const ventaDebito  = "VD";
    const ventaNormal = "VN";
    const ventaCuotas = "VC";
    const ventaSnInteres = "S1";
    const ventaSnInteres2 = "S2";
    const ventaNcuotas = "NC";
    const ventaPrepago = "VP";
}
