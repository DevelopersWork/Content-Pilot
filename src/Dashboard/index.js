/**
 * External dependencies.
 */
import React from 'react';
import * as WPElement from '@wordpress/element';

/**
 * Internal dependencies.
 */

/**
 * Initial render function.
 */
function render() {
    const container = document.getElementById('dashboard-root');

    if (null === container) {
        return;
    }

    const component = (
        <h1>Content Pilot</h1>
    );
    if (WPElement.createRoot) {
        WPElement.createRoot(container).render(component);
    } else {
        WPElement.render(component, container);
    }
}

render();