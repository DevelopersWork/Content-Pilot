const path = require('path');

const JS_DIR = path.resolve(__dirname, 'assets/js');
const IMG_DIR = path.resolve(__dirname, 'assets/img');
const LIB_DIR = path.resolve(__dirname, 'src/Lib');
const BUILD_DIR = path.resolve(__dirname, 'build');

const entry = {
	editor: JS_DIR + '/editor.js',
};
const output = {
	path: BUILD_DIR,
	filename: 'js/[name].js',
};

const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');

const plugins = (argv) => [
	new CleanWebpackPlugin({
		cleanStaleWebpackAssets: 'production' === argv.mode, // Automatically remove all unused webpack assets on rebuild, when set to true in production. ( https://www.npmjs.com/package/clean-webpack-plugin#options-and-defaults-optional )
	}),

	new MiniCssExtractPlugin({
		filename: 'css/[name].css',
	}),

	// new CopyPlugin({
	// 	patterns: [{ from: LIB_DIR, to: BUILD_DIR + '/library' }],
	// }),

	new DependencyExtractionWebpackPlugin({
		injectPolyfill: true,
		combineAssets: true,
	}),
];

const rules = [
	{
		test: /\.js$/,
		include: [JS_DIR],
		exclude: /node_modules/,
		use: 'babel-loader',
	},
	{
		test: /\.scss$/,
		exclude: /node_modules/,
		use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
	},
	{
		test: /\.(png|jpg|svg|jpeg|gif|ico)$/,
		use: {
			loader: 'file-loader',
			options: {
				name: 'img/[name].[ext]',
				publicPath: 'production' === process.env.NODE_ENV ? '../' : '../../',
			},
		},
	},
	{
		test: /\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
		exclude: [IMG_DIR, /node_modules/],
		use: {
			loader: 'file-loader',
			options: {
				name: '[path][name].[ext]',
				publicPath: 'production' === process.env.NODE_ENV ? '../' : '../../',
			},
		},
	},
];

const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, argv) => ({
	entry: entry,
	output: output,
	devtool: 'source-map',
	module: {
		rules: rules,
	},
	optimization: {
		minimize: true,
		minimizer: [
			new CssMinimizerPlugin(),
			new TerserPlugin({
				parallel: true,
			}),
		],
	},
	plugins: plugins(argv),
	externals: {
		jquery: 'jQuery',
	},
});
