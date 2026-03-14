import React, { useState } from 'react';
import centralApi from '../../shared/api/centralApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import InputField from '../../shared/components/InputField';

var initialForm = {
    company_name: '',
    company_code: '',
    subdomain: '',
    legal_name: '',
    email: '',
    phone: '',
    website: '',
    industry: '',
    company_size: '',
    country: '',
    state: '',
    city: '',
    address_line1: '',
    address_line2: '',
    postal_code: '',
    admin_name: '',
    admin_email: '',
    admin_password: '',
};

export default function CompanyCreate() {
    var [form, setForm] = useState(initialForm);
    var [errors, setErrors] = useState({});
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(false);

    function onChange(event) {
        var target = event.target;
        setForm(function (prev) {
            var next = Object.assign({}, prev);
            next[target.name] = target.value;
            return next;
        });
    }

    function onSubmit(event) {
        event.preventDefault();
        setLoading(true);
        setErrors({});
        setMessage('');

        centralApi
            .createCompany(form)
            .then(function (response) {
                var company = response.data.data ? response.data.data.company : null;
                setMessage(response.data.message || 'Company created.');
                if (company && company.id) {
                    window.location.href = '/companies/' + company.id;
                }
            })
            .catch(function (error) {
                setMessage(error.message || 'Create failed.');
                setErrors(error.errors || {});
            })
            .finally(function () {
                setLoading(false);
            });
    }

    function errorFor(name) {
        return errors[name] ? errors[name][0] : null;
    }

    return (
        <div>
            <h3 className="page-title mb-1">Create Company</h3>
            <p className="page-subtitle mb-3">Create a tenant profile and onboarding request.</p>
            <AlertMessage type="error" message={message} />
            <Card>
                <form onSubmit={onSubmit}>
                    <div className="row">
                        <div className="col-md-6"><InputField label="Company Name" name="company_name" value={form.company_name} onChange={onChange} error={errorFor('company_name')} /></div>
                        <div className="col-md-6"><InputField label="Company Code" name="company_code" value={form.company_code} onChange={onChange} error={errorFor('company_code')} /></div>
                        <div className="col-md-6"><InputField label="Subdomain" name="subdomain" value={form.subdomain} onChange={onChange} error={errorFor('subdomain')} /></div>
                        <div className="col-md-6"><InputField label="Website" name="website" value={form.website} onChange={onChange} error={errorFor('website')} /></div>
                        <div className="col-md-6"><InputField label="Email" name="email" type="email" value={form.email} onChange={onChange} error={errorFor('email')} /></div>
                        <div className="col-md-6"><InputField label="Phone" name="phone" value={form.phone} onChange={onChange} error={errorFor('phone')} /></div>
                        <div className="col-md-6"><InputField label="Admin Name" name="admin_name" value={form.admin_name} onChange={onChange} error={errorFor('admin_name')} /></div>
                        <div className="col-md-6"><InputField label="Admin Email" name="admin_email" type="email" value={form.admin_email} onChange={onChange} error={errorFor('admin_email')} /></div>
                        <div className="col-md-6"><InputField label="Admin Password" name="admin_password" type="password" value={form.admin_password} onChange={onChange} error={errorFor('admin_password')} /></div>
                    </div>
                    <div className="d-flex gap-2 mt-2">
                        <Button type="submit" loading={loading}>Create Company</Button>
                        <a href="/companies" className="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </Card>
        </div>
    );
}
