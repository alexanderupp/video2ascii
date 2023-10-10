<?php
	
	/**
	 * Optimizer class, well, optimizes the final frame array
	 */
	class Optimizer {
		private $framePrev = [];
		private $keyframe;

		public function __construct() {}

		/**
		 * Start the optimization process
		 * 
		 * @param array &$_frames 		Array of ASCII frames
		 * @param int $_keyframe		Number of frames before a keyframe
		 * @return void
		 */
		public function optimize(array &$_frames, int $_keyframe = 12) : void {
			$this->keyframe = $_keyframe;

			$buffer = [];
			$framePrev = $_frames[0];
			$length = count($_frames);

			$start = hrtime(true);

			for($index = 1; $index < $length; $index++) {
				if($index % $this->keyframe == 0) {
					$framePrev = $_frames[$index];
					continue;
				}

				$buffer[$index] = $this->frameDiff($framePrev, $_frames[$index]);

				$framePrev = $_frames[$index];

				printProgress($length, $index);
			}

			$end = hrtime(true);

			echo "\n";
			echo number_format(($end - $start) / 1e6, 3, ".", "") . "ms\n";

			foreach($buffer as $frameIndex => $frame) {
				$_frames[$frameIndex] = $frame;
			}
		}

		/**
		 * Gets the difference between 2 frames
		 * 
		 * @param array &$frameA		First frame array
		 * @param array &$frameB		Second frame array
		 * @return array				Array with only the different chars
		 */
		private function frameDiff(array &$frameA, array &$frameB) : array {
			$width = count($frameA[0]);
			$height = count($frameA);

			$half = (int)(($width * $height) / 2);

			$return = [];
			$updates = 0;

			for($y = 0; $y < $height; $y++) {
				for($x = 0; $x < $width; $x++) {
					if($frameA[$y][$x] != $frameB[$y][$x]) {
						$return[$y][$x] = $frameB[$y][$x];
					}
				}
			}

			return $return;
		}
	}