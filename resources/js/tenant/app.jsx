import React from 'react';
import tenantApi from '../shared/api/tenantApi';
import { matchPath } from '../shared/utils/routeMatcher';
import TopNav from '../shared/components/TopNav';
import TenantLogin from './pages/TenantLogin';
import TenantDashboard from './pages/TenantDashboard';
import UserList from './pages/UserList';
import UserCreate from './pages/UserCreate';
import UserEdit from './pages/UserEdit';
import TenantProfile from './pages/TenantProfile';

function path() {
    return window.location.pathname;
}

function navLinks() {
    return [
        { href: '/dashboard', label: 'Dashboard' },
        { href: '/users', label: 'Users' },
        { href: '/profile', label: 'Profile' },
    ];
}

export default function TenantApp() {
    var pathname = path();
    var editMatch = matchPath('/users/:id/edit', pathname);
    var isLogin = pathname === '/login';

    function logout() {
        tenantApi.logout().finally(function () {
            window.location.href = '/login';
        });
    }

    if (isLogin) {
        return <TenantLogin />;
    }

    var content = <TenantDashboard />;

    if (pathname === '/users') {
        content = <UserList />;
    } else if (pathname === '/users/create') {
        content = <UserCreate />;
    } else if (editMatch) {
        content = <UserEdit userId={editMatch.id} />;
    } else if (pathname === '/profile') {
        content = <TenantProfile />;
    }

    return (
        <div className="app-shell">
            <TopNav title="Tenant Workspace" links={navLinks()} onLogout={logout} />
            {content}
        </div>
    );
}
