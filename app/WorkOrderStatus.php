<?php

namespace App;

enum WorkOrderStatus: string
{
    //
    case Cancelled = 'CANCELLED';
    case Finished = 'FINISHED';
    case Open = 'OPEN';
    case Running = 'RUNNING';


    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
