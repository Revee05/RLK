<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\UserBannerNotification;

class CleanOldNotifications extends Command
{
    protected $signature = 'notif:cleanup';
    protected $description = 'Hapus notif popup yang sudah dibaca > 7 hari (berdasarkan updated_at)';

    public function handle()
    {
        $this->info('Membersihkan notif lama...');

        $deleted = UserBannerNotification::where('is_read', 1)
            ->where('updated_at', '<', Carbon::now()->subDays(7))
            ->delete();

        $this->info("✅ $deleted notif lama dihapus.");
    }
}
