<?php

namespace App\Models\Central;

use App\Support\ProvisionStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class CompanyOnboardingRequest extends Model
{
    use CentralConnection;
    use HasFactory;

    protected $table = 'company_onboarding_requests';

    protected $fillable = [
        'company_id',
        'tenant_id',
        'requested_by',
        'request_payload',
        'provision_status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(EnterpriseAdmin::class, 'requested_by');
    }

    public function isCompleted(): bool
    {
        return $this->provision_status === ProvisionStatuses::COMPLETED;
    }
}
