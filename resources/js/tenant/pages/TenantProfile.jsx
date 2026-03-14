import React, { useEffect, useState } from 'react';
import tenantApi from '../../shared/api/tenantApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import InputField from '../../shared/components/InputField';
import Loader from '../../shared/components/Loader';

export default function TenantProfile() {
    var [form, setForm] = useState(null);
    var [errors, setErrors] = useState({});
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(false);

    useEffect(function () {
        tenantApi
            .profile()
            .then(function (response) {
                var profile = response.data.data ? response.data.data.profile : null;
                setForm({
                    company_name: profile && profile.company_name ? profile.company_name : '',
                    legal_name: profile && profile.legal_name ? profile.legal_name : '',
                    email: profile && profile.email ? profile.email : '',
                    phone: profile && profile.phone ? profile.phone : '',
                    website: profile && profile.website ? profile.website : '',
                    industry: profile && profile.industry ? profile.industry : '',
                    company_size: profile && profile.company_size ? profile.company_size : '',
                    country: profile && profile.country ? profile.country : '',
                    state: profile && profile.state ? profile.state : '',
                    city: profile && profile.city ? profile.city : '',
                    address_line1: profile && profile.address_line1 ? profile.address_line1 : '',
                    address_line2: profile && profile.address_line2 ? profile.address_line2 : '',
                    postal_code: profile && profile.postal_code ? profile.postal_code : '',
                    logo_path: profile && profile.logo_path ? profile.logo_path : '',
                    timezone: profile && profile.timezone ? profile.timezone : 'UTC',
                    currency: profile && profile.currency ? profile.currency : 'USD',
                });
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load profile.');
            });
    }, []);

    function onChange(event) {
        var target = event.target;
        setForm(function (prev) {
            var next = Object.assign({}, prev);
            next[target.name] = target.value;
            return next;
        });
    }

    function errorFor(name) {
        return errors[name] ? errors[name][0] : null;
    }

    function onSubmit(event) {
        event.preventDefault();
        setLoading(true);
        setErrors({});

        tenantApi
            .updateProfile(form)
            .then(function (response) {
                setMessage(response.data.message || 'Profile updated.');
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to update profile.');
                setErrors(error.errors || {});
            })
            .finally(function () {
                setLoading(false);
            });
    }

    if (!form) {
        return <Loader label="Loading company profile..." />;
    }

    return (
        <div>
            <h3 className="page-title mb-1">Company Profile</h3>
            <p className="page-subtitle mb-3">Maintain tenant company details.</p>
            <AlertMessage type={message && message.toLowerCase().indexOf('unable') > -1 ? 'error' : 'success'} message={message} />
            <Card>
                <form onSubmit={onSubmit}>
                    <div className="row">
                        <div className="col-md-6"><InputField label="Company Name" name="company_name" value={form.company_name} onChange={onChange} error={errorFor('company_name')} /></div>
                        <div className="col-md-6"><InputField label="Legal Name" name="legal_name" value={form.legal_name} onChange={onChange} error={errorFor('legal_name')} /></div>
                        <div className="col-md-6"><InputField label="Email" name="email" type="email" value={form.email} onChange={onChange} error={errorFor('email')} /></div>
                        <div className="col-md-6"><InputField label="Phone" name="phone" value={form.phone} onChange={onChange} error={errorFor('phone')} /></div>
                        <div className="col-md-6"><InputField label="Website" name="website" value={form.website} onChange={onChange} error={errorFor('website')} /></div>
                        <div className="col-md-6"><InputField label="Industry" name="industry" value={form.industry} onChange={onChange} error={errorFor('industry')} /></div>
                        <div className="col-md-6"><InputField label="Country" name="country" value={form.country} onChange={onChange} error={errorFor('country')} /></div>
                        <div className="col-md-3"><InputField label="Timezone" name="timezone" value={form.timezone} onChange={onChange} error={errorFor('timezone')} /></div>
                        <div className="col-md-3"><InputField label="Currency" name="currency" value={form.currency} onChange={onChange} error={errorFor('currency')} /></div>
                    </div>
                    <div className="d-flex gap-2 mt-2">
                        <Button type="submit" loading={loading}>Save Profile</Button>
                    </div>
                </form>
            </Card>
        </div>
    );
}
