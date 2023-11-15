<?php
session_start();
header("Content-Type: image/png");


for($i=0;$i<count($_SESSION['grafic_data']);$i++) 
{
	$datalist[$i] = $_SESSION['grafic_data'][$i];
	$mxznach[] = max($_SESSION['grafic_data'][$i]);
	$mnznach[] = min($_SESSION['grafic_data'][$i]);

}
/*
$fp = fopen("file.txt", "w");
fwrite($fp, count($mxznach));
fclose($fp);
*/

    $x = 1000; 
 //$x = $_SESSION['grafic_x']; 
    $y = 600; 
    $img = ImageCreate ($x, $y)  
            or die ("Ошибка при создании изображения");          
    $fon = ImageColorAllocate ($img, 221, 238, 238);  //цвет фона
    $linecolor = ImageColorAllocate ($img, 0, 0, 0);   //цвет линий
    $textcolor = ImageColorAllocate ($img, 0, 0, 0);  //цвет текста
$stolbikcolor[] = ImageColorAllocate ($img, 0, 204, 51);   //цвет столбиков
$stolbikcolor[] = ImageColorAllocate ($img, 255, 0, 0);   //цвет столбиков
$stolbikcolor[] = ImageColorAllocate ($img, 0, 51, 204);   //цвет столбиков
$stolbikcolor[] = ImageColorAllocate ($img, 204, 153, 0);   //цвет столбиков
$stolbikcolor[] = ImageColorAllocate ($img, 204, 51, 204);   //цвет столбиков

$otd_name = array('31 отд.','32 отд.','33 отд.','34 отд.','35 отд.');
for($jj=0;$jj<5;$jj++)
{
$legend_move = 70;
$legend_start = 40;
ImageFilledRectangle ($img, 0 + $legend_start + $legend_move*$jj, 0, 10 + $legend_start + $legend_move*$jj, 10, $stolbikcolor[$jj]);
imagettftext ($img, 10, 0, 14 + $legend_start + $legend_move*$jj, 10, $textcolor, $_SERVER['DOCUMENT_ROOT'] .'/kk3project/include/arial.ttf', $otd_name[$jj]);
}

$x_otstup = 35;
$nomer = 10;
//$y_otstup = 10;
$h_max = max($mxznach) + (min($mnznach) < 0 ? -min($mnznach) : 0);

$count_label_y_p = round(max($mxznach)/$nomer) + 1; //число засечек
$count_label_y_m = (min($mnznach) < 0 ? (round(-min($mnznach)/$nomer)+1) : 2); //число засечек
$count_label_y = $count_label_y_p + $count_label_y_m; //число засечек
$len_label = 2;  //длина засечка (2х)
$smeshenie_y = 25;
$h_label = ($y-$smeshenie_y*2)/$count_label_y; //высота шкалы деления
$y0 = ($count_label_y_p * $h_label) + $smeshenie_y;
$x0 = $x_otstup;
$shag = ($x-$x0-20)/12;


// ось x
ImageLine ($img, $x0, $y0, $x, $y0, $linecolor);
// ось y
ImageLine ($img, $x0, 0, $x0, $y, $linecolor); 


for ($i=(-$count_label_y_p);$i<$count_label_y_m;$i++)
{
ImageLine ($img, $x0-$len_label, $y0+($h_label*$i), $x0+$len_label, $y0+($h_label*$i), $linecolor);
//if($i!=(-$count_label_y_p)) 
ImageString ($img, 3, $x0-35, $y0-7+($h_label*$i), -$i*$nomer ."%", $textcolor); 
}
 //x
for($i=1;$i<=12;$i++)
{
ImageLine ($img, $x0+$i*$shag, $y0-$len_label, $x0+$i*$shag, $y0+$len_label, $linecolor); 
ImageString ($img, 3, $x0+$i*$shag, $y0+5, $i, $textcolor);
}


	$k_range = $h_label/$nomer;
	$n_max = count($datalist);
	$border1 = 3; //$shag/10;
	$mini_shag = $shag - $border1* 2;
for ($i=0; $i<12; $i++) 
{ 
	for($n=0;$n<$n_max;$n++)
	{
		$x1[$n] = $x0+$border1 + $shag*$i + ($mini_shag/$n_max)*$n;
		$y1[$n] = $y0;
		$x2[$n] = $x0+$border1 + $shag*$i + $mini_shag/($n_max) + ($mini_shag/$n_max)*$n;
		$y2[$n] = $y0 - $datalist[$n][$i]*$k_range; //высота

		ImageFilledRectangle ($img, ($x1[$n]+1), $y1[$n], ($x2[$n]-1), $y2[$n], $stolbikcolor[$n]);
		//ImageString ($img, 0, $x1[$n], $y2[$n]- ($datalist[$n][$i] > 0 ? 10 : -3), $datalist[$n][$i] ."%", $textcolor);
		imagestringup  ($img, 0, $x1[$n]+4, $y2[$n]- ($datalist[$n][$i] > 0 ? 4 : -40), $datalist[$n][$i] ."%", $textcolor);
	}
} 

//ImageString ($img, 3, 50, 20, max($mxznach), $textcolor);
//ImageString ($img, 3, 50, 35, min($mnznach), $textcolor);
//ImageString ($img, 3, 50, 50, $h_max, $textcolor);
//header ("Content-type: image/png"); 
ImagePng ($img);   
imagedestroy($img);
?>