import React, { lazy, Suspense } from 'react';

import { Container } from 'react-bootstrap';
import { Tabs, Tab } from 'react-bootstrap';

const LoadingAnimation = lazy(() => import('./loadingAnimation'));

const getTabContent = (options, activeKey, isLoading) => {
	if (activeKey === options.eventKey && !isLoading)
		return (() => {
			if (options.component) return options.component(options.props || {});
			return <h1>{options.title}</h1>;
		})();

	return <LoadingAnimation />;
};

const generateTab = (options, activeKey, isLoading) => (
	<Tab key={options.eventKey} eventKey={options.eventKey} title={options.title}>
		<Suspense fallback={<LoadingAnimation />}>
			<Container fluid>
				{getTabContent(options, activeKey, isLoading)}
			</Container>
		</Suspense>
	</Tab>
);

const handleChangeKey = async (key, setIsLoading, setKey, parent) => {
	setIsLoading(<LoadingAnimation />);
	if (parent)
		return parent(key).then(() => {
			if (setKey) setKey(key);
			setIsLoading();
		});
	if (setKey) setKey(key);
	setIsLoading();
};

const tabTemplate = (props) => {
	const options = {
		id: props.id || '',
		defaultActiveKey: props.defaultActiveKey || '',
		tabs: props.tabs || [],
		setTab: props.setTab,
	};

	const [isLoading, setIsLoading] = React.useState();
	const [key, setKey] = React.useState(options.defaultActiveKey);

	return (
		<Tabs
			id={options.id}
			activeKey={key}
			onSelect={(k) => handleChangeKey(k, setIsLoading, setKey, options.setTab)}
			className="mb-3"
		>
			{options.tabs.map((tab) => generateTab(tab, key, isLoading))}
		</Tabs>
	);
};

export default tabTemplate;
