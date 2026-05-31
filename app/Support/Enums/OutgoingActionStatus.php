<?php

namespace App\Support\Enums;

enum OutgoingActionStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case RetryPending = 'retry_pending';
}
