import React, { useEffect, useState } from 'react';
import centralApi from '../../shared/api/centralApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import InputField from '../../shared/components/InputField';
import Loader from '../../shared/components/Loader';

export default function CompanyEdit(props) {
    var [form, setForm] = useState(null);
    var [errors, setErrors] = useState({});
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(false);

    useEffect(function () {
        centralApi
            .getCompanyEdit(props.companyId)
            .then(function (response) {
                var company = response.data.data ? response.data.data.company : null;
                if (company) {
                    setForm({
                        company_name: company.company_name || '',
                        company_code: company.company_code || '',
                        subdomain: company.subdomain || '',
                        legal_name: company.legal_name || '',
                        email: company.email || '',
                        phone: company.phone || '',
                        website: company.website || '',
                        industry: company.industry || '',
                        company_size: company.company_size || '',
                        country: company.country || '',
                        state: company.state || '',
                        city: company.city || '',
                        address_line1: company.address_line1 || '',
                        address_line2: company.address_line2 || '',
                        postal_code: company.postal_code || '',
                        admin_name: '',
                        admin_email: '',
                        admin_password: '',
                    });
                }
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load company.');
            });
    }, [props.companyId]);

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

        centralApi
            .updateCompany(props.companyId, form)
            .then(function (response) {
                setMessage(response.data.message || 'Company updated.');
            })
            .catch(function (error) {
                setMessage(error.message || 'Update failed.');
                setErrors(error.errors || {});
            })
            .finally(function () {
                setLoading(false);
            });
    }

    if (!form) {
        return <Loader label="Loading company..." />;
    }

    return (
        <div>
            <h3 className="page-title mb-1">Edit Company</h3>
            <p className="page-subtitle mb-3">Update company and onboarding details.</p>
            <AlertMessage type={message && message.toLowerCase().indexOf('failed') > -1 ? 'error' : 'success'} message={message} />
            <Card>
                <form onSubmit={onSubmit}>
                    <div className="row">
                        <div className="col-md-6"><InputField label="Company Name" name="company_name" value={form.company_name} onChange={onChange} error={errorFor('company_name')} /></div>
                        <div className="col-md-6"><InputField label="Company Code" name="company_code" value={form.company_code} onChange={onChange} error={errorFor('company_code')} /></div>
                        <div className="col-md-6"><InputField label="Subdomain" name="subdomain" value={form.subdomain} onChange={onChange} error={errorFor('subdomain')} /></div>
                        <div className="col-md-6"><InputField label="Website" name="website" value={form.website} onChange={onChange} error={errorFor('website')} /></div>
                        <div className="col-md-6"><InputField label="Email" name="email" type="email" value={form.email} onChange={onChange} error={errorFor('email')} /></div>
                        <div className="col-md-6"><InputField label="Phone" name="phone" value={form.phone} onChange={onChange} error={errorFor('phone')} /></div>
                        <div className="col-md-6"><InputField label="Admin Name (optional)" name="admin_name" value={form.admin_name} onChange={onChange} error={errorFor('admin_name')} /></div>
                        <div className="col-md-6"><InputField label="Admin Email (optional)" name="admin_email" type="email" value={form.admin_email} onChange={onChange} error={errorFor('admin_email')} /></div>
                        <div className="col-md-6"><InputField label="Admin Password (optional)" name="admin_password" type="password" value={form.admin_password} onChange={onChange} error={errorFor('admin_password')} /></div>
                    </div>
                    <div className="d-flex gap-2 mt-2">
                        <Button type="submit" loading={loading}>Save Changes</Button>
                        <a href={'/companies/' + props.companyId} className="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </Card>
        </div>
    );
}
