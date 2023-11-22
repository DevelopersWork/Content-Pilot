/**
 * External Dependencies
 */

/**
 * WordPress Dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

module.exports = {
    ...defaultConfig,
    ...{
        entry: {
            ...defaultConfig.entry
        },
        plugins: [
            ...defaultConfig.plugins,
        ],
        watchOptions: {
            aggregateTimeout: 500,
            poll: 1000, // Check for changes every second
            ignored: /node_modules/,
        }
    }
}