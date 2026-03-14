import React from 'react';

function badgeClass(status) {
    var map = {
        active: 'bg-success',
        provisioning: 'bg-warning text-dark',
        draft: 'bg-secondary',
        suspended: 'bg-danger',
        cancelled: 'bg-dark',
        pending: 'bg-secondary',
        processing: 'bg-warning text-dark',
        completed: 'bg-success',
        failed: 'bg-danger',
        inactive: 'bg-secondary',
        company_admin: 'bg-primary',
        company_user: 'bg-info text-dark',
    };

    return map[status] || 'bg-secondary';
}

export default function StatusBadge(props) {
    var value = props.value || 'unknown';

    return <span className={'badge ' + badgeClass(value)}>{String(value).replace(/_/g, ' ')}</span>;
}
