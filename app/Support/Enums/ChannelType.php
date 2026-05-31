<?php

namespace App\Support\Enums;

enum ChannelType: string
{
    case Social = 'social';
    case Messaging = 'messaging';
    case Community = 'community';
    case Other = 'other';
}
