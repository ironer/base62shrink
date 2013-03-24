<?php

if (!empty($_POST['b62s']) && is_string($_POST['b62s'])) $request = $_POST['b62s'];
else if (!empty($_GET['b62s']) && is_string($_GET['b62s'])) $request = $_GET['b62s'];
else $request = '';

$memory = memory_get_peak_usage(); $time = microtime(TRUE);
if ($request) { require_once('Base62Shrink.php'); $request = Base62Shrink::decompress($request); }
$time = microtime(TRUE) - $time; $memory = memory_get_peak_usage() - $memory;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>base62shrink</title>
	<script type="text/javascript" src="b62s.js"></script>
	<style>body { background: #eee; } .readonly { background: #ddd; }</style>
</head>
<body>
	<strong>Deshrinked in PHP</strong> length: <i id="paLen"></i> /
	time: <i><?=number_format($time * 1000, 0, '.', ' ')?> ms</i> /
	used memory: <i><?=number_format($memory / 1024, 0, '.', ' ')?> kB</i><br>
	<textarea id="phparea" class="readonly" rows="5" cols="100" readonly><?=htmlspecialchars($request)?></textarea>
	<br><br>
	<strong>InputArea</strong> length: <i id="iaLen"></i><br>
	<textarea id="inputarea" rows="5" cols="100"></textarea>
	<br><br>
	<strong>encodeURI</strong> length: <i id="uaLen"></i><br>
	<textarea id="uriarea" class="readonly" rows="5" cols="100" readonly></textarea>
	<br><br>
	<form action="<?=$_SERVER["PHP_SELF"]?>" method="post">
		<strong>Base62Shrink</strong> length: <i id="oaLen"></i>
		<input type="submit" value="Send by GET" onmousedown="this.parentNode.method='get'">
		<input type="submit" value="Send by POST" onmousedown="this.parentNode.method='post'"><br>
		<textarea name="b62s" id="outputarea" class="readonly" rows="5" cols="100" readonly></textarea>
		<br><br>
	</form>
	<strong>Deshrinked in javascript</strong> length: <i id="caLen"></i><br>
	<textarea id="checkarea" class="readonly" rows="5" cols="100" readonly></textarea>

	<script>
		var areas = {};
		areas.phparea = document.getElementById('phparea');
		areas.inputarea = document.getElementById('inputarea');
		areas.uriarea = document.getElementById('uriarea');
		areas.outputarea = document.getElementById('outputarea');
		areas.checkarea = document.getElementById('checkarea');
		areas.paLen = document.getElementById('paLen');
		areas.iaLen = document.getElementById('iaLen');
		areas.uaLen = document.getElementById('uaLen');
		areas.oaLen = document.getElementById('oaLen');
		areas.caLen = document.getElementById('caLen');

		areas.refresh = function() {
			areas.paLen.innerHTML = areas.formNum(areas.phparea.value.length);
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
		areas.inputarea.value = areas.inputarea.value || areas.phparea.value;
		areas.refresh();
	</script>
</body>
</html>
