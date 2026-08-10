<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $student_code;
    public string $student_name;
    public string $message;

    public function __construct(string $studentCode, string $studentName, string $message)
    {
        $this->student_code = $studentCode;
        $this->student_name = $studentName;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('quiz-channel');
    }

    public function broadcastAs()
    {
        return 'student-submitted';
    }
}