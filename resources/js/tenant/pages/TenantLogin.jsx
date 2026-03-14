import React, { useState } from 'react';
import tenantApi from '../../shared/api/tenantApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import InputField from '../../shared/components/InputField';

export default function TenantLogin() {
    var [form, setForm] = useState({ email: '', password: '', remember: false });
    var [errors, setErrors] = useState({});
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(false);

    function onChange(event) {
        var target = event.target;
        setForm(function (prev) {
            var next = Object.assign({}, prev);
            next[target.name] = target.type === 'checkbox' ? target.checked : target.value;
            return next;
        });
    }

    function onSubmit(event) {
        event.preventDefault();
        setLoading(true);
        setErrors({});
        setMessage('');

        tenantApi
            .login(form)
            .then(function (response) {
                var redirectTo = response.data && response.data.data ? response.data.data.redirect_to : '/dashboard';
                window.location.href = redirectTo || '/dashboard';
            })
            .catch(function (error) {
                setMessage(error.message || 'Login failed.');
                setErrors(error.errors || {});
            })
            .finally(function () {
                setLoading(false);
            });
    }

    return (
        <div className="app-shell py-5">
            <div className="row justify-content-center">
                <div className="col-md-6 col-lg-5">
                    <Card title="Tenant Login">
                        <AlertMessage type="error" message={message} />
                        <form onSubmit={onSubmit}>
                            <InputField
                                label="Email"
                                name="email"
                                type="email"
                                value={form.email}
                                onChange={onChange}
                                error={errors.email ? errors.email[0] : null}
                                autoComplete="username"
                            />
                            <InputField
                                label="Password"
                                name="password"
                                type="password"
                                value={form.password}
                                onChange={onChange}
                                error={errors.password ? errors.password[0] : null}
                                autoComplete="current-password"
                            />
                            <div className="form-check mb-3">
                                <input className="form-check-input" type="checkbox" name="remember" id="rememberTenant" checked={form.remember} onChange={onChange} />
                                <label className="form-check-label" htmlFor="rememberTenant">
                                    Remember me
                                </label>
                            </div>
                            <Button type="submit" loading={loading} className="w-100">
                                Login
                            </Button>
                        </form>
                    </Card>
                </div>
            </div>
        </div>
    );
}
