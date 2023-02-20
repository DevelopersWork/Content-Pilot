import React, { lazy } from 'react';

const MenuHeader = lazy(() => import('../../Components/menuHeader'));
const TabTemplate = lazy(() => import('../../Components/tabTemplate'));
const EditPostsTemplate = lazy(() =>
	import('../../Components/editPostsTemplate')
);
const ViewPostsTemplate = lazy(() =>
	import('../../Components/viewPostsTemplate')
);

const SwitchComponent = (props) => {
	if (props.post_id !== undefined && props.post_id !== null)
		return <EditPostsTemplate {...props} />;
	return (
		<React.Fragment>
			<TabTemplate
				defaultActiveKey={props.tab}
				id="credentials-tab"
				tabs={[
					{
						eventKey: 'all-credentials',
						title: 'All',
						component: () => <ViewPostsTemplate {...props} />,
					},
					{
						eventKey: 'published-credentials',
						title: 'Published',
						component: () => <ViewPostsTemplate {...props} />,
					},
					{
						eventKey: 'trash-credentials',
						title: 'Trash',
						component: () => <ViewPostsTemplate {...props} />,
					},
				]}
				setTab={props.handleOnTabChange}
			/>
		</React.Fragment>
	);
};

const Presentation = (props) => {
	return (
		<React.Fragment>
			<MenuHeader {...props} />
			<SwitchComponent {...props} />
		</React.Fragment>
	);
};

export default Presentation;
