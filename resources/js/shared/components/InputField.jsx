import React from 'react';

export default function InputField(props) {
    var id = props.id || props.name;

    return (
        <div className="mb-3">
            <label htmlFor={id} className="form-label fw-semibold">
                {props.label}
            </label>
            <input
                id={id}
                name={props.name}
                type={props.type || 'text'}
                className={'form-control ' + (props.error ? 'is-invalid' : '')}
                value={props.value || ''}
                onChange={props.onChange}
                placeholder={props.placeholder || ''}
                autoComplete={props.autoComplete || 'off'}
            />
            {props.error ? <div className="invalid-feedback">{props.error}</div> : null}
        </div>
    );
}
