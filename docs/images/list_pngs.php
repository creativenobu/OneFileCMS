OneFileCMS/images/<br>

<style>
body {font-family: courier;}
table    { border: 1px solid gray; border-collapse: collapse;}
table td { border: 0px solid gray; padding: .1em; }
.name    { padding-left: .2em; padding-right: 2em; }
</style>


<?php 

$all_files = glob("*", GLOB_BRACE);sort($all_files);
//$all_files = glob($varvar."{,.}*", GLOB_BRACE); sort($all_files);
$fc_files = count($all_files);
$hc_files = round($fc_files / 2);



 $x=0;
for ($fc = 0; $fc <= $fc_files; $fc++ ){ 
	if ( (substr($all_files[$fc], -3) == "png") ) { // || () || () ) 
		$png_files[$x++] = $all_files[$fc];
	}//end if
}//end for
$fc_png = count($png_files);
$hc_png = round($fc_png / 2);

//echo '<pre>';
//echo $fc_png.' | '.$hc_png.'<br>';
//echo print_r($png_files);
//echo '</pre>';


echo "\n<table>\n";
for ($x = 0; $x < $hc_png; $x++) {
	echo '<tr><td><img src="'.$png_files[$x].'"></td><td class=name>'.$png_files[$x]."</td>\n";
	$img_src = $png_files[$x+$hc_png];
	if ( file_exists($img_src) ) {
		echo '    <td><img src="'.$img_src.'"></td><td class=name>'.$img_src."</td></tr>\n";
	}else{
		echo "<td></td><td></td></tr>\n";
	}//end if
}//end for $x
echo "</table>\n";


/*************************
echo "<table>";
foreach ($all_files as $file) {
	$fc++;
	$ext = substr($file,-3);
	if ( ($ext == "png") || ($ext == "gif") || ($ext == "png") ) {
		echo '<tr><td><img src="'.$file.'"></td><td class=name>'.$file.'</td>';
		echo '    <td><img src="'.$file.'"></td><td class=name>'.$file.'</td></tr>';
	}
}//end foreach file
echo '</table><hr>';
*************************/
?>
