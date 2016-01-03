<?php

namespace Stichoza\NbgCurrency;

use Carbon\Carbon;
use Exception;
use SoapClient;

/**
 * NBG currency service wrapper class
 *
 * @author      Levan Velijanashvili <me@stichoza.com>
 * @link        http://stichoza.com/
 * @license     MIT
 */
class NbgCurrency
{
    /**
     * @var SoapClient
     */
    private static $client;

    /**
     * @var string WSDL address
     */
    private static $wsdl = 'http://nbg.gov.ge/currency.wsdl';

    /**
     * @var array List of all supported currencies
     */
    private static $supportedCurrencies = [
        'AED', 'AMD', 'AUD', 'AZN', 'BGN', 'BYR', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK',
        'EEK', 'EGP', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'INR', 'IRR', 'ISK', 'JPY',
        'KGS', 'KWD', 'KZT', 'LTL', 'LVL', 'MDL', 'NOK', 'NZD', 'PLN', 'RON', 'RSD',
        'RUB', 'SEK', 'SGD', 'TJS', 'TMT', 'TRY', 'UAH', 'USD', 'UZS',
    ];
    
    private static $methodMap = [
        'change' => 'GetCurrencyRate',
        'diff'   => 'GetCurrencyChange',
        'rate'   => 'GetCurrency',
        'text'   => 'GetCurrencyDescription',
    ];

    private static function checkClient() {
        if ( ! isset(self::$client)) {
            self::$client = new SoapClient(self::$wsdl);
        }
    }

    public static function currencyIsSupported($currency)
    {
        return in_array(strtoupper($currency), self::$supportedCurrencies);
    }

    public static function __callStatic($name, $args) {
        // Check client
        self::checkClient();

        // Date is a cool guy
        if ($name == 'date') {
            return Carbon::parse(self::$client->GetDate());
        }

        if (in_array($name, array_keys(self::$methodMap))) {
            $method = self::$methodMap[$name];
            print 'Calling ' . $method . ' with param ' . strtoupper($args[0]) . PHP_EOL;
            return self::$client->$method(strtoupper($args[0]));
        }

        foreach (self::$methodMap as $method => $soapMethod) {
            if (preg_match('/^' . $method . '/', $name)) {
                return self::$method(strtoupper($args[0]));
            }
        }
        
    }

}
