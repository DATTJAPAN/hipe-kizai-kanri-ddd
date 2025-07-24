<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'domain',
        'business_email',
    ];

    protected $casts = [
        'alt_domains' => 'array',
    ];
}
