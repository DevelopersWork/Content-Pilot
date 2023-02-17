import React, { lazy } from 'react';

const MenuHeader = lazy(() => import('../../Components/menuHeader'));
const TabTemplate = lazy(() => import('../../Components/tabTemplate'));
const EditPostsTemplate = lazy(() =>
	import('../../Components/editPostsTemplate')
);
const ViewPostsTemplate = lazy(() =>
	import('../../Components/viewPostsTemplate')
);

// const addNewCredentials = (props) => {
// 	console.log(props.wp_localize_script);
// 	return (
// 		<Form onSubmit={props.handleOnSubmit}>
// 			<Form.Group className="mb-3" controlId="formBasicEmail">
// 				<Form.Label>Name</Form.Label>
// 				<Form.Control type="email" placeholder="Enter name for the secret" />
// 				<Form.Text className="text-muted">
// 					We'll never share your email with anyone else.
// 				</Form.Text>
// 			</Form.Group>
// 			<Form.Group className="mb-3" controlId="formBasicPassword">
// 				<Form.Label>Password</Form.Label>
// 				<Form.Control type="password" placeholder="Password" />
// 			</Form.Group>
// 			<Form.Group className="mb-3" controlId="formBasicCheckbox">
// 				<Form.Check type="checkbox" label="Check me out" />
// 			</Form.Group>
// 			<Button variant="primary" type="submit">
// 				{props.addButton}
// 			</Button>
// 		</Form>
// 	);
// };

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
