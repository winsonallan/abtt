<?php

namespace App;

enum WorkOrderStatus: string
{
    //
    case Running = 'RUNNING';
    case Finished = 'FINISHED';
    case Open = 'OPEN';
    case Cancelled = 'CANCELLED';


    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
