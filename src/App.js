import { Fragment } from 'react';

import Dashboard from './Pages/Dashboard';
import Secrets from './Pages/Secrets';

const App = ({ props }) => {
	return (
		<Fragment>
			<Dashboard {...props} />
			<Secrets {...props} />
		</Fragment>
	);
};

export default App;
