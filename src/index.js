import React from 'react';
import { createRoot } from 'react-dom/client';

import { ThemeProvider } from 'react-bootstrap';

import App from './App';

document.addEventListener('DOMContentLoaded', function () {
	const element = document.getElementById('dwcp-admin-root');

	if (typeof element !== 'undefined' && null !== element) {
		const root = createRoot(element);
		const page = new URL(window.location.href).searchParams.get('page');
		const wp_localize_script =
			// eslint-disable-next-line no-undef
			typeof dwcp_app !== 'undefined' ? dwcp_app : null;

		if (null !== wp_localize_script && page.match(/^dw-cp-/)) {
			root.render(
				<React.StrictMode>
					<ThemeProvider
						breakpoints={['xxxl', 'xxl', 'xl', 'lg', 'md', 'sm', 'xs', 'xxs']}
						minBreakpoint="xxs"
					>
						<App wp_localize_script={wp_localize_script} page={page} />
					</ThemeProvider>
				</React.StrictMode>
			);
		} else
			root.render(
				<React.StrictMode>
					<ThemeProvider
						breakpoints={['xxxl', 'xxl', 'xl', 'lg', 'md', 'sm', 'xs', 'xxs']}
						minBreakpoint="xxs"
					>
						<h1>Plugin Broken</h1>
					</ThemeProvider>
				</React.StrictMode>
			);
	}
});
