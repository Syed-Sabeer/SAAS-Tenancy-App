<?php

namespace App\Models\Central;

use App\Support\EnterpriseAdminStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class EnterpriseAdmin extends Authenticatable
{
    use CentralConnection;
    use HasFactory;
    use Notifiable;

    protected $table = 'enterprise_admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->status === EnterpriseAdminStatuses::ACTIVE;
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'onboarded_by');
    }

    public function onboardingRequests(): HasMany
    {
        return $this->hasMany(CompanyOnboardingRequest::class, 'requested_by');
    }
}
