import React, { useState } from 'react';

import { Container, Card, Form, Button } from 'react-bootstrap';

import MenuHeader from '../../Components/menuHeader';
import TabTemplate from '../../Components/tabTemplate';
import ViewPostsTemplate from '../../Components/viewPostsTemplate';

const addNewSecrets = (props) => {
	console.log(props.wp_localize_script);
	return (
		<Form onSubmit={props.handleOnSubmit}>
			<Form.Group className="mb-3" controlId="formBasicEmail">
				<Form.Label>Name</Form.Label>
				<Form.Control type="email" placeholder="Enter name for the secret" />
				<Form.Text className="text-muted">
					We'll never share your email with anyone else.
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
	);
};

const allSecrets = (props) => <ViewPostsTemplate {...props} />;

const Presentation = (props) => {
	return (
		<React.Fragment>
			<MenuHeader {...props} />
			<TabTemplate
				defaultActiveKey="all-secrets"
				id="secrets-tab"
				tabs={[
					{
						eventKey: 'all-secrets',
						title: 'All Secrets',
						component: allSecrets,
						props: props,
					},
					{
						eventKey: 'add-new-secrets',
						title: 'Add New',
						component: addNewSecrets,
						props: props,
					},
				]}
			/>
		</React.Fragment>
	);
};

export default Presentation;
