import React from 'react';

import Presentation from './Presentation';

class ContainerComponent extends React.Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidMount() {}

	handleOnChange = () => {};

	handleOnSubmit = () => {};

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
