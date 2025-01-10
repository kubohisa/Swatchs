<?php
/*
	
	phpによるスクレイピングの実装例です。バニラのPHPで作動させるため、
	
	https://www.artiencegroup.com/ja/products/printing-related/cf/
	のソースリストをChromeブラウザのソースリスト表示機能を使ってファイルをセーブしたファイルを使ってデータを取得してます
	
	https://www.artiencegroup.com/ja/site-policy.html
	このPHPスクリプトで生成出来るデータは著作権により、「著作権法上の私的利用の範囲を超えて使用をすることができません」
	
	phpでのスクレイピングの助けになれば良いと思います
	
*/

// ファイルの取得
$html = file_get_contents("view-source_https___www.artiencegroup.com_ja_products_printing-related_cf_.html");

// 色単位のHTMLの取得。作業効率向上の為の作業
$result = preg_match_all("/scope<\/span>=\"<span class=\"html-attribute-value\">row<\/span>\"&gt;<\/span>([\S\s]+?#[0-9A-Fa-f]{6})/", $html, $table);
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
	$flag = preg_match("/row<\/span>\"&gt;<\/span>([\S\s]+?)<span/", $v, $m);
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
	$gpl .= ($r . "\t" . $g . "\t" . $b . "\t" . $n . "\r\n");
}

// gplファイルの作成
file_put_contents("TOYO COLOR FINDER.gpl", $gpl);

exit;
