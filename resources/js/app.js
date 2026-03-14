require('./bootstrap');

import React from 'react';
import ReactDOM from 'react-dom';
import CentralApp from './central/app';
import TenantApp from './tenant/app';
import './styles/app.css';

function resolveScope(rootElement) {
	if (!rootElement) {
		return 'central';
	}

	var scope = rootElement.getAttribute('data-app-scope');

	if (scope === 'tenant') {
		return 'tenant';
	}

	return 'central';
}

var root = document.getElementById('app');

if (root) {
	var scope = resolveScope(root);
	var App = scope === 'tenant' ? TenantApp : CentralApp;
	ReactDOM.render(<App />, root);
}
