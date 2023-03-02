<?php
namespace Foxsea\Paghiper\Helper;

use \Foxsea\Paghiper\Model\CybersourceSdk\CybsSoapClient;
use \Magento\Framework\App\RequestInterface;
use \Magento\Sales\Model\OrderFactory;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $cart;
    protected $cybsSoapClient;
    protected $orderFactory;
    protected $request;
    protected $debug;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Foxsea\Paghiper\Model\CybersourceSdk\CybsSoapClient $cybsSoapClient
    ) {
        $this->cart = $cart;
        $this->cybsSoapClient = $cybsSoapClient;
        $this->orderFactory = $orderFactory;
        $this->request = $request;
        $this->debug = true;

        parent::__construct($context);
    }

    protected function info($message) {
        if ($this->debug) {
            $this->_logger->debug($message);
        }
    }

    public function createOrderArray($order, $payment) {
        if ($order && $order->getRealOrderId()) {
            $this->info("Creating order array");
            $address = $order->getBillingAddress();

            $discount_payment = 0;
            $discount_days = 0;
            $early_payment = $this->helper()->getConfig('early_payment_discount');

            if ($early_payment == 1) {
                $discount_payment = $this->helper()->getConfig('early_payment_discounts_cents');
                if ($discount_payment != '' && intval($discount_payment) >= 1) {
                    $discount_payment = $order->getGrandTotal() * ($discount_payment / 100);
                    $discount_payment = intval($discount_payment * 100);
                }
                $discount_days = $this->helper()->getConfig('early_payment_discounts_days');
            }

            $this->info(
                "CPF/CNPJ\n" .
                "From Order additional information  ............  " .
                print_r($payment->getAdditionalInformation('paghiper_taxvat'), true) . "\n" .
                "From Customer Tax VAT  ........................  " .
                print_r($order->getCustomerTaxvat(), true)
            );


            $taxvat = ($payment->getAdditionalInformation('paghiper_taxvat') != '') ? $payment->getAdditionalInformation('paghiper_taxvat') : $order->getCustomerTaxvat();


            // si no existe lo obtenemos del POST?
            if (!$taxvat) {
                if (isset($this->request->getPost()['payment']['foxsea_paghiper_taxvat']) && $this->request->getPost()['payment']['foxsea_paghiper_taxvat'] != '') {
                    $taxvat = $this->request->getPost()['payment']['foxsea_paghiper_taxvat'];
                }
            }

            $this->info("Tax VAT value: " . print_r($taxvat, true));
            $cpfCnpjValidation = $this->validateTaxvat($taxvat);
            $this->info("Tax VAT validation result: " . print_r($cpfCnpjValidation, true) );

            if (!$cpfCnpjValidation) {
                $this->info("Error: CPF/CNPJ inválido.");
                return [
                    'error' => true,
                    'error_message' => 'CPF/CNPJ inválido.'
                ];
            }

            $name = $address->getFirstname() . ' ' . $address->getLastname();
            $this->info("Name: " . $name);

            $data = [
                'order_id'                      => $order->getIncrementId(),
                'payer_email'                   => $order->getCustomerEmail(),
                'payer_name'                    => $name,
                'payer_cpf_cnpj'                => $this->cleanCpfCnpj($taxvat),
                'payer_phone'                   => $address->getTelephone(),
                'payer_street'                  => (isset($address->getStreet()[0])) ? $address->getStreet()[0] : '',
                'payer_number'                  => (isset($address->getStreet()[1])) ? $address->getStreet()[1] : '',
                'payer_complement'              => (isset($address->getStreet()[2])) ? $address->getStreet()[2] : '',
                'payer_district'                => (isset($address->getStreet()[3])) ? $address->getStreet()[3] : '',
                'payer_city'                    => $address->getCity(),
                'payer_zip_code'                => $address->getPostcode(),
                'discount_cents'                => ($order->getDiscountAmount()*-1) * 100,
                'shipping_price_cents'          => $order->getShippingAmount() * 100,
                'shipping_methods'              => $order->getShippingDescription(),
                'fixed_description'             => false,
                'days_due_date'                 => $this->helper()->getConfig('days_due_date'), // vencimento
                'late_payment_fine'             => $this->helper()->getConfig('late_payment_fine'), // percentual multa
                'per_day_interest'              => $this->helper()->getConfig('per_day_interest'), // juros por atraso (bool)
                'early_payment_discounts_cents' => $discount_payment,
                'early_payment_discounts_days'  => $discount_days,
                'open_after_day_due'            => $this->helper()->getConfig('open_after_day_due'),
                'notification_url'              => $this->helper()->getNotificationUrl(),
                'type_bank_slip'                => 'boletoA4',
                'partners_id'                   => 'N2DXMMU6',
                'items'                         => []
            ];

            $this->info("Getting All Items info");

            foreach ($order->getAllVisibleItems() as $item) {
                $data['items'][] = [
                    'description' => $item->getName(),
                    'quantity' => $item->getQtyToShip() ?: 1,
                    'item_id' => $item->getSku(),
                    'price_cents' => $item->getPrice() * 100
                ];
            }

            $this->info(
                "Order data\n" .
                json_encode($data, JSON_PRETTY_PRINT)
            );

            return $data;
        } else {
            return ['error' => true];
        }
    }

    public function generate($data) {
        $this->info(
            '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------' . "\n" .
            "Request POST: \n" .
            print_r($this->request->getPost(), true) . "\n\n" .
            "Order data JSON: \n "   .
            json_encode($data, JSON_PRETTY_PRINT) . "\n" .
            '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'
        );

        $billTo = new \stdClass();
        $purchaseTotals = new \stdClass();
        $boletoPaymentService = new \stdClass();
        $personalID = new \stdClass();
        $ics = new \stdClass();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // Create a new product object
        $quote = $objectManager->create(\Magento\Quote\Model\QuoteManagement::class);
        // Get a request object singleton
        //$request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);

        $this->info("Reserver order increment ID: " . $this->cart->getQuote()->getReservedOrderId());

        $referenceCode = $this->cart->getQuote()->getReservedOrderId();//'000000014-' . time();           // Before using this example, you can use your own reference code for the transaction.
        $request = $this->cybsSoapClient->createRequest($referenceCode);

        // Build a sale request (combining an auth and capture). In this example only the amount is provided for the purchase total.
        //$ccAuthService->run = 'true';
        //$request->ccAuthService = $ccAuthService;

        //$ccCaptureService->run = 'true';
        //$request->ccCaptureService = $ccCaptureService;

        $billTo->firstName = $this->cart->getQuote()->getCustomer()->getFirstname();//'Indira';                                  //  billTo_firstName=Jason
        $billTo->lastName = $this->cart->getQuote()->getCustomer()->getLastname();//'Oliveira';                                 //  billTo_lastName=Ramirez

        $this->info(
            "Order billing address:\n" .
            json_encode(
                $this->cart->getQuote()->getBillingAddress()->getData('street'),
                JSON_PRETTY_PRINT
            )
        );

        $billTo->street1 = $this->cart->getQuote()->getBillingAddress()->getData('street');         //'Petrópolis, Pernambuco, 56596';             //  billTo_street1=Avenida Francisco Matarazzo 1987
        $billTo->city = $this->cart->getQuote()->getBillingAddress()->getData('city');              //'Pernambuco';                                   //  billTo_city=Sao Paulo
        $billTo->state = $this->cart->getQuote()->getBillingAddress()->getData('region');           //'PA';                                          //  billTo_state=SP
        $billTo->postalCode = $this->cart->getQuote()->getBillingAddress()->getData('postcode');    //'56596';                                  //  billTo_postalCode=05001-200
        $billTo->country = $this->cart->getQuote()->getBillingAddress()->getData('country_id');     //'BR';                                        //  billTo_country=BR
        $billTo->email = $this->cart->getQuote()->getShippingAddress()->getData('email');           //'aeNoo7zi@aeNoo7zi.com';                       //  billTo_email=test@cybersource.com
        $billTo->phoneNumber = $this->cart->getQuote()->getBillingAddress()->getData('telephone');  //'1121020053';                            //  billTo_phoneNumber=1121020053       Falta??
        //$billTo->ipAddress = '10.7.111.111';

        $request->billTo = $billTo;

        $billTo->buildingNumber = 'NA';
        $billTo->district = 'district';
        //$card->accountNumber = '4111111111111111';
        //$card->expirationMonth = '12';
        //$card->expirationYear = '2022';
        //$request->card = $card;

        $purchaseTotals->currency = 'BRL';                              //  purchaseTotals_currency=BRL
        $purchaseTotals->grandTotalAmount = $this->cart->getQuote()->getGrandTotal();//'788.00';                   //  purchaseTotals_grandTotalAmount=1.00
        $request->purchaseTotals = $purchaseTotals;

        $boletoPaymentService->run = 'true'; //  boletoPaymentService_run=true

        /*
        $boletoPaymentService->instruction = <<<INSTRUCTIONS
        Tiempo de entrega: dasdasdkasakjfghaksjfghasjkfghsajkfsghdfkjghdfkjasdfgsjkafghskajfghsdfkjhsgdfkjsdafghskdajfghsdkfjgfksjdfghsdjkgdkfjsghdafkjfgfgsdjkfsgdafjksgafjksdafgsjkdfghsdajkfghsdjkfsgdfjksgadfjksghdafkjasfgdkjjhdkasjdfghsfkjsfghdakjfghaksjfghasjkfghsajkfsghdfkjghdfkjasdfgsjkafghskajfghsdfkjhsgdfkjsdafghskdajfghsdkfjgfksjdfghsdjkgdkfjsghdafkjfgfgsdjkfsgdafjksgafjksdafgsjkdfghsdajkfghsdjkfsgdfjksgadfjksghdafkjasfgdkjjhdjksaddfgafhsdkgafkj.
        INSTRUCTIONS;
        */
        //$boletoPaymentService->instruction = "Tiempo de entrega: dasdasdkasjdfghsfkjs\nfghdakjfghaksjfghasjkfghsajkfsghdfkjghdfkj\nasdfgsjkafghskajfghsdfkjhsgdfkjsdafg\nhskdajfghsdkfjgfksjdfghsdjkgdkfjsghdafkjfgfgsdjk\nfsgdafjksgafjksdafgsjkdfghsdajkfghsdjkfsgdfjksg\nadfjksghdafkjasfgdkjjhdkasjdfghsfkjsfghdakjfghaksjfgha\nsjkfghsajkfsghdfkjghdfkjasdfgsjkafghskajfghsdf\nkjhsgdfkjsdafghskdajfghsdkfjgfksjdfghsdjkgdkfjsgh\ndafkjfgfgsdjkfsgdafjksgafjksdafgsjkdfghsdajkf\nghsdjkfsgdfjksgadfjksghdafkjasfgdkjjhdjks\naddfgafhsdkgafkjasghfksjasddf";        //boletoPaymentService_instruction=kasjdfghsfkjsfghdakjfghaksjfghasjkfghsajkfsghdfkjghdfkjasdfgsjkafghskajfghsdfkjhsgdfkjsdafghskdajfghsdkfjgfksjdfghsdjkgdkfjsghdafkjfgfgsdjkfsgdafjksgafjksdafgsjkdfghsdajkfghsdjkfsgdfjksgadfjksghdafkjasfgdkjjhdkasjdfghsfkjsfghdakjfghaksjfghasjkfghsajkfsghdfkjghdfkjasdfgsjkafghskajfghsdfkjhsgdfkjsdafghskdajfghsdkfjgfksjdfghsdjkgdkfjsghdafkjfgfgsdjkfsgdafjksgafjksdafgsjkdfghsdajkfghsdjkfsgdfjksgadfjksghdafkjasfgdkjjhdjksaddfgafhsdkgafkjasghfksjasddf

        $instructions = $this->helper()->getConfig('information');
        $boletoPaymentService->instruction = (string) $instructions;

        $expirationDateOverdue = (int) $this->helper()->getConfig('days_due_date');
        $expirationDate = \Date('Y-m-d', strtotime('+' . $expirationDateOverdue . ' days'));
        $this->info(
            '------------------------------------------------------------------------------------------------------------------------------------------------------------------' . "\n" .
            "Order instrucions:\n" .
            $instructions . "\n" .
            "Expiration date: \n" .
            $expirationDate . "\n" .
            '------------------------------------------------------------------------------------------------------------------------------------------------------------------'
        );

        $boletoPaymentService->expirationDate = $expirationDate;//'2022-07-01'; //boletoPaymentService_expirationDate=2022-07-01

        //$boletoPaymentService->reconciliationID =   $this->cart->getQuote()->getReservedOrderId() . '-'. time();  //'123343243'; //boletoPaymentService_reconciliationID=123343243
        //$boletoPaymentService->reconciliationID =   '123343243'; //boletoPaymentService_reconciliationID=123343243    // iosephus todo como se pone?
        // no lo pongo por que el manual dice que es del response

        $request->boletoPaymentService = $boletoPaymentService;

        $taxvat = ($this->cart->getQuote()->getPayment()->getAdditionalInformation('paghiper_taxvat') != '') ? $this->cart->getQuote()->getPayment()->getAdditionalInformation('paghiper_taxvat') : $this->cart->getQuote()->getCustomerTaxvat();

        $this->info("Tax VAT: . " . $taxvat);

        $personalID->number = $taxvat;//'18100164601';         //  personalID_number=18100164601     <----- equivalente al CURP
        $request->personalID = $personalID;

        //$request->merchantID = 'softtekbr_boleto';
        //$request->merchantReferenceCode = 'REF-' . time();

        //$request->id='4580713800000179089308----' . time();
        $request->id = $this->cart->getQuote()->getReservedOrderId() . '-' . time();

        //$request->offer0='amount:10.00^quantity:1^tax_amount:0.80';

        //$ics->applications = 'ics_boleto_payment,ics_tax';
        $ics->applications = 'ics_boleto_payment';

        //var_dump($request);
        //var_dump(get_class($this->cybsSoapClient));

        $this->info(
            "---------------------------------------------------------------------------------------------------------"  . "\n" .
            print_r($request, true)                                                                                      . "\n" .
            "---------------------------------------------------------------------------------------------------------"
        );
        $reply = $this->cybsSoapClient->runTransaction($request);

        $this->info(
            json_encode($reply, JSON_PRETTY_PRINT)
        );

        $response = json_encode($reply);
        $response = json_decode($response);
        $this->info("Response:\n" . print_r($response, true));
        $this->info("Reply:\n" . print_r($reply, true));
        $decision = $response->decision;
        $reasonCode = $response->reasonCode;
        $this->info("Decision: " . $decision);
        $this->info("Reason code: " . $reasonCode);

        /*
        {
            "merchantReferenceCode": "000000080",
            "requestID": "6475991893736239603007",
            "decision": "ACCEPT",
            "reasonCode": 100,
            "requestToken": "AxjnLwSTXxBw\/BPZyx0\/\/4oSxZM2bRmyaM1Eca9pPGe0OgqSB2GTSTL0Yt8U9gAABQj8",
            "purchaseTotals": {
                "currency": "BRL"
            },
            "boletoPaymentReply": {
                "url": "https:\/\/transactionsandbox.pagador.com.br\/post\/pagador\/reenvia.asp\/2f498d2e-ff48-4d72-b7d1-098f1496ebd5",
                "barCodeNumber": "00099.99921 50000.000518 49999.999904 6 90330000008361",
                "expirationDate": "2022-07-01 23:59:59",
                "reasonCode": 100,
                "requestDateTime": "2022-03-18T03:26:30Z",
                "amount": "83.61",
                "reconciliationID": "123343243",
                "boletoNumber": "5149-3",
                "avsCode": "5",
                "assignor": "CyberSource"
            },
            "additionalProcessorResponse": "2f498d2e-ff48-4d72-b7d1-098f1496ebd5"
        }
        */

        $result['create_request']['result']                         = 'success';
        $result['create_request']['bank_slip']['url_slip_pdf']      = rand();
        $result['create_request']['bank_slip']['digitable_line']    = rand();
        $result['create_request']['due_date']                       = rand();

        $result = json_encode($result);
        $result = json_decode($result);

        if ($decision == 'REJECT' && $reasonCode != 100) {
            $this->info('Problema ao gerar boleto #'. $data['order_id'] .': ' . $result->create_request->response_message);
            return ['success' => false, 'message' => $decision . ' by code ' .$reasonCode];
        } else if ($decision == 'ACCEPT' && $reasonCode == 100) {
            $additional = [
                'merchant_reference_code'               => $response->merchantReferenceCode,
                'request_id'                            => $response->requestID,
                'decision'                              => $response->decision,
                'reason_code'                           => $response->reasonCode,
                'request_token'                         => $response->requestToken,
                'purchase_totals_currency'              => $response->purchaseTotals->currency,
                'boleto_url'                            => $response->boletoPaymentReply->url,
                'linha_digitavel'                       => $response->boletoPaymentReply->barCodeNumber,
                'vencimento'                            => $response->boletoPaymentReply->expirationDate,
                'boleto_url'                            => $response->boletoPaymentReply->url,
                'boleto_bar_code_number'                => $response->boletoPaymentReply->barCodeNumber,
                'boleto_expiration_date'                => $response->boletoPaymentReply->expirationDate,
                'boleto_reason_code'                    => $response->boletoPaymentReply->reasonCode,
                'boleto_request_date_time'              => $response->boletoPaymentReply->requestDateTime,
                'boleto_amount'                         => $response->boletoPaymentReply->amount,
                'boleto_reconciliation_id'              => $response->boletoPaymentReply->reconciliationID,
                'boleto_boleto_number'                  => $response->boletoPaymentReply->boletoNumber,
                'boleto_avs_code'                       => $response->boletoPaymentReply->avsCode,
                'boleto_assignor'                       => $response->boletoPaymentReply->assignor,
                'boleto_additional_processor_response'  => $response->additionalProcessorResponse,
            ];
            return ['success' => true, 'additional' => $additional];
        }
    }


    public function addInformation($order, $additional) {
        if ($order && is_array($additional) && count($additional) >= 1) {
            $_additional = $order->getPayment()->getAdditionalInformation();
            foreach ($additional as $key => $value) {
                $_additional[$key] = $value;
            }
            $this->info(json_encode($_additional, JSON_PRETTY_PRINT));
            $order->getPayment()->setAdditionalInformation($_additional);
        } else {
            $this->info('Problema no IF');
            $this->info(var_export($additional));
        }
    }


    protected function helper() {
        return \Magento\Framework\App\ObjectManager::getInstance()->get('Foxsea\Paghiper\Helper\Data');
    }


    public function validateTaxvat($taxvat) {
//        // Deixa apenas números no valor
//        $taxvat = preg_replace( '/[^0-9]/', '', $taxvat );
//        // Garante que o valor é uma string
//        $taxvat = (string) $taxvat;
        $this->info("Before cleaning: " . $taxvat);

        $taxvat = str_replace('-', '', $taxvat);
        $taxvat = str_replace('.', '', $taxvat);
        $taxvat = str_replace('/', '', $taxvat);

        $this->info("After cleaning: " . $taxvat);
        //Caso seja CNPJ, Verifica CNPJ

        $this->info("Tax VAT length: " . strlen($taxvat));

        if (strlen($taxvat) == 14) {
            $this->info("Invoke validateCnpj");
            return $this->validateCnpj($taxvat);
        }

        // Verifica CPF, Caso seja CPF
        if (strlen($taxvat) == 11) {
            $this->info("Invoke validateCpf");
            return $this->validateCpf($taxvat);
        }
        $this->info("Invalid CPF / CNPJ length");
        // Não retorna nada
        return false;
    }


    public function cleanCpfCnpj($taxvat) {
        $taxvat = preg_replace('#[^0-9]#', '', $taxvat);
        $taxvat = str_pad($taxvat, 11, '0', STR_PAD_LEFT);
        return $taxvat;
    }


    private function validateCpf($taxvat)
    {
        if (empty($taxvat)) {
            $this->info("Error: Tax VAT is empty");
            return false;
        }

        $this->info("Tax VAT value is " . $taxvat);
        $taxvat = preg_replace('#[^0-9]#', '', $taxvat);
        $this->info("Tax VAT value is " . $taxvat);
        $taxvat = str_pad($taxvat, 11, '0', STR_PAD_LEFT);
        $this->info("Tax VAT value is " . $taxvat);

        if (strlen($taxvat) != 11) {
            $this->info("Error: Tax VAT length is not equal to 11, it is " . strlen($taxvat));
            return false;
        }

        if ($taxvat == '00000000000' ||
            $taxvat == '11111111111' ||
            $taxvat == '22222222222' ||
            $taxvat == '33333333333' ||
            $taxvat == '44444444444' ||
            $taxvat == '55555555555' ||
            $taxvat == '66666666666' ||
            $taxvat == '77777777777' ||
            $taxvat == '88888888888' ||
            $taxvat == '99999999999'
        ) {
            $this->info("Error: Tax VAT is in black list");
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $taxvat{$c} * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($taxvat{$c} != $d) {
                $this->info("Error: Tax VAT is invalid");
                return false;
            }
        }
        $this->info("Log: Tax VAT is valid");
        return true;
    }


    private function validateCnpj($taxvat)
    {
        $this->info(__METHOD__);
        $taxvat = preg_replace('/[^0-9]/', '', (string) $taxvat);

        if (strlen($taxvat) != 14) {
            $this->info("Error: CNPJ length is not 14");
            return false;
        }

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $taxvat{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($taxvat{12} != ($resto < 2 ? 0 : 11 - $resto)) {
            $this->info("Error: checksum is incorrect");
            return false;
        }

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $taxvat{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        $this->info("Final velidation test:\n" . print_r($taxvat{13}, true) . "\n" . print_r(  ($resto < 2 ? 0 : 11 - $resto), true)  );
        return $taxvat{13} == ($resto < 2 ? 0 : 11 - $resto);
    }
}
