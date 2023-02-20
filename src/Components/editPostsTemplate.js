import React from 'react';

import { Form, Button } from 'react-bootstrap';

const textField = (options) => (
	<Form.Group className="mb-3" key={JSON.stringify(options)}>
		{options.label ? <Form.Label>{options.label}</Form.Label> : ''}
		<Form.Control {...options.control} />
		{options.text ? (
			<Form.Text className="text-muted">{options.text}</Form.Text>
		) : (
			''
		)}
	</Form.Group>
);

const checkbox = (options) => (
	<Form.Group className="mb-3" key={JSON.stringify(options)}>
		{options.label ? <Form.Label>{options.label}</Form.Label> : ''}
		<Form.Check {...options.check} />
		<Form.Text className="text-muted">{options.text}</Form.Text>
	</Form.Group>
);

const editPostsTemplate = (props) => {
	const fields =
		props.fields && typeof props.fields === typeof [] ? props.fields : [];
	return (
		<React.Fragment>
			<Form onSubmit={props.handleOnSubmit}>
				{fields.map((e) => {
					if (e._type === 'text') return textField(e);
					if (e._type === 'checkbox') return checkbox(e);
				})}
				<Button variant="primary" type="submit">
					{props.addButton}
				</Button>
			</Form>
		</React.Fragment>
	);
};

export default editPostsTemplate;
