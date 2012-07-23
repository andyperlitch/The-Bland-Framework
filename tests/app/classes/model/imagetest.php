<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include APPPATH.'classes/model/image.php';
class Model_ImageTest extends PHPUnit_Framework_TestCase {
	
	private $image;
	
	public function setUp()
	{
		// create image
		$src = imagecreatetruecolor(500,500);
		imagejpeg($src, 'testing.jpg',80);
		
		// create new instance of model_image with this new image
		$this->image = new Model_Image('testing.jpg');
		imagedestroy($src);
	}
	
	public function tearDown()
	{
		$this->image = null;
		if (file_exists('testing.jpg')) unlink('testing.jpg');
		if (file_exists('testingSave.jpg')) unlink('testingSave.jpg');
		if (file_exists('testingSave2.jpg')) unlink('testingSave2.jpg');
	}
	
	public function testInfo()
	{
		$info = $this->image->info();
		$this->assertTrue(is_array($info) , 'info() method should return an array.');
		$this->assertTrue(array_key_exists('width',$info) , 'Returned array should have width array key.');
	}
	
	public function testSave()
	{
		$new_src = $this->image->save('testingSave');
		$this->assertTrue(is_array($new_src) , 'save() should return an array');
		$this->assertTrue(count($new_src) == 4 , 'Should have basename, dir name, full path, path from web root');
		$this->assertTrue( $new_src[0] == 'testingSave.jpg' , 'save()[0] should be the new filename');
		$this->assertTrue(file_exists($new_src[2]) , '$new_src[2] should be the full path to a new file.');
		unlink($new_src[2]);
	}
	
	public function testSaveOtherDir()
	{
		$new_src = $this->image->save(SYSPATH.'testingSave2');
		$this->assertTrue(is_array($new_src) , 'save() should return an array');
		$this->assertTrue(count($new_src) == 4 , 'Should have basename, dir name, full path, path from web root');
		$this->assertTrue( $new_src[0] == 'testingSave2.jpg' , 'save()[0] should be the new filename');
		$this->assertTrue(file_exists($new_src[2]) , '$new_src[2] should be the full path to a new file.');
		unlink($new_src[2]);
	}
	
	public function testCrop()
	{
		// crop the top ten and left ten pixels
		$this->image->crop(10,10,null,null);
		// should be 490x490
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 490 && $info['height'] == 490 , 'Should be 490x490');
		
		// crop down to 400x400
		$this->image->crop(0,0,400,400);
		// should be 400x400
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 400 && $info['height'] == 400 , 'Should be 400x400');
		
		// crop to a 2:1 ratio with only width supplied
		$this->image->crop(0,0,400,null,2);
		// should be 400x200
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 400 && $info['height'] == 200 , 'Should be 400x200');
		
		// crop to a 1:2 ratio with only height supplied
		$this->image->crop(0,0,null,200,0.5);
		// should be 100x200
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 100 && $info['height'] == 200 , 'Should be 100x200');
	}
	
	public function testScale()
	{
		// scale to new width and height
		$this->image->scale(500,500);
		// should be 500x500
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 500 && $info['height'] == 500 , 'Should be 500x500');
		
		// scale to new width, height must be calc'ed
		$this->image->scale(400);
		// should be 400x400
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 400 && $info['height'] == 400 , 'Should be 400x400');
		
		// scale to maxWidth
		$this->image->scale(null,null,360);
		// should be 360x360
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 360 && $info['height'] == 360 , 'Should be 360x360');
		
		// scale to maxHeight
		$this->image->scale(null,null,null,300);
		// should be 300x300
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 300 && $info['height'] == 300 , 'Should be 300x300');
		
		// scale to both maxWidth and maxHeight
		$this->image->scale(null,null,150,200);
		// should be 150x150
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 150 && $info['height'] == 150 , 'Should be 150x150');
		
		// scale to minWidth
		$this->image->scale(null,null,null,null,175);
		// should be 175x175
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 175 && $info['height'] == 175 , 'Should be 175x175');
		
		// scale to minHeight
		$this->image->scale(null,null,null,null,null,200);
		// should be 200x200
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 200 && $info['height'] == 200 , 'Should be 200x200');
		
		// scale to both minWidth and minHeight (w/o keepProportions)
		$this->image->scale(null,null,null,null,210,220,false);
		// should be 210x220
		$info = $this->image->info();
		$this->assertTrue($info['width'] == 210 && $info['height'] == 220 , 'Should be 210x220');
	}
	
	
	
}