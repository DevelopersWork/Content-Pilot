import React, { lazy, Suspense } from 'react';
import { useLocation } from 'react-router-dom';

const Dashboard = lazy(() => import('./Pages/Dashboard'));
const Credentials = lazy(() => import('./Pages/Credentials'));
const LoadingAnimation = lazy(() => import('./Components/loadingAnimation'));

import './App.scss';

const RouterComponent = (props) => {
	const location = useLocation();
	const query = new URLSearchParams(location.search);

	if (query.get('page') === 'dw-cp-main') return <Dashboard {...props} />;
	else if (query.get('page') === 'dw-cp-credentials')
		return <Credentials {...props} />;

	return <React.Fragment></React.Fragment>;
};

const App = (props) => {
	return (
		<Suspense fallback={<LoadingAnimation />}>
			<RouterComponent {...props} />
		</Suspense>
	);
};

export default App;
