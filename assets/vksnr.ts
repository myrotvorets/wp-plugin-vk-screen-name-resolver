import type apiFetch from '@wordpress/api-fetch';

declare global {
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

const typeMap = new Map<string, string>([
	['user', 'id'],
	['group', 'club'],
	['application', 'app'],
]);

const callback = () => {
	const src = document.getElementById('vk-screen-name') as HTMLInputElement | null;
	const dst = document.getElementById('vk-screen-name-result') as HTMLInputElement | null;
	const btn = document.getElementById('vk-screen-name-resolve') as HTMLInputElement | null;

	if (src && dst && btn) {
		btn.addEventListener('click', () => {
			if (!btn.hasAttribute('aria-disabled')) {
				const name = src.value.replace(/^(https?:\/\/(m|new\.)?vk\.com)?\/?/iu, '');
				dst.value  = '';
				btn.setAttribute('aria-disabled', 'true');

				wp.apiFetch<ApiResponse|ErrorResponse>({ path: `vksnr/v1/resolve/${name}` })
					.then((r) => {
						if ('message' in r) {
							dst.value = r.message;
						} else {
							const { id, type } = r;
							dst.value = 'https://vk.com/' + (typeMap.get(type) || '') + id;
						}
					}).catch((e: Error | ErrorResponse) => {
						dst.value = e.message;
					})
					.then(() => btn.removeAttribute('aria-disabled'))
			}
		});
	}
};

document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', callback) : callback();
