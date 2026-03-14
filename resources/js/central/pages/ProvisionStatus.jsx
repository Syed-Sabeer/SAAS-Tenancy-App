import React, { useEffect, useState } from 'react';
import centralApi from '../../shared/api/centralApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import Loader from '../../shared/components/Loader';
import StatusBadge from '../../shared/components/StatusBadge';
import Table from '../../shared/components/Table';

export default function ProvisionStatus(props) {
    var [data, setData] = useState(null);
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(true);

    function load() {
        setLoading(true);
        centralApi
            .provisionStatus(props.companyId)
            .then(function (response) {
                setData(response.data.data || null);
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load provisioning status.');
            })
            .finally(function () {
                setLoading(false);
            });
    }

    useEffect(function () {
        load();
    }, [props.companyId]);

    function triggerProvision() {
        centralApi
            .provisionCompany(props.companyId)
            .then(function (response) {
                setMessage(response.data.message || 'Provisioning triggered.');
                load();
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to trigger provisioning.');
            });
    }

    if (loading) {
        return <Loader label="Loading provisioning status..." />;
    }

    var logs = data && data.logs ? data.logs : [];

    return (
        <div>
            <div className="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 className="page-title mb-1">Provision Status</h3>
                    <p className="page-subtitle mb-0">Track each onboarding step for this company.</p>
                </div>
                <Button onClick={triggerProvision}>Trigger Provisioning</Button>
            </div>

            <AlertMessage type={message && message.toLowerCase().indexOf('unable') > -1 ? 'error' : 'success'} message={message} />

            <Card>
                <div className="row g-3 mb-3">
                    <div className="col-md-4"><strong>Company Status:</strong> <StatusBadge value={data ? data.company_status : 'unknown'} /></div>
                    <div className="col-md-4"><strong>Provision Status:</strong> <StatusBadge value={data ? data.provision_status : 'unknown'} /></div>
                    <div className="col-md-4"><strong>Tenant ID:</strong> {data ? data.tenant_id || 'N/A' : 'N/A'}</div>
                </div>
                {data && data.error_message ? (
                    <AlertMessage type="error" message={data.error_message} />
                ) : null}
                <Table
                    columns={[
                        { key: 'step', title: 'Step' },
                        {
                            key: 'status',
                            title: 'Status',
                            render: function (row) {
                                return <StatusBadge value={row.status} />;
                            },
                        },
                        { key: 'message', title: 'Message' },
                        { key: 'created_at', title: 'Time' },
                    ]}
                    rows={logs}
                    emptyMessage="No provisioning logs yet."
                />
                <a href={'/companies/' + props.companyId} className="btn btn-outline-secondary mt-2">Back to Company</a>
            </Card>
        </div>
    );
}
