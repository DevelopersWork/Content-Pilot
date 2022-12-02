import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';

document.addEventListener('DOMContentLoaded', function () {
	var element = document.getElementById('dwcp-admin-root');
	if (typeof element !== 'undefined' && element !== null) {
		ReactDOM.render(<App />, document.getElementById('dwcp-admin-root'));
	}
});
