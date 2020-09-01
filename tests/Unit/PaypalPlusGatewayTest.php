<?php

namespace Shoperti\Tests\PayMe\Unit;

use Shoperti\PayMe\Gateways\PaypalPlus\PaypalPlusGateway;

class PaypalPlusGatewayTest extends AbstractTestCase
{
    protected $gatewayData = [
        'class'                  => PaypalPlusGateway::class,
        'config'                 => 'paypal_plus',
        'innerMethod'            => 'mapResponse',
        'innerMethodExtraParams' => [200],
    ];

    /**
     * @test
     * charges()->complete()
     */
    public function it_should_parse_an_approved_payment()
    {
        $this->approvedPaymentTest($this->getApprovedPayment());
    }

    /** @test */
    public function it_should_parse_a_denied_payment()
    {
        $this->declinedPaymentTest($this->getDeniedPayment(), '');
    }

    /** @test */
    public function it_should_parse_a_pending_payment()
    {
        $this->pendingPaymentTest($this->getPendingPayment());
    }

    /** @test */
    public function it_should_parse_a_refunded_payment()
    {
        $this->approvedRefundTest($this->getRefundedPayment(), 'Transaction approved');
    }

    /** @test */
    public function it_should_parse_a_partially_refunded_payment()
    {
        $this->approvedPartialRefundTest($this->getPartiallyRefundedPayment());
    }

    /**
     * @see https://developer.paypal.com/docs/api/payments/v1/#payment_execute
     * @see https://developer.paypal.com/docs/api/payments/v1/#definition-sale
     */
    private function getApprovedPayment()
    {
        return [
            'id'           => 'PAY-4D905294SK041703DLH32GIA',
            'intent'       => 'sale',
            'state'        => 'approved',                   // ignore
            'cart'         => '755335510M315821L',
            'transactions' => [
                [
                    'related_resources' => [
                        [
                            'sale' => [
                                'id'     => '60016985HM514502U',
                                'state'  => 'completed',            // the one we care about
                                'amount' => [
                                    'total'    => '522.00',
                                    'currency' => 'MXN',
                                    'details'  => [
                                        'subtotal' => '522.00',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getPendingPayment()
    {
        $payload = $this->getApprovedPayment();
        $payload['transactions'][0]['related_resources'][0]['sale']['state'] = 'pending';

        return $payload;
    }

    private function getDeniedPayment()
    {
        $payload = $this->getApprovedPayment();
        $payload['transactions'][0]['related_resources'][0]['sale']['state'] = 'denied';

        return $payload;
    }

    private function getPartiallyRefundedPayment()
    {
        $payload = $this->getApprovedPayment();
        $payload['transactions'][0]['related_resources'][0]['sale']['state'] = 'partially_refunded';

        return $payload;
    }

    private function getRefundedPayment()
    {
        return [
            'id'     => 'PAYID-L4K6P3I34X98481BT0350124',
            'intent' => 'sale',
            'state'  => 'approved',
            'cart'   => '9J863377TH1110837',
            'payer'  => [
                'payment_method' => 'paypal',
                'status'         => 'UNVERIFIED',
                'payer_info'     => [
                    'email'            => 'apriego@rgtecsupport.com',
                    'first_name'       => 'Luis antonio',
                    'last_name'        => 'Priego muniz',
                    'payer_id'         => '833FF4L3AFTK8',
                    'shipping_address' => [
                        'recipient_name' => 'luis priego',
                        'line1'          => 'av costera de las palmas 2810',
                        'line2'          => 'departamento 3',
                        'city'           => 'acapulco de juarez',
                        'state'          => 'Guerrero',
                        'postal_code'    => '39897',
                        'country_code'   => 'MX',
                    ],
                    'phone'        => '7442956737',
                    'country_code' => 'MX',
                ],
            ],
            'transactions' => [
                [
                    'amount' => [
                        'total'    => '14920.00',
                        'currency' => 'MXN',
                        'details'  => [
                            'subtotal'          => '14920.00',
                            'shipping'          => '0.00',
                            'insurance'         => '0.00',
                            'handling_fee'      => '0.00',
                            'shipping_discount' => '0.00',
                        ],
                    ],
                    'payee' => [
                        'merchant_id' => 'W7TLFNKUJEM66',
                        'email'       => 'andres@cole.mx',
                    ],
                    'description'     => 'True Religion',
                    'invoice_number'  => 'ord_ckcuv3md0000012s1npx5w3rd',
                    'soft_descriptor' => 'PAYPAL *COLECOLLECT',
                    'item_list'       => [
                        'items' => [
                            [
                                'name'        => 'x1 RICKY SUPER T STRAIGHT JEAN GHSD-NO-SMOKE / 34',
                                'sku'         => '102990_GHSD_34',
                                'description' => 'RICKY SUPER T STRAIGHT JEAN GHSD-NO-SMOKE / 34',
                                'price'       => '4000.00',
                                'currency'    => 'MXN',
                                'tax'         => '0.00',
                                'quantity'    => 1,
                            ],
                        ],
                        'shipping_address' => [
                            'recipient_name' => 'luis priego',
                            'line1'          => 'av costera de las palmas 2810',
                            'line2'          => 'departamento 3',
                            'city'           => 'acapulco de juarez',
                            'state'          => 'Guerrero',
                            'postal_code'    => '39897',
                            'country_code'   => 'MX',
                        ],
                    ],
                    'related_resources' => [
                        [
                            'sale' => [
                                'id'     => '03P43844491050920',
                                'state'  => 'refunded',
                                'amount' => [
                                    'total'    => '14920.00',
                                    'currency' => 'MXN',
                                    'details'  => [
                                        'subtotal'          => '14920.00',
                                        'shipping'          => '0.00',
                                        'insurance'         => '0.00',
                                        'handling_fee'      => '0.00',
                                        'shipping_discount' => '0.00',
                                    ],
                                ],
                                'payment_mode'           => 'INSTANT_TRANSFER',
                                'protection_eligibility' => 'INELIGIBLE',
                                'transaction_fee'        => [
                                    'value'    => '2851.38',
                                    'currency' => 'MXN',
                                ],
                                'receipt_id'     => '5280176468350218',
                                'parent_payment' => 'PAYID-L4K6P3I34X98481BT0350124',
                                'create_time'    => '2020-07-20T18:53:01Z',
                                'update_time'    => '2020-07-31T01:07:40Z',
                                'links'          => [
                                    [
                                        'href'   => 'https://api.paypal.com/v1/payments/sale/03P43844491050920',
                                        'rel'    => 'self',
                                        'method' => 'GET',
                                    ],
                                    [
                                        'href'   => 'https://api.paypal.com/v1/payments/sale/03P43844491050920/refund',
                                        'rel'    => 'refund',
                                        'method' => 'POST',
                                    ],
                                    [
                                        'href'   => 'https://api.paypal.com/v1/payments/payment/PAYID-L4K6P3I34X98481BT0350124',
                                        'rel'    => 'parent_payment',
                                        'method' => 'GET',
                                    ],
                                ],
                                'soft_descriptor' => 'PAYPAL *COLECOLLECT',
                            ],
                        ],
                        [
                            'refund' => [
                                'id'     => '634467273V4268942',
                                'state'  => 'completed',
                                'amount' => [
                                    'total'    => '-14920.00',
                                    'currency' => 'MXN',
                                ],
                                'parent_payment' => 'PAYID-L4K6P3I34X98481BT0350124',
                                'sale_id'        => '03P43844491050920',
                                'create_time'    => '2020-07-21T10:31:01Z',
                                'update_time'    => '2020-07-21T10:31:01Z',
                                'links'          => [
                                    [
                                        'href'   => 'https://api.paypal.com/v1/payments/refund/634467273V4268942',
                                        'rel'    => 'self',
                                        'method' => 'GET',
                                    ],
                                    [
                                        'href'   => 'https://api.paypal.com/v1/payments/payment/PAYID-L4K6P3I34X98481BT0350124',
                                        'rel'    => 'parent_payment',
                                        'method' => 'GET',
                                    ],
                                    [
                                        'href'   => 'https://api.paypal.com/v1/payments/sale/03P43844491050920',
                                        'rel'    => 'sale',
                                        'method' => 'GET',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'create_time' => '2020-07-20T18:52:29Z',
            'update_time' => '2020-07-31T01:07:40Z',
            'links'       => [
                [
                    'href'   => 'https://api.paypal.com/v1/payments/payment/PAYID-L4K6P3I34X98481BT0350124',
                    'rel'    => 'self',
                    'method' => 'GET',
                ],
            ],
        ];
    }
}
