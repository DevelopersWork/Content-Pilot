import React from 'react';

import Presentation from './Presentation';

class ContainerComponent extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			REST_API: {
				url: `${this.props.wp_localize_script.apiUrl.replace(
					/\/?$/,
					''
				)}/credentials`,
				headers: {
					'X-WP-Nonce': `${this.props.wp_localize_script.nonce}`,
					'X-DWCP-Nonce': `${this.props.wp_localize_script.dwcp_nonce}`,
				},
			},
			columns: [
				{ name: 'ID', value: '#' },
				{ name: 'post_name', value: 'Title' },
				{ name: 'author', value: 'Author' },
				{ name: 'categories', value: 'Categories' },
				{ name: 'post_date', value: 'Created On' },
				{ name: 'post_modified', value: 'Last Modified On' },
			],
			page: new URL(window.location.href).searchParams.get('page'),
			post_id: new URL(window.location.href).searchParams.get('post_id'),
			posts_per_page:
				new URL(window.location.href).searchParams.get('posts_per_page') || 10,
			current_page:
				new URL(window.location.href).searchParams.get('current_page') || 0,
			total_posts:
				new URL(window.location.href).searchParams.get('total_posts') || 0,
			posts: [],
			static: {},
			tab: 'all-credentials',
			fields: [
				{
					label: 'Name',
					control: { type: 'text', placeholder: 'Name of the credential' },
					text: 'Custom Text',
					_priority: 0,
					_type: 'text',
				},
				{
					label: 'Value',
					control: { type: 'password', placeholder: 'Value of the Credential' },
					text: "We'll never share your email with anyone else.",
					_priority: 1,
					_type: 'text',
				},
			],
			addButton: 'Add',
		};
	}

	fetchCredentials = async (queryParams = {}) => {
		return fetch(
			this.state.REST_API.url +
				'?' +
				new URLSearchParams({
					...queryParams,
					posts_per_page: this.state.posts_per_page,
					offset: this.state.current_page,
				}),
			{
				method: 'GET',
				headers: this.state.REST_API.headers,
			}
		)
			.then((response) => response.json())
			.then(
				function (response) {
					this.setState({
						posts: response.posts || [],
						total_posts: response.total_posts || 0,
					});
				}.bind(this)
			)
			.catch((error) => console.error(error));
	};

	componentDidMount() {
		this.fetchCredentials();
	}

	handleOnChange = () => {};

	handleOnSubmit = (e) => {
		e.preventDefault();
		this.setState({
			addButton: 'Adding...',
		});

		var body = new FormData();
		body.append('title', 'ABc');
		body.append('value', '1234');
		body.append('category', 'YouTube111');

		fetch(this.state.REST_API.url, {
			method: 'POST',
			headers: this.state.REST_API.headers,
			body: body,
		})
			.then((response) => response.json())
			.then(
				function (response) {
					console.log(response);
					this.setState({
						addButton: 'Add',
					});
				}.bind(this)
			);
	};

	handleOnTabChange = async (key) => {
		const queryParams = {
			post_status: key.split('-')[0],
		};
		return this.fetchCredentials(queryParams).then(() => {
			return this.setState({ tab: key });
		});
	};

	render() {
		return (
			<Presentation
				{...this.props}
				{...this.state}
				handleOnChange={this.handleOnChange}
				handleOnSubmit={this.handleOnSubmit}
				handleOnTabChange={this.handleOnTabChange}
			/>
		);
	}
}

export default ContainerComponent;
