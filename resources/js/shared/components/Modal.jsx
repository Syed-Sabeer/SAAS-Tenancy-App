import React from 'react';
import Button from './Button';

export default function Modal(props) {
    if (!props.open) {
        return null;
    }

    return (
        <div className="modal d-block" tabIndex="-1" role="dialog" style={{ backgroundColor: 'rgba(0,0,0,0.4)' }}>
            <div className="modal-dialog" role="document">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">{props.title || 'Confirm'}</h5>
                        <button type="button" className="btn-close" onClick={props.onCancel} />
                    </div>
                    <div className="modal-body">
                        <p className="mb-0">{props.message || 'Are you sure?'}</p>
                    </div>
                    <div className="modal-footer">
                        <Button variant="btn-outline-secondary" onClick={props.onCancel}>Cancel</Button>
                        <Button variant="btn-danger" onClick={props.onConfirm}>Confirm</Button>
                    </div>
                </div>
            </div>
        </div>
    );
}
