<?php
	#!/usr/bin/php

	include "app/ASCII.php";
	//include "app/Optimizer.php";
	include "app/Frame.php";
	include "app/RTL_Encoder.php";

	// check if a video file name was provided
	if($argc == 1) {
		echo "No input file provided\n";
		exit;
	}

	// get the video file name
	$filename = trim($argv[1]);

	$img = __DIR__ . "/img/";

	// get the output file name
	$output = __DIR__ . "/" . $argv[2];

	$raw = [];
	$frames = [];

	echo "Starting frame image conversion\n";

	// check if the frame directory exists
	if(!is_dir($img)) {
		mkdir($img);
	}

	// clear out previously exported frames
	foreach(new DirectoryIterator($img) as $file) {
		if($file->isDot()) {
			continue;
		}

		unlink($img . $file->getFilename());
	}

	// fire a call to ffmpeg to export the frames
	// we want only 4 frames per second at 640x360 resolution
	echo "Exporting video frames\n";
	echo shell_exec("ffmpeg -loglevel warning -i " . $filename . " -vf fps=4,scale=640:360 ./img/frame%d.jpg");

	// get all of the newly exported frames
	foreach(new DirectoryIterator($img) as $file) {
		if($file->isDot()) {
			continue;
		}

		$raw[] = $img . $file->getFilename();
	}

	$total = count($raw);

	// check to make sure we exported the frames properly
	if($total == 0) {
		echo "error: no frames found\n";
		exit;
	}

	echo "Total frames: " . $total . "\n";

	$count = 0;

	
	$start = hrtime(true);

	foreach($raw as $file) {
		$frame = new Frame($file);
		$frame->load();

		if($frame->convert()) {
			$frames[] = $frame->getFrame();
		}

		printProgress($total, $count);

		$count++;
	}

	$end = hrtime(true);

	echo "\n";
	print number_format((($end - $start) / 1e6 ), 3, ".", "") . "ms\n";

	/** SIMPLE RUNTIME LENGTH ENCODING */
	$rtl = new RTL_Encoder();
	$rtl->encode($frames);

	echo "Exporting to JSON\n";

	$json = json_encode([
		[
			"width" => 128,
			"height" => 72
		],
		$frames
	], JSON_FORCE_OBJECT);

	file_put_contents($output, $json);

	echo "Done!\n";

	//exit;

	/**
	 * Render a little progress bar
	 * 
	 * @param int $total		Total number of steps
	 * @param int $frame		Current step number
	 * @return void
	 */
	function printProgress(int $total, int $step) : void{
		// scale the bar down to 64 chars
		$ratio = 64 / $total;
		echo "\r[";

		$complete = round((($step / $total) * 64));

		// array_fill is kind of slow
		// but I'm ok with it here.
		$bar = array_merge(
			array_fill(0, $complete, "#"),
			array_fill($complete, 64 - $complete, "-")
		);

		echo implode("", $bar) . "]";
	}

	/**
	 * Output a single frame array for testing. Must be called via browser
	 * This WON'T work with compression on. Each frame builds off of the last
	 * 
	 * @param array $ascii		2D array containing the ASCII frame data
	 * @return void
	 */
	function debug(array $ascii) : void {
		echo "<pre style='font-family:monospace; font-size:12px; line-height:0.5em;'>";
		
		foreach($ascii as $frame) {
			for($y = 0; $y < 36; $y++) {
				for($x = 0; $x < 64; $x++) {
					echo $frame[$y][$x];
				}

				echo "\n";
			}
		}

		echo "</pre>\n";
	}