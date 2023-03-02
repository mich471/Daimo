<?php
namespace Foxsea\Paghiper\Model\CybersourceSdk;

use Foxsea\Paghiper\Helper\Data;
use \SoapClient;
use \SoapVar;

// esto sera desde la configuracion de admin
//set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/conf');

/**
 * CybsClient
 *
 * An implementation of PHP's SOAPClient class for making either name-value pair
 * or XML CyberSource requests.
 */
class CybsClient extends \SoapClient
{
    const CLIENT_LIBRARY_VERSION    = "CyberSource PHP 1.0.0";
    const CLIENT_WSDL_NVP           = "https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_NVP_1.192.wsdl";
    const CLIENT_WSDL               = "https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.192.wsdl";
    const CLIENT_MERCHANT_ID        = "softtekbr_boleto";// TODO hacer administrable
    const CLIENT_TRANSACTION_KEY    = "6zuR9IdA0uP/YgsXFm12i/FO1cF9R24WfUX51QAi/S4cpzSwIxT8uHMTYcmnR1azRqeUNocB3UBJ9UusZo7CWUp72x8e6hgmooyR7Ypc8ONlpY0oQLF3c2I0/6akzv5FPPT9+/w7ZXlOtEQqa1ZRVTdhC1iiGMTK9Yn94ZPZBNxrSVvFI2u9myP04buI0C5/JZZfSPW/dhDaBk2fAp2NJX+HUKPXXp+XGoRfPpw6L47PIzgd42/lD2amq7fl/NeuDsQWouoPvaL+KU0OwMeSXgheKzBr+qXATG8D3ty3gJoVVQei7yz51HzbEoYpBOok8l6BRHQxeoUDo4Sp5qtjzg==";

    private $merchantId;
    private $transactionKey;
    private $helperData;

    public function __construct(
        $options = [],
        $properties,
        $nvp = false,
        Data $helperData
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helperData = $helperData;

        $_nvp               = $nvp;
        $_wsdl              = self::CLIENT_WSDL;
        $_wsdlNvp           = self::CLIENT_WSDL_NVP;
        $_merchantId        = self::CLIENT_MERCHANT_ID;
        $_transactionKey    = self::CLIENT_TRANSACTION_KEY;

        $_nvp               = $this->helperData->getConfig('nvp');
        $_wsdl              = $this->helperData->getConfig('wsdl');                     //  $wsdl = "https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.192.wsdl";
        $_wsdlNvp           = $this->helperData->getConfig('nvp_wsdl');                 //  $wsdl = "https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_NVP_1.192.wsdl";
        $_merchantId        = $this->helperData->getPtMerchantId();              //  "softtekbr_boleto"; // TODO hacer administrable
        $_transactionKey    = $this->helperData->getPtTransactionKey();          //  "6zuR9IdA0uP/YgsXFm12i/FO1cF9R24WfUX51QAi/S4cpzSwIxT8uHMTYcmnR1azRqeUNocB3UBJ9UusZo7CWUp72x8e6hgmooyR7Ypc8ONlpY0oQLF3c2I0/6akzv5FPPT9+/w7ZXlOtEQqa1ZRVTdhC1iiGMTK9Yn94ZPZBNxrSVvFI2u9myP04buI0C5/JZZfSPW/dhDaBk2fAp2NJX+HUKPXXp+XGoRfPpw6L47PIzgd42/lD2amq7fl/NeuDsQWouoPvaL+KU0OwMeSXgheKzBr+qXATG8D3ty3gJoVVQei7yz51HzbEoYpBOok8l6BRHQxeoUDo4Sp5qtjzg==";

        if (!$properties["merchant_id"]) {
            $properties["merchant_id"] = $_merchantId;
        }
        if (!$properties["transaction_key"]) {
            $properties["transaction_key"] = $_transactionKey;
        }
        if (!$properties["wsdl"]) {
            $properties["wsdl"] = $_wsdl;
        }
        if (!$properties["nvp_wsdl"]) {
            $properties["nvp_wsdl"] = $_wsdlNvp;
        }

        $required = array('merchant_id', 'transaction_key');

        /*
        if (!$properties) {
            throw new Exception('Unable to read cybs.ini.');
        }
        */

        if ($_nvp) {
            array_push($required, 'nvp_wsdl');
            // Modify the URL to point to either a live or test WSDL file with the desired API version.
            // ojo, esta cambia cuando es prod
            $wsdl = $_wsdlNvp;
        } else {
            array_push($required, 'wsdl');
            // Modify the URL to point to either a live or test WSDL file with the desired API version for the name-value pairs transaction API.
            // ojo, esta cambia cuando es prod
            // se le quita el TEST
            $wsdl = $_wsdl;
        }

        foreach ($required as $req) {
            if (empty($properties[$req])) {
                throw new Exception(__('Invalid seller payment information'));
            }
        }

        parent::__construct($wsdl, $options);
        $this->merchantId = $_merchantId;
        $this->transactionKey = $_transactionKey;

        $nameSpace = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";

        $soapUsername = new \SoapVar(
            $this->merchantId,
            XSD_STRING,
            NULL,
            $nameSpace,
            NULL,
            $nameSpace
        );

        $soapPassword = new \SoapVar(
            $this->transactionKey,
            XSD_STRING,
            NULL,
            $nameSpace,
            NULL,
            $nameSpace
        );

        $auth = new \stdClass();
        $auth->Username = $soapUsername;
        $auth->Password = $soapPassword;

        $soapAuth = new \SoapVar(
            $auth,
            SOAP_ENC_OBJECT,
            NULL, $nameSpace,
            'UsernameToken',
            $nameSpace
        );

        $token = new \stdClass();
        $token->UsernameToken = $soapAuth;

        $soapToken = new \SoapVar(
            $token,
            SOAP_ENC_OBJECT,
            NULL,
            $nameSpace,
            'UsernameToken',
            $nameSpace
        );

        $security =new \SoapVar(
            $soapToken,
            SOAP_ENC_OBJECT,
            NULL,
            $nameSpace,
            'Security',
            $nameSpace
        );

        $header = new \SoapHeader($nameSpace, 'Security', $security, true);
        $this->__setSoapHeaders(array($header));
    }

    /**
     * @return string The client's merchant ID.
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @return string The client's transaction key.
     */
    public function getTransactionKey()
    {
        return $this->transactionKey;
    }
}
