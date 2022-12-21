import React, { lazy, Suspense } from 'react';

import { Container } from 'react-bootstrap';
import { Tabs, Tab } from 'react-bootstrap';

const LoadingAnimation = lazy(() => import('./loadingAnimation'));

const createTab = (options, activeKey) => {
	if (activeKey === options.eventKey)
		return (
			<Tab
				key={options.eventKey}
				eventKey={options.eventKey}
				title={options.title}
			>
				<Suspense fallback={<LoadingAnimation />}>
					<Container fluid>
						{(() => {
							if (options.component)
								return options.component(options.props || {});
							return <h1>{options.title}</h1>;
						})()}
					</Container>
				</Suspense>
			</Tab>
		);
	else
		return (
			<Tab
				key={options.eventKey}
				eventKey={options.eventKey}
				title={options.title}
			>
				<Container fluid></Container>
			</Tab>
		);
};

const tabTemplate = (props) => {
	const options = {
		defaultActiveKey: props.defaultActiveKey || '',
		id: props.id || '',
		tabs: props.tabs || [],
	};

	const [key, setKey] = React.useState(options.defaultActiveKey);

	return (
		<Tabs
			activeKey={key}
			onSelect={(k) => setKey(k)}
			id={options.id}
			className="mb-3"
		>
			{options.tabs.map((tab) => createTab(tab, key))}
		</Tabs>
	);
};

export default tabTemplate;
