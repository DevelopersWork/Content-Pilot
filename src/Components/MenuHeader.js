import React from 'react';
import { useLocation } from 'react-router-dom';

const addNewButton = () => {
	const location = useLocation();
	const query = new URLSearchParams(location.search);
	return (
		<a
			href={location.pathname + '?page=' + query.get('page') + '&post_id=-1'}
			className="btn btn-outline-primary mx-3"
		>
			Add New
		</a>
	);
};

const menuHeader = (props) => {
	console.log(props.addNewButton);
	return (
		<React.Fragment>
			<h2 className="d-inline-flex">
				{props.name || ''}
				{props.addNewButton !== undefined ? addNewButton() : ''}
			</h2>

			<p>{props.description || ''}</p>
			<hr className="wp-header-end" />
		</React.Fragment>
	);
};

export default menuHeader;
