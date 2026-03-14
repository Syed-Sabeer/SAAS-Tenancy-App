import React from 'react';
import Button from './Button';

export default function TopNav(props) {
    return (
        <nav className="navbar navbar-expand-lg navbar-dark app-gradient mb-4">
            <div className="container-fluid">
                <span className="navbar-brand fw-bold">{props.title}</span>
                <div className="d-flex gap-2">
                    {(props.links || []).map(function (link) {
                        return (
                            <a key={link.href} href={link.href} className="btn btn-sm btn-outline-light">
                                {link.label}
                            </a>
                        );
                    })}
                    {props.onLogout ? (
                        <Button variant="btn-sm btn-light text-dark" onClick={props.onLogout}>
                            Logout
                        </Button>
                    ) : null}
                </div>
            </div>
        </nav>
    );
}
