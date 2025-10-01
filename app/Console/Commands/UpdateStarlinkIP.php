<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\allowed_networks;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateStarlinkIP extends Command
{
    protected $signature = 'starlink:update-ip';
    protected $description = 'Fetch current Starlink public IP and update allowed_networks table';

    public function handle()
    {
        try {
            // Get public IP using external API
            $response = Http::get('https://api.ipify.org?format=json');

            if (!$response->successful()) {
                $this->error('Failed to fetch public IP');
                Log::error('[Starlink Update] Failed to fetch public IP');
                return 1;
            }

            $publicIp = $response->json()['ip'];

            // Update or insert into allowed_networks
            $allowedNetworks = allowed_networks::findOrFail(1);
            $allowedNetworks->ip_address = $publicIp . '/24';
            $allowedNetworks->is_active = true;
            $allowedNetworks->save();


            $message = "[Starlink Update] Public IP updated: {$publicIp} at " . now();
            $this->info($message);
            Log::info($message);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('[Starlink Update] Error: ' . $e->getMessage());
            return 1;
        }
    }
}
