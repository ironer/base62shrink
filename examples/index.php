<?php

if (!empty($_POST['b62s']) && is_string($_POST['b62s'])) $request = $_POST['b62s'];
else if (!empty($_GET['b62s']) && is_string($_GET['b62s'])) $request = $_GET['b62s'];
else $request = '';

$memory = memory_get_peak_usage(); $time = microtime(TRUE);
if ($request) { require_once('../Base62Shrink/Base62Shrink.php'); $request = Base62Shrink::decompress($request); }
$time = microtime(TRUE) - $time; $memory = memory_get_peak_usage() - $memory;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>base62shrink</title>
	<script>
	<? readfile(__DIR__ . '/../Base62Shrink/b62s.js'); ?>
	</script>
	<style>body { background: #eee; } label { font-weight: bold; } .pad { height: 10px; } .readonly { background: #ddd; }</style>
</head>
<body>
	<label for="phpArea">Deshrinked in PHP from base62</label> length: <i id="paLen"></i> /
	time: <i><?=number_format($time * 1000, 0, '.', ' ')?> ms</i> /
	used memory: <i><?=number_format($memory / 1024, 0, '.', ' ')?> kB</i><br>
	<textarea id="phpArea" class="readonly" rows="3" cols="100" readonly></textarea>

	<div class="pad"></div>
	<label for="inputArea">InputArea</label> length: <i id="iaLen"></i><br>
	<textarea id="inputArea" rows="3" cols="100"></textarea>

	<div class="pad"></div>
	<label for="uriArea">encodeURI</label> length: <i id="uaLen"></i><br>
	<textarea id="uriArea" class="readonly" rows="3" cols="100" readonly></textarea>
	
	<div class="pad"></div>
	<form id="base62Form" action="" method="post">
		<label for="base62Area">Base62Shrink</label> length: <i id="b62aLen"></i>
		<input type="submit" value="Send by GET" onmousedown="this.parentNode.method='get'">
		<input type="submit" value="Send by POST" onmousedown="this.parentNode.method='post'"><br>
		<textarea name="b62s" id="base62Area" class="readonly" rows="3" cols="100" readonly></textarea>
	</form>

	<div class="pad"></div>
	<label for="check62Area">Deshrinked in js from base62</label> length: <i id="c62aLen"></i><br>
	<textarea id="check62Area" class="readonly" rows="3" cols="100" readonly></textarea>

	<div class="pad"></div>
	<label for="base32kArea">Base32kShrink</label> length: <i id="b32kaLen"></i><br>
	<textarea id="base32kArea" class="readonly" rows="3" cols="100" readonly></textarea>

	<div class="pad"></div>
	<label for="check32kArea">Deshrinked in js from base32k</label> length: <i id="c32kaLen"></i><br>
	<textarea id="check32kArea" class="readonly" rows="3" cols="100" readonly></textarea>

	<script>
		document.getElementById('base62Form').action = window.location.protocol + '//' + window.location.host + window.location.pathname;

		var areas = {};
		areas.phpArea = document.getElementById('phpArea');
		areas.inputArea = document.getElementById('inputArea');
		areas.uriArea = document.getElementById('uriArea');
		areas.base62Area = document.getElementById('base62Area');
		areas.check62Area = document.getElementById('check62Area');
		areas.base32kArea = document.getElementById('base32kArea');
		areas.check32kArea = document.getElementById('check32kArea');

		areas.paLen = document.getElementById('paLen');
		areas.iaLen = document.getElementById('iaLen');
		areas.uaLen = document.getElementById('uaLen');
		areas.b62aLen = document.getElementById('b62aLen');
		areas.c62aLen = document.getElementById('c62aLen');
		areas.b32kaLen = document.getElementById('b32kaLen');
		areas.c32kaLen = document.getElementById('c32kaLen');

		areas.refresh = function() {
			areas.paLen.innerHTML = areas.formNum(areas.phpArea.value.length);
			var input = areas.inputArea.value;
			areas.iaLen.innerHTML = areas.formNum(input.length);

			var startTime = new Date().getTime(), uriEncoded = encodeURI(input), endTime = new Date().getTime();
			areas.uriArea.value = uriEncoded;
			areas.uaLen.innerHTML = areas.formNum(uriEncoded.length) + ' / ' + areas.formNum(endTime - startTime) + ' ms';

			startTime = new Date().getTime();
			var base8 = b62s.compress(input), compressTime = new Date().getTime() - startTime;
			var base62 = b62s.base8To62(base8);
			endTime = new Date().getTime();

			areas.base62Area.value = base62;
			areas.b62aLen.innerHTML = areas.formNum(base62.length) + ' / ' + areas.formNum(endTime - startTime) + ' ms';

			startTime = new Date().getTime();
			var check62 = b62s.decompress(b62s.base62To8(base62));
			endTime = new Date().getTime();

			areas.check62Area.value = check62;
			areas.c62aLen.innerHTML = areas.formNum(check62.length) + ' / ' + areas.formNum(endTime - startTime) + ' ms';

			startTime = new Date().getTime();
			var base32k = b62s.base8To32k(base8);
			endTime = new Date().getTime();

			areas.base32kArea.value = base32k;
			areas.b32kaLen.innerHTML = areas.formNum(base32k.length) + ' / ' + areas.formNum(endTime - startTime + compressTime) + ' ms';

			startTime = new Date().getTime();
			var check32k = b62s.decompress(b62s.base32kTo8(base32k));
			endTime = new Date().getTime();

			areas.check32kArea.value = check32k;
			areas.c32kaLen.innerHTML = areas.formNum(check32k.length) + ' / ' + areas.formNum(endTime - startTime) + ' ms';
		};

		areas.formNum = function(num) {
			return ('' + num).split('').reverse().join('').replace(/(.{3})(?=.)/g, '$1 ').split('').reverse().join('');
		};

		areas.inputArea.onkeyup = areas.refresh;
		areas.phpArea.value = <?=json_encode($request)?>;
		areas.inputArea.value = areas.inputArea.value || areas.phpArea.value;
		areas.refresh();
	</script>
</body>
</html>
