import React from 'react';

export default function SelectField(props) {
    var id = props.id || props.name;

    return (
        <div className="mb-3">
            <label htmlFor={id} className="form-label fw-semibold">
                {props.label}
            </label>
            <select
                id={id}
                name={props.name}
                className={'form-select ' + (props.error ? 'is-invalid' : '')}
                value={props.value || ''}
                onChange={props.onChange}
            >
                <option value="">Select</option>
                {(props.options || []).map(function (option) {
                    return (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    );
                })}
            </select>
            {props.error ? <div className="invalid-feedback">{props.error}</div> : null}
        </div>
    );
}
