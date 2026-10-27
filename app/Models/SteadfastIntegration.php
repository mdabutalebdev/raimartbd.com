<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SteadfastIntegration extends Model
{
    protected $fillable = ['api_key', 'secret_key', 'base_url', 'webhook_token', 'is_active'];
}
