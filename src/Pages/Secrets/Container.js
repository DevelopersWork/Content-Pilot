import React from 'react';

import Presentation from './Presentation';

class ContainerComponent extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			addButton: 'Add',
		};
	}

	componentDidMount() {
		console.log(this.props);
	}

	handleOnChange = () => {};

	handleOnSubmit = (e) => {
		e.preventDefault();
		this.setState({
			addButton: 'Adding...',
		});

		const url = `${this.props.wp_localize_script.apiUrl}/dw_content_pilot_api/v1/Secrets`;

		const headers = {
			'X-WP-NONCE': `${this.props.wp_localize_script.nonce}`,
		};

		fetch(url, {
			headers,
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
