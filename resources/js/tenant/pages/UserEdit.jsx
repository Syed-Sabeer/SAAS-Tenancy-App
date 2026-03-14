import React, { useEffect, useState } from 'react';
import tenantApi from '../../shared/api/tenantApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import InputField from '../../shared/components/InputField';
import Loader from '../../shared/components/Loader';
import SelectField from '../../shared/components/SelectField';

export default function UserEdit(props) {
    var [form, setForm] = useState(null);
    var [errors, setErrors] = useState({});
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(false);

    useEffect(function () {
        tenantApi
            .getUserEdit(props.userId)
            .then(function (response) {
                var user = response.data.data ? response.data.data.user : null;
                if (user) {
                    setForm({
                        name: user.name || '',
                        email: user.email || '',
                        password: '',
                        role: user.role || '',
                        status: user.status || 'active',
                        phone: user.phone || '',
                    });
                }
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load user.');
            });
    }, [props.userId]);

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
            .updateUser(props.userId, form)
            .then(function (response) {
                setMessage(response.data.message || 'User updated.');
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to update user.');
                setErrors(error.errors || {});
            })
            .finally(function () {
                setLoading(false);
            });
    }

    if (!form) {
        return <Loader label="Loading user..." />;
    }

    return (
        <div>
            <h3 className="page-title mb-1">Edit User</h3>
            <p className="page-subtitle mb-3">Update tenant user access and details.</p>
            <AlertMessage type={message && message.toLowerCase().indexOf('unable') > -1 ? 'error' : 'success'} message={message} />
            <Card>
                <form onSubmit={onSubmit}>
                    <div className="row">
                        <div className="col-md-6"><InputField label="Name" name="name" value={form.name} onChange={onChange} error={errorFor('name')} /></div>
                        <div className="col-md-6"><InputField label="Email" name="email" type="email" value={form.email} onChange={onChange} error={errorFor('email')} /></div>
                        <div className="col-md-6"><InputField label="New Password (optional)" name="password" type="password" value={form.password} onChange={onChange} error={errorFor('password')} /></div>
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
                        <Button type="submit" loading={loading}>Save Changes</Button>
                        <a href="/users" className="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </Card>
        </div>
    );
}
