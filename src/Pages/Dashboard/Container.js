import { Component } from 'react';
import Presentation from './Presentation';

class ContainerComponent extends Component {
	constructor(props) {
		super(props);
	}

	componentDidMount() {}

	handleOnChange = () => {};

	handleOnSubmit = () => {};

	render() {
		return <Presentation {...this.props} />;
	}
}

export default ContainerComponent;
