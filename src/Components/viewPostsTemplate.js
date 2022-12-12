import React from 'react';

import { Table } from 'react-bootstrap';

const viewPostsTemplate = (props) => {
	return (
		<Table striped bordered hover responsive>
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Categories</th>
					<th>Tags</th>
					<th>Author</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>Key for YouTube</td>
					<td></td>
					<td></td>
					<td>DevelopersWork</td>
					<td>1999-01-01 00:00:00.000</td>
				</tr>
			</tbody>
		</Table>
	);
};

export default viewPostsTemplate;
