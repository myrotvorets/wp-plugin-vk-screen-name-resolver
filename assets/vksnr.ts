import type apiFetch from '@wordpress/api-fetch';

declare global {
	// eslint-disable-next-line sonarjs/class-name
	interface wp { // NOSONAR
		apiFetch: typeof apiFetch;
	}

	interface Window {
		wp: wp;
	}
}

interface ErrorResponse {
	code: string;
	message: string;
}

interface ApiResponse {
	id: string;
	type: string;
}

declare const wp: wp;

const typeMap = new Map<string, string>( [
	[ 'user', 'id' ],
	[ 'group', 'club' ],
	[ 'application', 'app' ],
] );

const callback = (): void => {
	const src = document.getElementById( 'vk-screen-name' ) as HTMLInputElement | null;
	const dst = document.getElementById( 'vk-screen-name-result' ) as HTMLInputElement | null;
	const btn = document.getElementById( 'vk-screen-name-resolve' ) as HTMLInputElement | null;

	if ( src && dst && btn ) {
		btn.addEventListener( 'click', () => {
			if ( ! btn.hasAttribute( 'aria-disabled' ) ) {
				const name = src.value.replace( /^(https?:\/\/(m|new\.)?vk\.com)?\/?/iu, '' );
				dst.value = '';
				btn.setAttribute( 'aria-disabled', 'true' );

				void wp.apiFetch<ApiResponse|ErrorResponse>( { path: `vksnr/v1/resolve/${ name }` } )
					.then( ( r ) => {
						if ( 'message' in r ) {
							dst.value = r.message;
						} else {
							const { id, type } = r;
							dst.value = `https://vk.com/${ typeMap.get( type ) ?? '' }${ id }`;
						}
					} ).catch( ( e: unknown ) => {
						dst.value = ( e as Error | ErrorResponse ).message;
					} )
					.then( () => btn.removeAttribute( 'aria-disabled' ) );
			}
		} );
	}
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', callback );
} else {
	callback();
}
