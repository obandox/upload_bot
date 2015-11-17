<?php

/**
 * Primera prueba enviar 10 imagenes en una sola peticion
 * $upload->sendImages(30, "single_01_", "Primera prueba enviar 10 imagenes en una sola peticion");
 * Segunda prueba enviar 10 imagenes una tras otra
 * $upload->sendRequests(30, 1, "stack_01_", "Segunda prueba enviar 10 imagenes una tras otra");
 * Tercera prueba enviar 10 imagenes en paralelo
 * $upload->sendRequestsParallel(30, 1, "parallel_01_", "Tercera prueba enviar 10 imagenes en paralelo");
 */

require "vendor/autoload.php";


use PHPImageWorkshop\ImageWorkshop;

ini_set('memory_limit','660M');
set_time_limit(999999);


use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$upload = new Uploader();

$upload->sendImages(4, "test_01_", "prueba enviar 4 imagenes en una sola peticion");


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

    public function sendRequestsParallel($requestNumber, $imageNumber, $name, $content = null){
        $this->sendRequests($requestNumber, $imageNumber, $name, $content, true);
    }

    public function sendRequests($requestNumber, $imageNumber, $name, $content = null, $future = false){
        for ($i=0; $i < $requestNumber; $i++) { 
            $this->sendImages($imageNumber, $i.'_'.$name, $content, $future);
        }
    }

    public function sendImages($number, $name, $content = null, $future = false){

        $multipart = [];

        for ($i=0; $i < $number; $i++) { 
               $filename = $name.'_'.$i;
               $filePath = $this->generateTempImage($filename, $content);
               $multipart []= [
                    'name'     => $filename,
                    'contents' => fopen($filePath, 'r')
               ];
        }

        $client = new GuzzleHttp\Client();
        $body = [
            'multipart' => $multipart
        ];
        if($future){
            $body["future"] = true;
        }
        echo "upload \n";
        echo json_encode($body, JSON_PRETTY_PRINT)."\n";
        $response = $client->request('POST', 'http://httpbin.org/post', $body);
        echo json_encode($response, JSON_PRETTY_PRINT)."\n";
    }

	public function getRandomBase(){
        $image_path = $this->images[mt_rand(0, count($this->images) - 1)];
        return ImageWorkshop::initFromPath($image_path);
	}

	public function generateTempImage($filename, $content = null){
        $filename = $filename."_".rand(0,999).".png";

		if($content == null) $content = date('Y-m-d h:i:s');

		$group = $this->getRandomBase();

		$fontPath = $this->dirBase.'/Libraries/Fonts/cut.ttf';
		$fontSize = 12;
		$fontColor = "FFFFFF";
		$textRotation = 0;
		$backgroundColor = null; // optionnal
		
        $shadow = ImageWorkshop::initTextLayer($filename.":\n".$content, $fontPath, $fontSize+1, "000000", $textRotation, $backgroundColor);

        $text = ImageWorkshop::initTextLayer($filename.":\n".$content, $fontPath, $fontSize, $fontColor, $textRotation, $backgroundColor);

		$level = 2; 
		$positionX = 80; 
		$positionY = 40; 
		$position = "LT";

        $group->addLayer($level, $shadow, $positionX-2, $positionY-2, $position);
        $group->addLayer($level, $text, $positionX, $positionY, $position);



		$dirPath = $this->dirBase."/temp/";
		$createFolders = true;
		$backgroundColor = null; // transparent, only for PNG (otherwise it will be white if set null)
		$imageQuality = 95; // useless for GIF, usefull for PNG and JPEG (0 to 100%)
		 
		$group->save($dirPath, $filename, $createFolders, $backgroundColor, $imageQuality);
        echo $dirPath.$filename."\n";
        return $dirPath.$filename;
	}

}
