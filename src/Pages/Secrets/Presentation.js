/* eslint-disable react/prop-types */
import React from 'react';

import { Container, Card } from 'react-bootstrap';

const Presentation = (props) => {
	return (
		<React.Fragment>
			<Card>
				<Card.Body>
					<Card.Title>
						<h2>{props.name || ''}</h2>
					</Card.Title>
					<Card.Subtitle>{props.description || ''}</Card.Subtitle>
					<Container>
						<h1>HELLO WORLD</h1>
					</Container>
				</Card.Body>
			</Card>
		</React.Fragment>
	);
};

export default Presentation;
