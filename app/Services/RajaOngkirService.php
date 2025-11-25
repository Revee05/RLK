<?php
namespace App\Services;

use GuzzleHttp\Client;

class RajaOngkirService
{
    protected $client;

    public function __construct()
    {
        // >>> FIX PENTING <<< //
        $endpoint = rtrim(env('RAJAONGKIR_ENDPOINT', 'https://rajaongkir.komerce.id/api/v1'), '/');
        
        $this->client = new Client([
            'base_uri' => $endpoint . '/', // hasil: https://rajaongkir.komerce.id/api/v1/
        ]);
    }


    public function calculateCost($origin, $destination, $weight, $courier, $price = 'lowest')
    {
        \Log::info('PAYLOAD RAJAONGKIR:', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
            'price' => $price
        ]);
        $response = $this->client->post('calculate/district/domestic-cost', [
            'headers' => [
                'key'       => env('RAJAONGKIR_API_KEY'),
                'Accept'    => 'application/json'
            ],
            'multipart' => [
                [
                    'name' => 'origin',
                    'contents' => $origin
                ],
                [
                    'name' => 'destination',
                    'contents' => $destination
                ],
                [
                    'name' => 'weight',
                    'contents' => $weight
                ],
                [
                    'name' => 'courier',
                    'contents' => $courier
                ],
                [
                    'name' => 'price',
                    'contents' => $price
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

}
