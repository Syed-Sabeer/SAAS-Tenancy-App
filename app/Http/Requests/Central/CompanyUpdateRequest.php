<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return auth('enterprise')->check();
    }

    public function rules()
    {
        $companyId = $this->route('company') ? $this->route('company')->id : null;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_code' => ['required', 'string', 'max:100', Rule::unique('companies', 'company_code')->ignore($companyId)],
            'subdomain' => ['required', 'alpha_dash', 'max:100', Rule::unique('companies', 'subdomain')->ignore($companyId)],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:100'],
            'company_size' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['nullable', 'email', 'max:255'],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
