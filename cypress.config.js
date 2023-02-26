module.exports = {
	baseUrl: 'http://localhost:3000',
	viewportWidth: 1280,
	viewportHeight: 720,
	defaultCommandTimeout: 5000,
	video: false,
	screenshotsFolder: 'cypress/screenshots',
	integrationFolder: 'cypress/integration',
	pluginsFile: 'cypress/plugins/index.js',
	supportFile: 'cypress/support/index.js',
	testFiles: '**/*.spec.js',
	ignoreTestFiles: '**/node_modules/**',
};
