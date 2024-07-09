
# Video to ASCII

A quick and dirty tool to covert a video file to a JSON file containing a series of ASCII image frames.

This tool powers the "video" file behind the easter egg at https://alexrupp.me/~/1993/10/08/data.txt
## Installation

Clone the repo and you're basically ready to go. You will need to have ffmpeg and I think at least PHP 8 to run.

```bash
  $ git clone git@github.com:alexanderupp/video2ascii.git
  $ cd video2ascii
```

## Usage/Examples

Converting a video file called `video.mp4` into an output file called `demo.json`.

```bash
  $ php convert.php video.mp4 demo.json
```