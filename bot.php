<?php

require "vendor/autoload.php";


use PHPImageWorkshop\ImageWorkshop;


$upload = new Uploader();

$upload->generateTempImage("una imagen con datos");


class Uploader{

	public function __construct(){
		$this->dirBase = dirname(__FILE__);
		$this->images  = [];
		$filepath_base = $this->dirBase.'/Libraries/Images/';

		$files = scandir($filepath_base);
		foreach ($files as $filename) 
		{ 
		  if (!in_array($filename,array(".",".."))) 
		  { 
            $this->images []= $filepath_base.$filename;
		  } 
		}

	}

	public function getRandomBase(){
        $image_path = $this->images[mt_rand(0, count($this->images) - 1)];
        return ImageWorkshop::initFromPath($image_path);
	}

	public function generateTempImage($name, $content = null){
		if($content == null) $content = date('Y-m-d h:i:s');

		$group = $this->getRandomBase();

		$fontPath = $this->dirBase.'/Libraries/Fonts/cut.ttf';
		$fontSize = 12;
		$fontColor = "FFFFFF";
		$textRotation = 0;
		$backgroundColor = null; // optionnal
		
        $shadow = ImageWorkshop::initTextLayer($name.":\n".$content, $fontPath, $fontSize+1, "000000", $textRotation, $backgroundColor);

        $text = ImageWorkshop::initTextLayer($name.":\n".$content, $fontPath, $fontSize, $fontColor, $textRotation, $backgroundColor);

		$level = 2; 
		$positionX = 80; 
		$positionY = 40; 
		$position = "LT";

        $group->addLayer($level, $shadow, $positionX-2, $positionY-2, $position);
        $group->addLayer($level, $text, $positionX, $positionY, $position);



		$dirPath = $this->dirBase."/temp/";
		$filename = $name."_".time().".png";
		$createFolders = true;
		$backgroundColor = null; // transparent, only for PNG (otherwise it will be white if set null)
		$imageQuality = 95; // useless for GIF, usefull for PNG and JPEG (0 to 100%)
		 
		$group->save($dirPath, $filename, $createFolders, $backgroundColor, $imageQuality);
        return $dirPath.$filename;
	}

}
