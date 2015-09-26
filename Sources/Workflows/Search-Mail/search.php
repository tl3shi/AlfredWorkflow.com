<?php

include('extension_utils.php');

$utils = new ExtensionUtils();

$query = $argv[1];

if ( strlen( $query ) < 3 ):
	exit(1);
endif;

$home = exec('printf $HOME');
$maildir = "$home/Library/Mail/V2";


$results = $utils->local_find(
	"(kMDItemContentType == 'com.apple.mail.emlx') && (kMDItemSubject == '*".$query."*'c || kMDItemAuthors == '*".$query."*'c || kMDItemAuthorEmailAddresses == '*".$query."*'c)"
);

$results = array_slice($results, 0, 15);

$output = array();

foreach( $results as $k => $v ):

	exec("mdls -name kMDItemSubject -raw '$v'", $title);
	exec("mdls -name kMDItemAuthors -raw '$v'", $subtitle);

	$subtitle = trim( str_replace( "\"", "" , $subtitle[1] ) );
	$utf8sub = preg_replace("#\\\u([0-9a-f]+)#ie","iconv('UCS-2','UTF-8', pack('H4', '\\1'))", $subtitle);
	$temp = array(
		'title' => $title[0],
		'subtitle' => "From: ". $utf8sub ,
		'icon' => 'icon.png',
		'uid' => '',
		'arg' => $v
	);

	array_push($output, $temp);

	unset($title, $subtitle);

endforeach;

echo $utils->arrayToXML( $output );