import React, { lazy, Suspense } from 'react';

const Dashboard = lazy(() => import('./Pages/Dashboard'));
const Credentials = lazy(() => import('./Pages/Credentials'));
const LoadingAnimation = lazy(() => import('./Components/loadingAnimation'));

import './App.scss';

const RouterComponent = (props) => {
	if (props.page === 'dw-cp-wppage') return <Dashboard {...props} />;
	else if (props.page === 'dw-cp-credentials')
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
