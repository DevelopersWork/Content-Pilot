import React from 'react';

import { Container } from 'react-bootstrap';
import { Tabs, Tab } from 'react-bootstrap';

const createTab = (options) => (
	<Tab key={options.eventKey} eventKey={options.eventKey} title={options.title}>
		<Container>
			{(() => {
				if (options.component) return options.component(options.props || {});
				return <h1>{options.title}</h1>;
			})()}
		</Container>
	</Tab>
);

const tabTemplate = (props) => {
	const options = {
		defaultActiveKey: props.defaultActiveKey || '',
		id: props.id || '',
		tabs: props.tabs || [],
	};

	return (
		<React.Fragment>
			<Tabs
				defaultActiveKey={options.defaultActiveKey}
				id={options.id}
				className="mb-3"
			>
				{options.tabs.map((tab) => createTab(tab))}
			</Tabs>
		</React.Fragment>
	);
};

export default tabTemplate;
