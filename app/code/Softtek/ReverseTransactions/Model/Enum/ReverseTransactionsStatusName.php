<?php


namespace Softtek\ReverseTransactions\Model\Enum;


abstract class ReverseTransactionsStatusName
{
    //TODO: Add new status from new Payment methods   NOT APPLY debito o prepago
    const authorized = "AUTHORIZED";
    const processed = "PROCESSED";
    const completed = "COMPLETED";
    const cancelled = "CANCELED";
    const reversed = "REVERSED";
    const nullified = "NULLIFIED";
    const pending = "PENDING";
    const notapply = "NOT APPLY";
    const error = "ERROR";
}
