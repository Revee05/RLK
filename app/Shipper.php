<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    protected $table = 'shipper';
    protected $fillable = [
        'name',
    ];

    /*public function calculateCost($origin, $destination, $weight = 1000)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api-sandbox.collaborator.komerce.id/tariff/api/v2/calculate', [
                'headers' => ['key' => env('RAJAONGKIR_API_KEY')],
                'form_params' => [
                    'origin' => $origin['kabupaten_id'],
                    'destination' => $destination['kabupaten_id'],
                    'weight' => $weight,
                    'courier' => strtolower($this->name)
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            $results = $data['rajaongkir']['results'][0] ?? null;

            if (!$results || empty($results['costs'])) {
                // kurir tidak punya ongkir / error
                return ['price' => 0, 'eta' => '-'];
            }

            // Ambil layanan pertama
            $firstCost = $results['costs'][0]['cost'][0] ?? null;

            if (!$firstCost) {
                return ['price' => 0, 'eta' => '-'];
            }

            $cost = $firstCost['value'] ?? 0;
            $eta  = $firstCost['etd']   ?? '-';

            return [
                'price' => (int)$cost,
                'eta'   => $eta !== '' ? $eta : '-'  // jadikan '-' kalau kosong
            ];

        } catch (\Exception $e) {

            \Log::error("RajaOngkir error ({$this->name}): " . $e->getMessage());

            // fallback → shipping = 0
            return ['price' => 0, 'eta' => '-'];
        }
    }
    */
}
