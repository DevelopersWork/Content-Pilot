import React from 'react';

import { Table } from 'react-bootstrap';
import { Form, Button } from 'react-bootstrap';

const editPostsTemplate = (props) => {
	// props.fetchPosts(props.options);
	return (
		<React.Fragment>
			<Form onSubmit={props.handleOnSubmit}>
				<Form.Group className="mb-3" controlId="formBasicEmail">
					<Form.Label>Name</Form.Label>
					<Form.Control type="email" placeholder="Enter name for the secret" />
					<Form.Text className="text-muted">
						{"We'll never share your email with anyone else."}
					</Form.Text>
				</Form.Group>
				<Form.Group className="mb-3" controlId="formBasicPassword">
					<Form.Label>Password</Form.Label>
					<Form.Control type="password" placeholder="Password" />
				</Form.Group>
				<Form.Group className="mb-3" controlId="formBasicCheckbox">
					<Form.Check type="checkbox" label="Check me out" />
				</Form.Group>
				<Button variant="primary" type="submit">
					{props.addButton}
				</Button>
			</Form>
			<Table bordered striped hover className="min-vh-80">
				<thead>
					<tr></tr>
				</thead>
				<tbody></tbody>
			</Table>
		</React.Fragment>
	);
};

export default editPostsTemplate;
