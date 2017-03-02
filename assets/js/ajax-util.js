(function ($, factory) {
	'use strict';
	window.cbsiamMetrics = window.cbsiamMetrics || {};
	$.extend(window.cbsiamMetrics, factory());
})(jQuery, function () {
	return {
		sendAjaxRequest: function (url, method, data) {
			let request = new XMLHttpRequest(),
				dataString = '';

			if (data !== undefined) {
				for (let key in data) {
					if (dataString !== '') {
						dataString += '&';
					}
					dataString += encodeURIComponent(key) + '=' +
						encodeURIComponent(data[key]);
				}
				if (method === 'GET') {
					url += '?' + dataString;
				}
			}

			return new Promise((resolve, reject) => {
				request.onreadystatechange = function () {
					if (this.readyState === 4) {
						if (this.status === 200) {
							resolve(JSON.parse(this.responseText));
						} else {
							reject(JSON.parse(this.responseText));
						}
					}
				}

				request.open(method, url, true);
				request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
				if (method === 'GET') {
					request.send();
				} else {
					request.setRequestHeader('Content-type',
						'application/x-www-form-urlencoded');
					request.send(dataString);
				}
			});
		}
	}
});
