<?php
/*
	
	PHPによるスクレイピングの実装例です。バニラのPHPで作動させるため、
	
	https://www.artiencegroup.com/ja/site-policy.html
	このPHPスクリプトで生成出来るデータは著作権により、「著作権法上の私的利用の範囲を超えて使用をすることができません」
	
	PHPでのスクレイピングの助けになれば良いと思います
	
*/

// ファイルの取得
$html = file_get_contents(
	"https://www.artiencegroup.com/ja/products/printing-related/cf/",
	false,
	stream_context_create(array(
		"ssl" => array(
			"verify_peer" => false,
			"verify_peer_name" => false,
		)
	))
);

$flag = preg_match("/<tr>\n<th scope=\"row\">CF10001[\S\s]+?<\/tbody>/", $html, $m);
$html = $m[0];

// 色単位のHTMLの取得。作業効率向上の為の作業
$result = preg_match_all("/<th scope=\"row\">([\S\s]+?#[0-9A-Fa-f]{6})/", $html, $table);
//	print_r($table[0]); exit;
//	echo($result); exit;

// gplファイルのヘッダー作成
$gpl = <<<EOF
GIMP Palette
Name: TOYO COLOR FINDER Swatch
Columns: 8
#

EOF;

// 色単位でのデータを取り出す
foreach ($table[0] as $v) {
	//		echo($v); exit;
	//		if ($v === "") continue;

	// 色のデータの取得
	$flag = preg_match("/background-color: (#[0-9A-Fa-f]{5,6})/", $v, $m);
	//		print_r($m); exit;

	$col = $m[1];
	//		echo($col); exit;

	// 色の名前の取得
	$flag = preg_match("/scope=\"row\">([\S\s]+?)</", $v, $m);
	//		if (! $flag) continue;
	//		if (! $flag) print_r($v); exit;
	//		print_r($m); exit;

	$n = $m[1];

	// 色のデータを１０進数化
	$c = str_split(mb_substr($col, 1), 2);
	//		print_r($c); exit;

	$r = hexdec($c[0]);
	$g = hexdec($c[1]);
	$b = hexdec($c[2]);

	// gplファイルへデータの追加
	$gpl .= $r . "\t" . $g . "\t" . $b . "\t" . $n . "\r\n";
}

// gplファイルの作成
file_put_contents("TOYO COLOR FINDER.gpl", $gpl);

exit;
