import React from 'react';

import Presentation from './Presentation';

class ContainerComponent extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			addButton: 'Add',
			REST_API: {
				url: `${this.props.wp_localize_script.apiUrl.replace(
					/\/?$/,
					''
				)}/credentials`,
				headers: {
					'X-WP-NONCE': `${this.props.wp_localize_script.nonce}`,
				},
			},
			columns: [
				{ name: 'ID', value: '#' },
				{ name: 'post_name', value: 'Title' },
				{ name: 'post_date', value: 'Created On' },
				{ name: 'post_modified', value: 'Last Modified On' },
			],
			posts_per_page: 1,
			current_page: 0,
			total_posts: 0,
			posts: [],
		};
	}

	fetchPosts = () => {
		fetch(
			this.state.REST_API.url +
				'?' +
				new URLSearchParams({
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
		this.fetchPosts();
	}

	handleOnChange = () => {};

	handleOnSubmit = (e) => {
		e.preventDefault();
		this.setState({
			addButton: 'Adding...',
		});

		fetch(this.state.REST_API.url, {
			headers: this.state.REST_API.headers,
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

	render() {
		return (
			<Presentation
				{...this.props}
				{...this.state}
				handleOnChange={this.handleOnChange}
				handleOnSubmit={this.handleOnSubmit}
			/>
		);
	}
}

export default ContainerComponent;
