<?php

namespace Components;
use PHPImageWorkshop\ImageWorkshop;


class Uploader{

	public function generateTempImage(){

		$image = ImageWorkshop::initFromPath(__DIR__.'/../Libraries/Images/Image01.jpg');

		$text = "I am the text";
		$fontPath = __DIR__.'/../Libraries/Fonts/quartzo.ttf';
		$fontSize = 12;
		$fontColor = "FFFFFF";
		$textRotation = 0;
		$backgroundColor = null; // optionnal
		
		$text = ImageWorkshop::initTextLayer($text, $fontPath, $fontSize, $fontColor, $textRotation, $backgroundColor);



	}

}