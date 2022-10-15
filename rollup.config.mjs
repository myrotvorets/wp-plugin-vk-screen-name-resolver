import babel from '@rollup/plugin-babel';
import { terser } from '@wwa/rollup-plugin-terser';

export default (async () => ({
	input: 'assets/vksnr.ts',
	output: {
		file: 'assets/vksnr.min.js',
		format: 'iife',
		plugins: [
			terser(),
		],
		compact: true,
		sourcemap: 'hidden',
		strict: false,
	},
	plugins: [
		babel({
			babelHelpers: 'bundled',
			exclude: 'node_modules/**',
			extensions: ['.js', '.ts', '.mjs']
		}),
	],
	strictDeprecations: true,
}))();
