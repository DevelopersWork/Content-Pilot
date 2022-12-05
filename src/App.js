import React from 'react';

import Dashboard from './Pages/Dashboard';
import Secrets from './Pages/Secrets';

const App = () => {
	// accessing query parameter "page"
	const page = new URL(window.location.href).searchParams.get('page');

	if (page === 'dw-cp-wppage') return <Dashboard />;
	else if (page === 'dw-cp-secrets') return <Secrets />;

	return <React.Fragment></React.Fragment>;
};

export default App;
