import React, { useEffect, useState } from 'react';
import centralApi from '../../shared/api/centralApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Card from '../../shared/components/Card';
import Loader from '../../shared/components/Loader';

export default function EnterpriseDashboard() {
    var [stats, setStats] = useState(null);
    var [message, setMessage] = useState('');

    useEffect(function () {
        centralApi
            .dashboard()
            .then(function (response) {
                setStats(response.data.data || {});
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load dashboard stats.');
            });
    }, []);

    if (!stats && !message) {
        return <Loader label="Loading dashboard..." />;
    }

    return (
        <div>
            <h3 className="page-title mb-2">Enterprise Dashboard</h3>
            <p className="page-subtitle mb-4">Monitor tenant activity and provisioning progress.</p>
            <AlertMessage type="error" message={message} />
            <div className="row g-3">
                <div className="col-md-3">
                    <Card title="Total Companies">
                        <h2 className="mb-0">{stats ? stats.total_companies : 0}</h2>
                    </Card>
                </div>
                <div className="col-md-3">
                    <Card title="Active">
                        <h2 className="mb-0 text-success">{stats ? stats.active_companies : 0}</h2>
                    </Card>
                </div>
                <div className="col-md-3">
                    <Card title="Provisioning">
                        <h2 className="mb-0 text-warning">{stats ? stats.provisioning_companies : 0}</h2>
                    </Card>
                </div>
                <div className="col-md-3">
                    <Card title="Suspended">
                        <h2 className="mb-0 text-danger">{stats ? stats.suspended_companies : 0}</h2>
                    </Card>
                </div>
            </div>
        </div>
    );
}
