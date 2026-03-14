import React, { useState } from 'react';
import tenantApi from '../../shared/api/tenantApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import InputField from '../../shared/components/InputField';
import SelectField from '../../shared/components/SelectField';

var initialForm = {
    name: '',
    email: '',
    password: '',
    role: '',
    status: 'active',
    phone: '',
};

export default function UserCreate() {
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

    function errorFor(name) {
        return errors[name] ? errors[name][0] : null;
    }

    function onSubmit(event) {
        event.preventDefault();
        setLoading(true);
        setErrors({});

        tenantApi
            .createUser(form)
            .then(function (response) {
                setMessage(response.data.message || 'User created.');
                window.location.href = '/users';
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to create user.');
                setErrors(error.errors || {});
            })
            .finally(function () {
                setLoading(false);
            });
    }

    return (
        <div>
            <h3 className="page-title mb-1">Create User</h3>
            <p className="page-subtitle mb-3">Add a new tenant user or admin.</p>
            <AlertMessage type="error" message={message} />
            <Card>
                <form onSubmit={onSubmit}>
                    <div className="row">
                        <div className="col-md-6"><InputField label="Name" name="name" value={form.name} onChange={onChange} error={errorFor('name')} /></div>
                        <div className="col-md-6"><InputField label="Email" name="email" type="email" value={form.email} onChange={onChange} error={errorFor('email')} /></div>
                        <div className="col-md-6"><InputField label="Password" name="password" type="password" value={form.password} onChange={onChange} error={errorFor('password')} /></div>
                        <div className="col-md-3">
                            <SelectField
                                label="Role"
                                name="role"
                                value={form.role}
                                onChange={onChange}
                                error={errorFor('role')}
                                options={[
                                    { label: 'Company Admin', value: 'company_admin' },
                                    { label: 'Company User', value: 'company_user' },
                                ]}
                            />
                        </div>
                        <div className="col-md-3">
                            <SelectField
                                label="Status"
                                name="status"
                                value={form.status}
                                onChange={onChange}
                                error={errorFor('status')}
                                options={[
                                    { label: 'Active', value: 'active' },
                                    { label: 'Inactive', value: 'inactive' },
                                ]}
                            />
                        </div>
                        <div className="col-md-6"><InputField label="Phone" name="phone" value={form.phone} onChange={onChange} error={errorFor('phone')} /></div>
                    </div>
                    <div className="d-flex gap-2 mt-2">
                        <Button type="submit" loading={loading}>Create User</Button>
                        <a href="/users" className="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </Card>
        </div>
    );
}
