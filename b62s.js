var b62s = {};

b62s.compress = function(text) {
	b62s._bd || b62s._init();
	return text ? b62s._base8To62(b62s._deltaShrinkToBase8(b62s._compressLZW(b62s._encodeUtf8(text)))) : '';
};

b62s.decompress = function(compressed) {
	b62s._bd || b62s._init();
	return compressed ? b62s._decodeUtf8(b62s._decompressLZW(b62s._deltaUnshrinkFromBase8(b62s._base62To8(compressed)))) : '';
};

b62s._init = function() {
	var i;
	b62s._b62array = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.split('');
	b62s._b62object = {};
	for (i = b62s._b62array.length; i-- > 0;) b62s._b62object[b62s._b62array[i]] = i;
	b62s._LZWarray = [];
	b62s._LZWobject = {};
	for (i = 0; i < 256; ++i) b62s._LZWobject[b62s._LZWarray[i] = String.fromCharCode(i)] = i;
	b62s._bd = true;
};

b62s._base8To62 = function(base8) {
	base8 = base8.length % 2 ? '1' + base8 : '0' + base8 + '0';
	for (var base62 = '', i = 0, j = base8.length; ++i < j; ++i) base62 += b62s._b62array[parseInt((base8.substr(i - 1, 2)), 8)];
	return base62;
};

b62s._base62To8 = function(base62) {
	for (var base8 = '', i = 0, j = base62.length, k; i < j; ++i) {
		base8 += (k = parseInt(b62s._b62object[base62[i]]).toString(8))[1] ? k[0] + k[1] : '0' + k[0];
	}
	return base8[0] === '1' ? base8.slice(1) : base8.slice(1, -1);
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