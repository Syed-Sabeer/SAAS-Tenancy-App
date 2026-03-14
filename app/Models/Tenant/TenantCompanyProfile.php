<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantCompanyProfile extends Model
{
    use HasFactory;

    protected $table = 'tenant_company_profiles';

    protected $fillable = [
        'central_company_id',
        'tenant_id',
        'company_name',
        'legal_name',
        'email',
        'phone',
        'website',
        'industry',
        'company_size',
        'country',
        'state',
        'city',
        'address_line1',
        'address_line2',
        'postal_code',
        'logo_path',
        'timezone',
        'currency',
    ];
}
