<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentSubmission;
use Pusher\Pusher;

class ResetTreeCommand extends Command
{
    protected $signature = 'tree:clear';
    protected $description = 'Xóa sạch bông hoa trên Cây Tri Thức';

    public function handle()
    {
        // 1. Xóa dữ liệu trong DB
        StudentSubmission::truncate();

        // 2. Bắn Realtime phát tín hiệu xóa màn hình /live
        try {
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                ['cluster' => env('PUSHER_APP_CLUSTER'), 'useTLS' => true]
            );
            $pusher->trigger('quiz-channel', 'tree-reset', ['status' => 'cleared']);
        } catch (\Exception $e) {}

        $this->info('✅ Đã xóa sạch Cây Tri Thức thành công!');
    }
}