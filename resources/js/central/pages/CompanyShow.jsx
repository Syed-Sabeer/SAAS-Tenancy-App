import React, { useEffect, useState } from 'react';
import centralApi from '../../shared/api/centralApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Card from '../../shared/components/Card';
import Loader from '../../shared/components/Loader';
import StatusBadge from '../../shared/components/StatusBadge';

export default function CompanyShow(props) {
    var [company, setCompany] = useState(null);
    var [message, setMessage] = useState('');

    useEffect(function () {
        centralApi
            .getCompany(props.companyId)
            .then(function (response) {
                setCompany(response.data.data ? response.data.data.company : null);
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load company details.');
            });
    }, [props.companyId]);

    if (!company && !message) {
        return <Loader label="Loading company details..." />;
    }

    return (
        <div>
            <h3 className="page-title mb-1">Company Details</h3>
            <p className="page-subtitle mb-3">Review company profile and onboarding status.</p>
            <AlertMessage type="error" message={message} />
            {company ? (
                <Card>
                    <div className="row g-3">
                        <div className="col-md-6"><strong>Name:</strong> {company.company_name}</div>
                        <div className="col-md-6"><strong>Code:</strong> {company.company_code}</div>
                        <div className="col-md-6"><strong>Subdomain:</strong> {company.subdomain}</div>
                        <div className="col-md-6"><strong>Status:</strong> <StatusBadge value={company.status} /></div>
                        <div className="col-md-6"><strong>Tenant ID:</strong> {company.tenant_id || 'N/A'}</div>
                        <div className="col-md-6"><strong>Email:</strong> {company.email || 'N/A'}</div>
                        <div className="col-md-6"><strong>Website:</strong> {company.website || 'N/A'}</div>
                        <div className="col-md-6"><strong>Phone:</strong> {company.phone || 'N/A'}</div>
                    </div>
                    <div className="d-flex gap-2 mt-4">
                        <a href={'/companies/' + props.companyId + '/edit'} className="btn btn-outline-primary">Edit</a>
                        <a href={'/companies/' + props.companyId + '/provision-status'} className="btn btn-outline-dark">Provision Status</a>
                        <a href="/companies" className="btn btn-outline-secondary">Back</a>
                    </div>
                </Card>
            ) : null}
        </div>
    );
}
