<?php

	/**
	 * Simple RTL encoder
	 */
	class RTL_Encoder {
		/**
		 * Start the encoding process
		 * 
		 * @param array &$_frames 		Array of ASCII frames
		 * @return void
		 */
		public function encode(array &$_frames) : void {
			//get a frame's length;
			$length = strlen($_frames[0]);
			$char = $_frames[0][0];

			foreach($_frames as $index => $frame) {
				$buffer = "";
				$i = 0;

				//file_put_contents("./raw.txt", $frame);

				while($i < $length) {
					$count = 1;

					while((($i + 1) < $length) && ($frame[$i] == $frame[$i + 1])) {
						$i++;

						$count++;
					}

					// Check if we are actually saving any bytes by adding the count
					// We'll handle single chars during decoding
					if($count > 1) {
						$buffer .= $count . $frame[$i] . ":";
					} else {
						$buffer .= $frame[$i] . ":";
					}
					$i++;
				}

				$_frames[$index] = $buffer;

				// file_put_contents("./debug.txt", $buffer);
				// exit;
			}
		}
	}