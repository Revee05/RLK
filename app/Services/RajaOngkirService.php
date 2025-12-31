<?php
namespace App\Services;

use GuzzleHttp\Client;

class RajaOngkirService
{
    protected $client;

    public function __construct()
    {
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

        try{
            $response = $this->client->post('calculate/district/domestic-cost', [
                'headers' => [
                    'key'       => env('RAJAONGKIR_API_KEY'),
                    'Accept'    => 'application/json'
                ],
                'form_params' => [
                    'origin' => (int) $origin,
                    'destination' => (int) $destination,
                    'weight' => (int) $weight,
                    'courier' => $courier,
                    'price' => $price,
                ],
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);

            \Log::info('RAJAONGKIR RESPONSE', $data);

            return [
                'data' => collect($data['data'] ?? [])
                    ->map(function ($item) {
                        return [
                            'service' => $item['service'],
                            'cost'    => $item['cost'],
                            'etd'     => $item['etd'] ?? '-',
                        ];
                    })
                    ->toArray()
            ];

        } catch (\Throwable $e) {

            \Log::error('RAJAONGKIR SERVICE ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return [];
        }

        return json_decode($response->getBody(), true);
    }

}
