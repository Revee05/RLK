<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Province;
use App\City;
use App\District;

class SyncRajaOngkir extends Command
{
    protected $signature = 'rajaongkir:sync';
    protected $description = 'Sync province, city, and district from RajaOngkir API';

    public function handle()
    {
        $this->info("Starting RajaOngkir sync...");

        $client = new Client();
        $base = "https://rajaongkir.komerce.id/api/v1/destination";
        $key = env('RAJAONGKIR_API_KEY');

        // ============================================================
        // 1. SYNC PROVINCE
        // ============================================================
        $this->info("Fetching provinces...");

        $response = $client->get("$base/province", [
            'headers' => ['key' => $key]
        ]);

        $provinces = json_decode($response->getBody(), true)['data'];

        foreach ($provinces as $p) {

            if (Province::where('id', $p['id'])->exists()) {
                $this->line("✓ Skip province {$p['name']} (already synced)");
                continue;
            }

            Province::create([
                'id' => $p['id'],
                'name' => $p['name']
            ]);

            $this->info("→ Province synced: {$p['name']}");

            sleep(1); // aman limit
        }

        // ============================================================
        // 2. SYNC CITY FOR EACH PROVINCE
        // ============================================================
        $this->info("Fetching cities...");

        $allProvinces = Province::all();

        foreach ($allProvinces as $prov) {

            $this->info("→ Province: {$prov->name}");

            $response = $client->get("$base/city/{$prov->id}", [
                'headers' => ['key' => $key]
            ]);

            $cities = json_decode($response->getBody(), true)['data'];

            foreach ($cities as $c) {

                if (City::where('id', $c['id'])->exists()) {
                    $this->line("  ✓ Skip city {$c['name']} (already synced)");
                    continue;
                }

                City::create([
                    'id' => $c['id'],
                    'province_id' => $prov->id,
                    'name' => $c['name']
                ]);

                $this->info("  → City synced: {$c['name']}");
                sleep(1);
            }
        }

        // ============================================================
        // 3. SYNC DISTRICT PER CITY
        // ============================================================
        $this->info("Fetching districts...");

        $allCities = City::all();

        foreach ($allCities as $city) {

            $this->info("→ City: {$city->name}");

            try {
                $response = $client->get("$base/district/{$city->id}", [
                    'headers' => ['key' => $key]
                ]);
            } catch (\Exception $e) {
                $this->warn("⚠ Error fetching district for city {$city->name}");
                continue;
            }

            $body = json_decode($response->getBody(), true);

            if (
                !isset($body['data']) ||
                empty($body['data']) ||
                !is_array($body['data'])
            ) {
                $this->warn("⚠ Tidak ada district untuk city {$city->name}");
                continue;
            }

            foreach ($body['data'] as $d) {

                if (District::where('id', $d['id'])->exists()) {
                    $this->line("  ✓ Skip district {$d['name']} (already synced)");
                    continue;
                }

                District::create([
                    'id' => $d['id'],
                    'city_id' => $city->id,
                    'name' => $d['name']
                ]);

                $this->info("  → District synced: {$d['name']}");
                sleep(1);
            }

        }

        $this->info("✔ RajaOngkir Sync Completed (incremental)!");
    }
}
