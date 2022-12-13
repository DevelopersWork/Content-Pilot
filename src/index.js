import React from 'react';
import { createRoot } from 'react-dom/client';

import { ThemeProvider } from 'react-bootstrap';

import App from './App';

// eslint-disable-next-line no-undef
const wp_localize_script = dw_content_pilot_app;

document.addEventListener('DOMContentLoaded', function () {
	const element = document.getElementById('dwcp-admin-root');

	if (typeof element !== 'undefined' && null !== element) {
		const root = createRoot(element);

		root.render(
			<React.StrictMode>
				<ThemeProvider
					breakpoints={['xxxl', 'xxl', 'xl', 'lg', 'md', 'sm', 'xs', 'xxs']}
					minBreakpoint="xxs"
				>
					<App wp_localize_script={wp_localize_script} />
				</ThemeProvider>
			</React.StrictMode>
		);
	}
});
