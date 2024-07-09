<?php

	/**
	 * 
	 */
	class Frame {
		private $ascii;
		private $file;
		private $frame;

		const WIDTH = 128;
		const HEIGHT = 72;

		public function __construct(string $_file) {
			$this->file = $_file;
		}

		/**
		 * Coverts the image file into ASCII text
		 * 
		 * @return bool
		 */
		public function convert() : bool {
			// Filter the image a bit to make the coversion more clear
			imagefilter($this->frame, IMG_FILTER_GRAYSCALE);
			imagefilter($this->frame, IMG_FILTER_BRIGHTNESS, 20);
			imagefilter($this->frame, IMG_FILTER_CONTRAST, -30);

			$temp = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

			if(imagecopyresized($temp, $this->frame, 0, 0, 0, 0, self::WIDTH, self::HEIGHT, 640, 360)) {
				// free up the orginal, larger, image resource
				imagedestroy($this->frame);
				
				// copy the resized image into our now empty frame resource
				$this->frame = $temp;

				// free up the memory used by the resized image
				imagedestroy($temp);
			}

			$this->ascii = "";

			for($y = 0; $y < self::HEIGHT; $y++) {
				// intialize empty frame line string
				//$this->ascii[$y] = "";

				for($x = 0; $x < self::WIDTH; $x++) {
					$pixel = $this->int2rgb(imagecolorat($this->frame, $x, $y));

					$brightness = round((($pixel[0] + $pixel[1] + $pixel[2]) / 3) / 25);
					//$this->ascii[$y] .= ASCII::VALUES[$brightness];
					$this->ascii .= ASCII::VALUES[$brightness];
				}
			}

			//imagejpeg($this->frame, __DIR__ . "/test.jpg");

			return true;
		}

		/**
		 * Get the ASCII frame data
		 * 
		 * @return array
		 */
		public function getFrame() : string {
			return $this->ascii;
		}

		/**
		 * Load in the image resource
		 * 
		 * @return bool
		 */
		public function load() : bool {
			if(!file_exists($this->file)) {
				return false;
			}

			return (bool)$this->frame = imagecreatefromjpeg($this->file);
		}

		/**
		 * Converts an int color value into it's RBG values
		 * 
		 * @param int $val		Color value
		 * @return array		RGB colors
		 */
		private function int2rgb(int $val) : array {
			return array(
				$val >> 16 & 0xFF,
				$val >> 8 & 0xFF,
				$val & 0xFF
			);
		}
	}