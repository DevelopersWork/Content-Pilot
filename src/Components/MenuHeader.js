import React from 'react';

const menuHeader = (props) => {
	return (
		<React.Fragment>
			<h2 className="">{props.name || ''}</h2>
			<p>{props.description || ''}</p>
			<hr className="wp-header-end" />
		</React.Fragment>
	);
};

export default menuHeader;
