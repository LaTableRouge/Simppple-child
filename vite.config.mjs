import { resolve } from 'path'

import { stringReplaceOpenAndWrite, viteStringReplace } from '@mlnop/string-replace'
import autoprefixer from 'autoprefixer'
import { defineConfig, loadEnv } from 'vite'
import { run } from 'vite-plugin-run'
import sassGlobImports from 'vite-plugin-sass-glob-import'
import { viteStaticCopy } from 'vite-plugin-static-copy'

/*
 |--------------------------------------------------------------------------
 | Global config
 |--------------------------------------------------------------------------
 |
 | Assets path
 | Destination path
 |
 */
const themeName = 'simppple-child'
const assetsPath = 'assets'
const distPath = 'build'

/*
 |--------------------------------------------------------------------------
 | Assets config
 |--------------------------------------------------------------------------
 | {
 |  scripts = [
 |      {
 |        - File name
 |        - File input
 |      }
 |    ]
 |
 |  styles = [
 |      {
 |        - File name
 |        - File input
 |      }
 |    ]
 | }
 |
 */
const entryFiles = [
	{
		scripts: [
			{
				name: 'front',
				input: `${assetsPath}/js`
			},
			{
				name: 'admin',
				input: `${assetsPath}/js`
			},
			{
				name: 'editor',
				input: `${assetsPath}/js`
			},
		],
		styles: [
			{
				name: 'front',
				input: `${assetsPath}/scss`
			},
			{
				name: 'admin',
				input: `${assetsPath}/scss`
			},
			{
				name: 'editor',
				input: `${assetsPath}/scss`
			},
		]
	}
]

/*
 |--------------------------------------------------------------------------
 | Beautify config (lint/prettier files)
 |--------------------------------------------------------------------------
 | {
 |    js|php|scss: {
 |     - Config (string)
 |     - Files (array of strings)
 |    }
 | }
 |
 */
const beautifyObject = {
	js_lint: {
		config: 'npx eslint --no-error-on-unmatched-pattern --fix',
		files: [
			...Array.from(new Set(entryFiles.flatMap(element => element.scripts.flatMap(script => script.input)))),
			'blocks/react/src',
			'blocks/acf',
			'blocks/core',
			'blocks/woocommerce',
			'parts',
			'patterns',
			'templates'
		]
	},
	js_prettier: {
		config: 'npx prettier --no-error-on-unmatched-pattern --write',
		files: [
			...Array.from(new Set(entryFiles.flatMap(element => element.scripts.flatMap(script => script.input)))),
			'blocks/react/src',
			'blocks/acf',
			'blocks/core',
			'blocks/woocommerce',
			'parts',
			'patterns',
			'templates'
		]
	},
	scss_lint: {
		config: 'npx stylelint --allow-empty-input --fix',
		files: [
			...Array.from(new Set(entryFiles.flatMap(element => element.styles.flatMap(style => style.input)))),
			'blocks/react/src',
			'blocks/acf',
			'blocks/core',
			'blocks/woocommerce',
			'parts',
			'patterns',
			'templates'
		]
	},
	scss_prettier: {
		config: 'npx prettier --no-error-on-unmatched-pattern --write',
		files: [
			...Array.from(new Set(entryFiles.flatMap(element => element.styles.flatMap(style => style.input)))),
			'blocks/react/src',
			'blocks/acf',
			'blocks/core',
			'blocks/woocommerce',
			'parts',
			'patterns',
			'templates'
		]
	},
	php_lint: {
		config: `${resolve(__dirname, 'vendor/bin/php-cs-fixer.bat')} fix -v --show-progress=dots --using-cache=no --config=${resolve(__dirname, '.php-cs-fixer.php')}`,
		files: [
			'inc',
			'functions.php',
			'patterns',
			'blocks'
		]
	}
}

/*
 |--------------------------------------------------------------------------
 | Files to edit
 |--------------------------------------------------------------------------
 |  [
 |    {
 |     - File path (array of strings)
 |     - Replace (array)
 |       {
 |        from (regex of string)
 |        to (string)
 |       }
 |    }
 |  ]
 |
 */
const filesToEdit = [
	{
		filePath: [
			resolve(__dirname, 'inc/'),
			resolve(__dirname, 'functions.php')
		],
		replace: [
			{
				from: /\bvar_dump\(([^)]+)\);/g,
				to: ''
			}
		]
	}
]

/*
 |--------------------------------------------------------------------------
 | Copy config
 |--------------------------------------------------------------------------
 |  [
 |    {
 |      - File input (string)
 |      - File output (string)
 |    }
 |  ]
 |
 */
const filesToCopy = [
	{
		src: `${assetsPath}/img`,
		dest: 'assets/'
	}
]

/*
 |--------------------------------------------------------------------------
 |--------------------------------------------------------------------------
 |--------------------------------------------------------------------------
 | That's all, stop editing, happy development
 |--------------------------------------------------------------------------
 |--------------------------------------------------------------------------
 |--------------------------------------------------------------------------
 */

export default defineConfig(async ({ command, mode, isSsrBuild, isPreview }) => {
	const isProduction = command === 'build'

	const env = loadEnv(mode, process.cwd(), '')
	const chore = env?.npm_config_chore

	const entriesToCompile = []
	if (entryFiles.length) {
		entryFiles.forEach(group => {
			if (group) {
				/*
				|--------------------------------------------------------------------------
				| Javascript Compilation
				|--------------------------------------------------------------------------
				|
				| Create array of files to compile
				|
				*/
				if (group.scripts?.length) {
					group.scripts.forEach(file => {
						if (!entriesToCompile.includes(`${file.input}/${file.name}.js`)) {
							entriesToCompile.push(`${file.input}/${file.name}.js`)
						}
					})
				}

				/*
				|--------------------------------------------------------------------------
				| SCSS Compilation
				|--------------------------------------------------------------------------
				|
				| Create array of files to compile
				|
				*/
				if (group.styles?.length) {
					group.styles.forEach(file => {
						if (chore === undefined || chore === 'all' || chore.includes('scss')) {
							if (!entriesToCompile.includes(`${file.input}/${file.name}.scss`)) {
								entriesToCompile.push(`${file.input}/${file.name}.scss`)
							}
						}
					})
				}
			}
		})
	}

	/*
	 |--------------------------------------------------------------------------
	 | Replace in file
	 |--------------------------------------------------------------------------
	 |
	 | Replace string in file
	 | Change vite constant in watch
	 | Change vite constant in build
	 |
	 */
	if (chore !== 'ci') {
		if (isProduction) {
			await stringReplaceOpenAndWrite(
				resolve(__dirname, 'functions.php'),
				[
					{
						from: /\bdefine\([ ]?'SIMPPPLECHILD_IS_VITE_DEVELOPMENT',[ ]?true[ ]?\);/g,
						to: "define('SIMPPPLECHILD_IS_VITE_DEVELOPMENT', false);"
					}
				]
			)
		} else {
			await stringReplaceOpenAndWrite(
				resolve(__dirname, 'functions.php'),
				[
					{
						from: /\bdefine\([ ]?'SIMPPPLECHILD_IS_VITE_DEVELOPMENT',[ ]?false[ ]?\);/g,
						to: "define('SIMPPPLECHILD_IS_VITE_DEVELOPMENT', true);"
					}
				]
			)
		}
	}

	/*
	 |--------------------------------------------------------------------------
	 | Global Vite config
	 |--------------------------------------------------------------------------
	 |
	 | Plugins :
	 |  - Replace in file
	 |  - Enable Sass glob imports
	 |  - Static files copy
	 |  - Run :
	 |    - execute scss lint command
	 |    - execute scss prettier command
	 |    - execute js lint command
	 |    - execute js prettier command
	 |    - execute php lint command
	 | Options :
	 |  - Build
	 |    - minify when production build
	 |    - define build directory
	 |    - empty build dir
	 |  - Server
	 |    - hot reload config
	 |  - CSS
	 |    - autoprefixer when production build
	 |
	 */
	return {
		base: isProduction ? './' : `/wp-content/themes/${themeName}`, // Url to apply refresh
		plugins: [
			{
				...sassGlobImports(),
				enforce: 'pre',
			},
			chore === 'all'
				? {
					...viteStringReplace(filesToEdit),
					apply: 'build',
					enforce: 'pre',
				}
				: false,
			{
				...run({
					silent: false,
					skipDts: true,
					input: chore === 'all'
						? [
							{
								name: 'prettier:scss',
								run: [`${beautifyObject.scss_prettier.config} ${beautifyObject.scss_prettier.files.length > 1 ? `{${beautifyObject.scss_prettier.files.join(',')}}` : beautifyObject.scss_prettier.files.join(',')}/**/*.scss`],
							},
							{
								name: 'lint:scss',
								run: [`${beautifyObject.scss_lint.config} ${beautifyObject.scss_lint.files.length > 1 ? `{${beautifyObject.scss_lint.files.join(',')}}` : beautifyObject.scss_lint.files.join(',')}/**/*.scss`],
							},
							{
								name: 'prettier:js',
								run: [`${beautifyObject.js_prettier.config} ${beautifyObject.js_prettier.files.length > 1 ? `{${beautifyObject.js_prettier.files.join(',')}}` : beautifyObject.js_prettier.files.join(',')}/**/*.js`],
							},
							{
								name: 'lint:js',
								run: [`${beautifyObject.js_lint.config} ${beautifyObject.js_lint.files.length > 1 ? `{${beautifyObject.js_lint.files.join(',')}}` : beautifyObject.js_lint.files.join(',')}/**/*.js`],
							},
							{
								name: 'lint:php',
								run: [`${beautifyObject.php_lint.config} ${beautifyObject.php_lint.files.join(' ')}`],
							}
						]
						: []
				}),
				apply: 'build',
				enforce: 'pre',
			},
			viteStaticCopy({
				targets: filesToCopy
			})
		].filter(Boolean),

		esbuild: isProduction
			? {
				minifyIdentifiers: false,
				keepNames: true,
				pure: ['console.log'],
				reserveProps: /^__\(*$/
			}
			: null,

		build: {
			rollupOptions: {
				input: entriesToCompile,
			},
			write: true,
			minify: isProduction ? 'esbuild' : false,
			outDir: distPath,
			emptyOutDir: true,
			manifest: true,
			sourcemap: !isProduction,
		},

		server: {
			cors: true,
			strictPort: true,
			port: 5173,
			https: false,
			open: false,
			hmr: {
				host: 'localhost'
			},
			watch: {
				usePolling: true
			},
		},

		css: {
			devSourcemap: !isProduction,
			postcss: {
				plugins: [
					autoprefixer
				],
			}
		},

		clearScreen: false
	}
})
