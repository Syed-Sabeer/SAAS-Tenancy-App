import React from 'react';

export default function Table(props) {
    return (
        <div className="table-responsive">
            <table className="table table-hover align-middle">
                <thead className="table-light">
                    <tr>
                        {(props.columns || []).map(function (column) {
                            return <th key={column.key}>{column.title}</th>;
                        })}
                    </tr>
                </thead>
                <tbody>
                    {(props.rows || []).length === 0 ? (
                        <tr>
                            <td colSpan={(props.columns || []).length} className="text-center text-muted py-4">
                                {props.emptyMessage || 'No records found.'}
                            </td>
                        </tr>
                    ) : (
                        (props.rows || []).map(function (row, rowIndex) {
                            return (
                                <tr key={row.id || rowIndex}>
                                    {(props.columns || []).map(function (column) {
                                        if (column.render) {
                                            return <td key={column.key}>{column.render(row)}</td>;
                                        }

                                        return <td key={column.key}>{row[column.key]}</td>;
                                    })}
                                </tr>
                            );
                        })
                    )}
                </tbody>
            </table>
        </div>
    );
}
