var b62s = {'initialized': false};

b62s.compress = function(text) {
	b62s.initialized || b62s._init();
	return text ? b62s._deltaShrinkToBase8(b62s._compressLZW(b62s._encodeUtf8(text))) : '';
};

b62s.decompress = function(base8) {
	b62s.initialized || b62s._init();
	return base8 ? b62s._decodeUtf8(b62s._decompressLZW(b62s._deltaUnshrinkFromBase8(base8))) : '';
};

b62s.base8To32k = function(base8) {
	if (!base8) return '';
	var add = (5 - (base8.length + 1) % 5) % 5;
	base8 = add + base8;
	while (add-- > 0) base8 += '0';

	for (var base32k = '', i = 0, j = base8.length, k; i < j; i += 5) {
		k = parseInt((base8.substr(i, 5)), 8) + 13312;
		if (k > 19893) k += (k > 40795) ? 16548 : 74;
		base32k += String.fromCharCode(k);
	}
	return base32k;
};

b62s.base32kTo8 = function(base32k) {
	if (!base32k) return '';
	for (var base8 = '', i = 0, j = base32k.length, k; i < j; ++i) {
		k = base32k.charCodeAt(i) - 13312;
		if (k > 6581) k -= (k > 27557) ? 16548 : 74;
		k = (~~k).toString(8);
		while (k.length < 5) k = '0' + k;
		base8 += k;
	}
	return base8[0] === '0' ? base8.slice(1) : base8.slice(1, -(~~base8[0]));
};

b62s.base8To62 = function(base8) {
	if (!base8) return '';
	b62s.initialized || b62s._init();
	base8 = base8.length % 2 ? '0' + base8 : '1' + base8 + '0';
	for (var base62 = '', i = 0, j = base8.length; i < j; i += 2) base62 += b62s._b62array[base8.substr(i, 2)];
	return base62;
};

b62s.base62To8 = function(base62) {
	if (!base62) return '';
	b62s.initialized || b62s._init();
	for (var base8 = '', i = 0, j = base62.length; i < j; ++i) base8 += b62s._b62object[base62[i]];
	return base8[0] === '0' ? base8.slice(1) : base8.slice(1, -1);
};

b62s._init = function() {
	var i, b62string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	b62s._b62array = {};
	b62s._b62object = {};
	for (i = 0; i < 62; ++i) b62s._b62array[b62s._b62object[b62string[i]] = (07 & i >> 3) + '' + (07 & i)] = b62string[i];
	b62s._LZWarray = [];
	b62s._LZWobject = {};
	for (i = 0; i < 256; ++i) b62s._LZWobject[b62s._LZWarray[i] = String.fromCharCode(i)] = i;
	b62s.initialized = true;
};

b62s._deltaShrinkToBase8 = function(intArray) {
	for (var compArray = [], i = 0, j = intArray.length, k = 0, l; i < j; k = l) compArray.push(((l = intArray[i++]) - k).toString(6));
	return compArray.join('6').replace(/6-/g, '7');
};

b62s._deltaUnshrinkFromBase8 = function(base8) {
	var compArray = base8.replace(/7/g, '6-').split('6');
	for (var intArray = [], i = 0, j = compArray.length, k = 0, l; i < j; k = l) intArray.push(l = (k + parseInt(compArray[i++], 6)));
	return intArray;
};

b62s._compressLZW = function(text) {
	"use strict";
	var i, j, dict = JSON.parse(JSON.stringify(b62s._LZWobject)), c, wc, w = "", result = [], dictSize = 256;

	for (i = 0, j = text.length; i < j; ++i) {
		c = text.charAt(i);
		wc = w + c;
		if (dict.hasOwnProperty(wc)) w = wc;
		else {
			result.push(dict[w]);
			dict[wc] = dictSize++;
			w = String(c);
		}
	}

	if (w !== "") result.push(dict[w]);
	return result;
};

b62s._decompressLZW = function(compressed) {
	"use strict";
	var i, j, dict = JSON.parse(JSON.stringify(b62s._LZWarray)), w, result, k, entry, dictSize = 256;

	result = w = String.fromCharCode(compressed[0]);
	for (i = 1, j = compressed.length; i < j; ++i) {
		k = compressed[i];
		if (dict[k]) entry = dict[k];
		else if (k === dictSize) entry = w + w.charAt(0);
		else return null;

		dict[dictSize++] = w + entry.charAt(0);
		result += w = entry;
	}
	return result;
};

b62s._encodeUtf8 = function(text) {
	return unescape(encodeURIComponent(text));
};

b62s._decodeUtf8 = function(text) {
	return decodeURIComponent(escape(text));
};