import React from 'react';

export default function Card(props) {
    return (
        <div className={'card shadow-sm border-0 ' + (props.className || '')}>
            {props.title ? (
                <div className="card-header bg-white">
                    <h6 className="mb-0 fw-bold">{props.title}</h6>
                </div>
            ) : null}
            <div className="card-body">{props.children}</div>
        </div>
    );
}
