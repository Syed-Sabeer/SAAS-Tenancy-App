import React, { useEffect, useState } from 'react';
import tenantApi from '../../shared/api/tenantApi';
import AlertMessage from '../../shared/components/AlertMessage';
import Button from '../../shared/components/Button';
import Card from '../../shared/components/Card';
import Loader from '../../shared/components/Loader';
import Modal from '../../shared/components/Modal';
import StatusBadge from '../../shared/components/StatusBadge';
import Table from '../../shared/components/Table';

export default function UserList() {
    var [rows, setRows] = useState([]);
    var [message, setMessage] = useState('');
    var [loading, setLoading] = useState(true);
    var [confirmDelete, setConfirmDelete] = useState(null);

    function load() {
        setLoading(true);
        tenantApi
            .users()
            .then(function (response) {
                var data = response.data.data && response.data.data.users ? response.data.data.users.data || [] : [];
                setRows(data);
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to load users.');
            })
            .finally(function () {
                setLoading(false);
            });
    }

    useEffect(function () {
        load();
    }, []);

    function destroy(id) {
        tenantApi
            .deleteUser(id)
            .then(function (response) {
                setMessage(response.data.message || 'User deleted.');
                setConfirmDelete(null);
                load();
            })
            .catch(function (error) {
                setMessage(error.message || 'Delete failed.');
                setConfirmDelete(null);
            });
    }

    return (
        <div>
            <div className="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 className="page-title mb-1">Users</h3>
                    <p className="page-subtitle mb-0">Manage company admins and users.</p>
                </div>
                <a href="/users/create" className="btn btn-primary">Add User</a>
            </div>
            <AlertMessage type={message && message.toLowerCase().indexOf('failed') > -1 ? 'error' : 'success'} message={message} />
            <Card>
                {loading ? (
                    <Loader label="Loading users..." />
                ) : (
                    <Table
                        columns={[
                            { key: 'name', title: 'Name' },
                            { key: 'email', title: 'Email' },
                            {
                                key: 'role',
                                title: 'Role',
                                render: function (row) {
                                    return <StatusBadge value={row.role} />;
                                },
                            },
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
                                        <div className="d-flex gap-2">
                                            <a href={'/users/' + row.id + '/edit'} className="btn btn-sm btn-outline-primary">Edit</a>
                                            <Button variant="btn-sm btn-danger" onClick={function () { setConfirmDelete(row); }}>Delete</Button>
                                        </div>
                                    );
                                },
                            },
                        ]}
                        rows={rows}
                        emptyMessage="No users found."
                    />
                )}
            </Card>
            <Modal
                open={Boolean(confirmDelete)}
                title="Delete User"
                message={confirmDelete ? 'Delete ' + confirmDelete.name + '?' : ''}
                onCancel={function () { setConfirmDelete(null); }}
                onConfirm={function () { if (confirmDelete) { destroy(confirmDelete.id); } }}
            />
        </div>
    );
}
