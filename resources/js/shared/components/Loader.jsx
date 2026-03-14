import React from 'react';

export default function Loader(props) {
    return (
        <div className={'d-flex align-items-center gap-2 ' + (props.className || '')}>
            <div className="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true" />
            <span>{props.label || 'Loading...'}</span>
        </div>
    );
}
