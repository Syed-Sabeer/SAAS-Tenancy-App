import React, { useEffect, useState } from 'react';
import centralApi from '../../shared/api/centralApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import Loader from '../../shared/components/Loader';
import Modal from '../../shared/components/Modal';
import StatusBadge from '../../shared/components/StatusBadge';
import Table from '../../shared/components/Table';

export default function CompanyList() {
    var [rows, setRows] = useState([]);
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(true);
    var [confirmDelete, setConfirmDelete] = useState(null);

    function loadCompanies() {
        setLoading(true);
        centralApi
            .companies()
            .then(function (response) {
                var data = response.data.data && response.data.data.companies ? response.data.data.companies.data || [] : [];
                setRows(data);
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load companies.');
            })
            .finally(function () {
                setLoading(false);
            });
    }

    useEffect(function () {
        loadCompanies();
    }, []);

    function provision(company) {
        centralApi
            .provisionCompany(company.id)
            .then(function (response) {
                setMessage(response.data.message || 'Provisioning started.');
                loadCompanies();
            })
            .catch(function (error) {
                setMessage(error.message || 'Provisioning failed.');
            });
    }

    function destroy(id) {
        centralApi
            .deleteCompany(id)
            .then(function (response) {
                setMessage(response.data.message || 'Company deleted.');
                setConfirmDelete(null);
                loadCompanies();
            })
            .catch(function (error) {
                setMessage(error.message || 'Delete failed.');
                setConfirmDelete(null);
            });
    }

    var columns = [
        { key: 'company_name', title: 'Company' },
        { key: 'company_code', title: 'Code' },
        { key: 'subdomain', title: 'Subdomain' },
        {
            key: 'status',
            title: 'Status',
            render: function (row) {
                return <StatusBadge value={row.status} />;
            },
        },
        {
            key: 'actions',
            title: 'Actions',
            render: function (row) {
                return (
                    <div className="d-flex flex-wrap gap-2">
                        <a href={'/companies/' + row.id} className="btn btn-sm btn-outline-primary">View</a>
                        <a href={'/companies/' + row.id + '/edit'} className="btn btn-sm btn-outline-secondary">Edit</a>
                        <Button variant="btn-sm btn-success" onClick={function () { provision(row); }}>
                            Provision
                        </Button>
                        <a href={'/companies/' + row.id + '/provision-status'} className="btn btn-sm btn-outline-dark">Status</a>
                        <Button variant="btn-sm btn-danger" onClick={function () { setConfirmDelete(row); }}>
                            Delete
                        </Button>
                    </div>
                );
            },
        },
    ];

    return (
        <div>
            <div className="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 className="page-title mb-1">Companies</h3>
                    <p className="page-subtitle mb-0">Manage tenant onboarding and provisioning.</p>
                </div>
                <a href="/companies/create" className="btn btn-primary">Create Company</a>
            </div>

            <AlertMessage type={message && message.toLowerCase().indexOf('failed') > -1 ? 'error' : 'success'} message={message} />

            <Card>
                {loading ? <Loader label="Loading companies..." /> : <Table columns={columns} rows={rows} emptyMessage="No companies found." />}
            </Card>

            <Modal
                open={Boolean(confirmDelete)}
                title="Delete Company"
                message={confirmDelete ? 'Delete ' + confirmDelete.company_name + '?' : ''}
                onCancel={function () { setConfirmDelete(null); }}
                onConfirm={function () { if (confirmDelete) { destroy(confirmDelete.id); } }}
            />
        </div>
    );
}
