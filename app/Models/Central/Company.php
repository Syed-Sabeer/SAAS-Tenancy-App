<?php

namespace App\Models\Central;

use App\Support\CompanyStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Company extends Model
{
    use CentralConnection;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'tenant_id',
        'company_code',
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
        'subdomain',
        'status',
        'onboarded_by',
        'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];

    public function onboardedBy(): BelongsTo
    {
        return $this->belongsTo(EnterpriseAdmin::class, 'onboarded_by');
    }

    public function onboardingRequest(): HasOne
    {
        return $this->hasOne(CompanyOnboardingRequest::class);
    }

    public function provisioningLogs(): HasMany
    {
        return $this->hasMany(ProvisioningLog::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'company_subscriptions')
            ->withPivot(['status', 'starts_at', 'ends_at', 'meta'])
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->status === CompanyStatuses::ACTIVE;
    }
}
