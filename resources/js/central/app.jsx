import React from 'react';
import centralApi from '../shared/api/centralApi';
import { matchPath } from '../shared/utils/routeMatcher';
import TopNav from '../shared/components/TopNav';
import EnterpriseLogin from './pages/EnterpriseLogin';
import EnterpriseDashboard from './pages/EnterpriseDashboard';
import CompanyList from './pages/CompanyList';
import CompanyCreate from './pages/CompanyCreate';
import CompanyEdit from './pages/CompanyEdit';
import CompanyShow from './pages/CompanyShow';
import ProvisionStatus from './pages/ProvisionStatus';

function path() {
    return window.location.pathname;
}

function navLinks() {
    return [
        { href: '/dashboard', label: 'Dashboard' },
        { href: '/companies', label: 'Companies' },
        { href: '/companies/create', label: 'New Company' },
    ];
}

export default function CentralApp() {
    var pathname = path();
    var editMatch = matchPath('/companies/:id/edit', pathname);
    var showMatch = matchPath('/companies/:id', pathname);
    var statusMatch = matchPath('/companies/:id/provision-status', pathname);
    var isLogin = pathname === '/login';

    function logout() {
        centralApi.logout().finally(function () {
            window.location.href = '/login';
        });
    }

    if (isLogin) {
        return <EnterpriseLogin />;
    }

    var content = <CompanyList />;

    if (pathname === '/dashboard') {
        content = <EnterpriseDashboard />;
    } else if (pathname === '/companies/create') {
        content = <CompanyCreate />;
    } else if (statusMatch) {
        content = <ProvisionStatus companyId={statusMatch.id} />;
    } else if (editMatch) {
        content = <CompanyEdit companyId={editMatch.id} />;
    } else if (showMatch && showMatch.id) {
        content = <CompanyShow companyId={showMatch.id} />;
    }

    return (
        <div className="app-shell">
            <TopNav title="Enterprise Console" links={navLinks()} onLogout={logout} />
            {content}
        </div>
    );
}
