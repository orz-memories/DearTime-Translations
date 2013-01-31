<?php
$log = "";
function mylog($m) {
	global $log;
	$log .= $m . "\n";
}

function loadphp($file)
{
	$content = file_get_contents($file);
	$result = array();
	preg_match_all("/^\\s*(['\"])([A-Za-z0-9_]*?)\\1\\s*=>\\s*(['\"])(.*?)\\3,?\\s*$/", $content, $r, PREG_SET_ORDER);
	foreach ($r as $i)
		$result[$i[1]] = $i[2] . $i[3] . $i[2];
	return $result;
}

function dumpphp($file, $list)
{
	$result = "";
	$result .= "<?php" . "\n";
	$result .= "return array(" . "\n";
	$keys = array_keys($list);
	sort($keys);
	foreach ($keys as $k)
		$result .= "\t'" . $k . "' => " . $list[$k] . ",\n";
	$result .= ");";
	file_put_contents($file, $result);
}

mylog("Bot: rewriting");
$origin = loadphp("zh-cn.php");

foreach (glob("*.php") as $file) {
	$list = loadphp($file);
	$result = array();
	foreach(array_diff(array_keys($list), array_keys($origin)) as $k)
		mylog("invalid key in " . $file. ": \"" . $k . "\" => " . $list[$k]);
	foreach(array_keys($origin) as $k)
	{
		if (array_key_exists($k, $list))
			$result[$k] = $list[$k];
		else
		{
			mylog("not found in " . $file . ": \"" . $k . "\". using origin.");
			$result[$k] = $origin[$k];
		}
	}
	dumpphp($file, $result);
}