<?php

namespace App\Services\Common;

class ImageService
{
	public function verifyCode($code, $width = 80, $height = 40)
	{
		if (empty($code)) return false;

		//创建画布
		$image = @imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');
    	//填充背景色
    	$bgcolor = imagecolorallocate($image, 255, 255, 255);
    	imagefill($image, 0, 0, $bgcolor);

    	$fontfile = ROOT_PATH . 'public' . DS . 'font' . DS . 'simhei.ttf';
    	// 生成随机码
    	$len = strlen($code);
    	for ($i = 0; $i < $len; $i++) {
		    //设置随机字体颜色
		    $fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));
		    //随机码宽度
		    $fontsize = rand(18, 24);
		    $angle = rand(0, 30);
		    $x = ($i * $width / $len) + 5;
		    //随机码高度
		    $y = rand($fontsize, $height - 5);
		    //填充当前字符入画布
		    imagettftext($image, $fontsize, rand(0, 30), $x, $y, $fontcolor, $fontfile, $code[$i]);
    	}
    	//加入干扰线
	    for($i = 0; $i < 4; $i++) {
	        //设置线的颜色
		    $linecolor = imagecolorallocate($image, rand(80, 220), rand(80, 220),rand(80, 220));
		    //设置线，两点一线
		    imageline($image, rand(1, $width - 1), rand(1, $height - 1), rand(1, $width - 1), rand(1, $height - 1), $linecolor);

	    }
	    //加入干扰象素
	    for ($i = 0; $i < 300; $i++) {
		    //设置点的颜色
		    $pointcolor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));    
		    //imagesetpixel画一个单一像素
		    imagesetpixel($image, rand(0, $width), rand(0, $height), $pointcolor);
		}
		header('Content-Type: image/png');
		//生成png图片
		imagepng($image);
		//销毁$image
		imagedestroy($image);
		return true;
	}
}