import React from 'react';
import ContainerComponent from './Container';

const component = (props) => {
	return (
		<ContainerComponent
			{...props}
			name="Secrets"
			description="WordPress offers you the ability to create a custom URL structure for your permalinks and archives. Custom URL structures can improve the aesthetics, usability, and forward-compatibility of your links. A number of tags are available, and here are some examples to get you started."
		/>
	);
};

export default component;
