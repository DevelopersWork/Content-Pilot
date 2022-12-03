import React from 'react';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';

document.addEventListener('DOMContentLoaded', function () {
	const element = document.getElementById('dwcp-admin-root');

	if (typeof element !== 'undefined' && element !== null) {
		const root = createRoot(element);

		root.render(
			<StrictMode>
				<App />
			</StrictMode>
		);
	}
});
