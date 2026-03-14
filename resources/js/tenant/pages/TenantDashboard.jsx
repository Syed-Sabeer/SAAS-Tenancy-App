import React, { useEffect, useState } from 'react';
import tenantApi from '../../shared/api/tenantApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Card from '../../shared/components/Card';
import Loader from '../../shared/components/Loader';

export default function TenantDashboard() {
    var [data, setData] = useState(null);
    var [message, setMessage] = useState('');

    useEffect(function () {
        tenantApi
            .dashboard()
            .then(function (response) {
                setData(response.data.data || null);
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load dashboard.');
            });
    }, []);

    if (!data && !message) {
        return <Loader label="Loading tenant dashboard..." />;
    }

    return (
        <div>
            <h3 className="page-title mb-1">Tenant Dashboard</h3>
            <p className="page-subtitle mb-3">Welcome back, manage your users and profile.</p>
            <AlertMessage type="error" message={message} />
            {data ? (
                <div className="row g-3">
                    <div className="col-md-4">
                        <Card title="Logged in User">
                            <p className="mb-1"><strong>Name:</strong> {data.user ? data.user.name : 'N/A'}</p>
                            <p className="mb-1"><strong>Email:</strong> {data.user ? data.user.email : 'N/A'}</p>
                            <p className="mb-0"><strong>Role:</strong> {data.user ? data.user.role : 'N/A'}</p>
                        </Card>
                    </div>
                    <div className="col-md-4">
                        <Card title="User Statistics">
                            <h4 className="mb-1">{data.stats ? data.stats.total_users : 0}</h4>
                            <div className="text-muted">Total users</div>
                            <h5 className="mt-3 mb-1 text-success">{data.stats ? data.stats.active_users : 0}</h5>
                            <div className="text-muted">Active users</div>
                        </Card>
                    </div>
                    <div className="col-md-4">
                        <Card title="Company">
                            <p className="mb-1"><strong>Name:</strong> {data.company_profile ? data.company_profile.company_name : 'Not set'}</p>
                            <p className="mb-0"><strong>Tenant ID:</strong> {data.tenant_id || 'N/A'}</p>
                        </Card>
                    </div>
                </div>
            ) : null}
        </div>
    );
}
