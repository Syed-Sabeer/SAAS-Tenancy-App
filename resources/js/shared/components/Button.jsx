import React from 'react';

export default function Button(props) {
    var className = 'btn ' + (props.variant || 'btn-primary') + ' ' + (props.className || '');

    return (
        <button
            type={props.type || 'button'}
            className={className}
            onClick={props.onClick}
            disabled={props.disabled}
        >
            {props.loading ? 'Please wait...' : props.children}
        </button>
    );
}
