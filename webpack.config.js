const path = require('path');
const webpack = require('webpack');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const VersionFile = require('webpack-version-file-plugin');
const WebpackShellPluginNext = require('webpack-shell-plugin-next');

const REACTJS_DIR = path.resolve(__dirname, 'src');
const BUILD_DIR = path.resolve(__dirname, 'build');

const GIT_FILE = path.join(BUILD_DIR, 'git.json');

module.exports = (env, argv) => {
	console.log(argv);

	const mode = argv.mode || 'development';

	return {
		mode: mode,
		watch: mode === 'production' ? false : true,
		devtool: 'cheap-module-source-map',
		entry: path.join(REACTJS_DIR, 'index.js'),
		output: {
			path: BUILD_DIR,
			filename: 'bundle.js',
		},
		optimization: {
			minimize: true,
			nodeEnv: mode,
			chunkIds: mode === 'development' ? 'named' : 'deterministic',
			concatenateModules: true,
			innerGraph: true,
			mangleExports: true,
			providedExports: false,
			mergeDuplicateChunks: true,
			minimizer: [
				new CssMinimizerPlugin(),
				new TerserPlugin({
					parallel: true,
				}),
			],
		},
		module: {
			rules: [
				{
					test: /\.(jsx|js)$/,
					include: REACTJS_DIR,
					exclude: /node_modules/,
					use: [
						{
							loader: 'babel-loader',
							options: {
								presets: ['@babel/preset-env', '@babel/preset-react'],
							},
						},
					],
				},
			],
		},
		plugins: [
			new webpack.HotModuleReplacementPlugin(),
			new webpack.ids.DeterministicChunkIdsPlugin({
				maxLength: 5,
			}),
			new WebpackShellPluginNext({
				onBuildStart: {
					scripts: [
						`echo "{" > ${GIT_FILE}`,
						`echo "\\"branch\\": \\"$(git name-rev --name-only HEAD)\\"," >> ${GIT_FILE}`,
						`echo "\\"commits\\": \\"$(git rev-list HEAD --count)\\"," >> ${GIT_FILE}`,
						`echo "\\"hash\\": \\"$(git rev-parse HEAD)\\"" >> ${GIT_FILE}`,
						`echo "}" >> ${GIT_FILE}`,
					],
					blocking: true,
					parallel: false,
				},
				onBuildEnd: {
					scripts: [],
					blocking: true,
					parallel: false,
				},
			}),
			new VersionFile({
				packageFile: path.join(__dirname, 'package.json'),
				outputFile: path.join(BUILD_DIR, 'version.json'),
				data: {
					date: new Date(),
					environment: env,
				},
				extras: {
					timestamp: Date.now(),
				},
			}),
		],
	};
};
