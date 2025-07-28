const wordpressConfig = require("@wordpress/scripts/config/webpack.config");
const CopyWebpackPlugin = require("copy-webpack-plugin");

module.exports = {
	...wordpressConfig,
	plugins: [
		...wordpressConfig.plugins,
		new CopyWebpackPlugin({
			patterns: [
				{ from: "**/*.twig", context: process.env.WP_SOURCE_PATH },
				{ from: "**/*.php", context: process.env.WP_SOURCE_PATH }
			],
		}),
	],
};
