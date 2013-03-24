<?php
require_once('Base62Shrink.php');
$request = '';

if (!empty($_GET['b62s'])) {
	$request = Base62Shrink::decompress($_GET['b62s']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>base62shrink</title>
	<script type="text/javascript" src="b62s.js"></script>
</head>
<body>
	<strong>Deshrinked in PHP</strong> length: <i><?=number_format(mb_strlen($request), 0, '.', ' ')?></i><br>
	<textarea rows="5" cols="100" readonly><?=htmlspecialchars($request)?></textarea>
	<br><br>
	<strong>InputArea</strong> length: <i id="iaLen"></i><br>
	<textarea id="inputarea" rows="5" cols="100"></textarea>
	<br><br>
	<strong>test (encodeURI)</strong> length: <i id="uaLen"></i><br>
	<textarea id="uriarea" rows="5" cols="100" readonly></textarea>
	<br><br>
	<strong>base62shrinked</strong> length: <i id="oaLen"></i><br>
	<textarea id="outputarea" rows="5" cols="100" readonly></textarea>
	<br><br>
	<strong>deshrinked back ;-)</strong> length: <i id="caLen"></i><br>
	<textarea id="checkarea" rows="5" cols="100" readonly></textarea>

	<script>
		var areas = {};
		areas.inputarea = document.getElementById('inputarea');
		areas.uriarea = document.getElementById('uriarea');
		areas.outputarea = document.getElementById('outputarea');
		areas.checkarea = document.getElementById('checkarea');
		areas.iaLen = document.getElementById('iaLen');
		areas.uaLen = document.getElementById('uaLen');
		areas.oaLen = document.getElementById('oaLen');
		areas.caLen = document.getElementById('caLen');

		areas.refresh = function() {
			var input = areas.inputarea.value;
			areas.iaLen.innerHTML = areas.formNum(input.length);
			var uri = encodeURI(input);
			areas.uriarea.value = uri;
			areas.uaLen.innerHTML = areas.formNum(uri.length);
			var shrinked = b62s.compress(input);
			areas.outputarea.value = shrinked;
			areas.oaLen.innerHTML = areas.formNum(shrinked.length);
			var deshrinked = b62s.decompress(shrinked);
			areas.checkarea.value = deshrinked;
			areas.caLen.innerHTML = areas.formNum(deshrinked.length);
		};

		areas.formNum = function(num) {
			return ('' + num).split('').reverse().join('').replace(/(.{3})(?=.)/g, '$1 ').split('').reverse().join('');
		};

		areas.inputarea.onkeyup = areas.refresh;
		areas.refresh();
	</script>
</body>
</html>
