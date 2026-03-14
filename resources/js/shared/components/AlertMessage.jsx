import React from 'react';

export default function AlertMessage(props) {
    if (!props.message) {
        return null;
    }

    var className = 'alert ' + (props.type === 'error' ? 'alert-danger' : 'alert-success');

    return (
        <div className={className} role="alert">
            {props.message}
        </div>
    );
}
