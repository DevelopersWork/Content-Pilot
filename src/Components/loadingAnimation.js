import React from 'react';

import { Spinner } from 'react-bootstrap';

const LoadingAnimation = () => {
	return (
		<div className="text-center align-items-center">
			<Spinner animation="border" role="status">
				<span className="visually-hidden">Loading...</span>
			</Spinner>
		</div>
	);
};

export default LoadingAnimation;
