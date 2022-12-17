import React from 'react';

import Presentation from './Presentation';

class ContainerComponent extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			addButton: 'Add',
			url: `${this.props.wp_localize_script.apiUrl.replace(
				/\/?$/,
				''
			)}/credentials`,
		};
	}

	componentDidMount() {
		const headers = {
			'X-WP-NONCE': `${this.props.wp_localize_script.nonce}`,
		};
		fetch(this.state.url, {
			method: 'POST',
			headers,
		})
			.then((response) => response.text())
			.then(
				function (response) {
					console.log(response);
					this.setState({
						addButton: 'Add',
					});
				}.bind(this)
			);
	}

	handleOnChange = () => {};

	handleOnSubmit = (e) => {
		e.preventDefault();
		this.setState({
			addButton: 'Adding...',
		});

		const headers = {
			'X-WP-NONCE': `${this.props.wp_localize_script.nonce}`,
		};

		fetch(this.state.url, {
			headers,
		})
			.then((response) => response.json())
			.then(
				function (response) {
					// console.log(response);
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
