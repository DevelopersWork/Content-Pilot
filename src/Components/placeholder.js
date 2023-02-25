import React from 'react';

import { Image } from 'react-bootstrap';

// import placeholder from '../../assets/images/4dfdoa.jpg';

const Placeholder = () => {
	return (
		<div className="text-center align-items-center">
			<Image
				fluid
				thumbnail
				src={'https://c.tenor.com/JA7DqiW6_4kAAAAC/panic-mainwaring.gif'}
			/>
		</div>
	);
};

export default Placeholder;
