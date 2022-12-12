import React from 'react';
import { createRoot } from 'react-dom/client';

import { ThemeProvider } from 'react-bootstrap';

import App from './src/App';

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
					<App />
				</ThemeProvider>
			</React.StrictMode>
		);
	}
});
