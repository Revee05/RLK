<?php

namespace App\Services;

use Xendit\Xendit;

class XenditService
{
    public function __construct()
    {
        Xendit::setApiKey(env('XENDIT_SECRET_KEY'));
    }

    public function createInvoice($params)
    {
        return \Xendit\Invoice::create($params);
    }

    public function getInvoice($invoiceId)
    {
        return \Xendit\Invoice::retrieve($invoiceId);
    }
}
