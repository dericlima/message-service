<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array $service
 * @property string $message
 * @property string $code
 * @property array $payload
 */
class Tracker extends Model
{
    protected $table = 'tracker';

    protected function setPayloadAttribute(array $value)
    {
        $this->attributes['payload'] = json_encode($value);
    }

    protected function getPayloadAttribute($value)
    {
        return json_decode($value);
    }
}
