<?php

/**
* 
*/
class Conversion {
  
  function __construct($ffmpeg, $filename, $directory, $path, $logs) {
    $this->ffmpeg = $ffmpeg;
    $this->filename = $filename;
    $this->directory = $directory; // e.g 2017/04/28
    $this->path = $path;
    $this->logs = $logs;
    $this->width = false;
    $this->height = false;
  }

  private function remove($path) {
    if (file_exists($path)) {
      unlink($path);
    }
  }

  private function details($path, $jsonDecode = true) {
    $command = "/usr/bin/ffprobe -v quiet -print_format json -show_format -show_streams $path";
    $details = shell_exec($command);
    if ($jsonDecode) {
      return json_decode($details, true)['streams'];
    } else {
      return $details;
    }
  }

  private function duration($path) {
    $command = "/usr/bin/ffprobe -v quiet -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $path";
    $duration = shell_exec($command);
    if (is_numeric(trim($duration))) {
      return $duration;
    }
  }

  public function createThumbnails($path) {
    $generatedThumbs = array();
    $thumbOptions = array(
      "original" => $this->width . 'x' . $this->height,
      'lowest' => '168x105',
      'small' => '416x260',
      'medium' => '632x395',
      'highest' => '768x432'
      );

    foreach ($thumbOptions as $optionName => $size) {
      $thumbOutputPath = str_replace('//', '/', THUMBNAILS_DIRECTORY . '/' . $this->directory . '/' . $this->filename . '_' . $optionName . '.jpg');
      $this->remove($thumbOutputPath);
      $thumbsCommand = "$this->ffmpeg -deinterlace -an -ss 00:00:02 -i $path -f image2 -t 1 -r 1 -y -s $size -q:v 1 $thumbOutputPath";
      $this->logs->write("Command for thumb : $optionName ($size) is : $thumbsCommand");
      shell_exec($thumbsCommand);
      if (file_exists($thumbOutputPath)) {
        $this->logs->write("Successfully generated $optionName thumb");
        $generatedThumbs[$optionName] = $thumbOutputPath;
      }
    }
    return $generatedThumbs;
  }

  private function possibleQualities($videoWidth, $videoHeight) {
    $basicQualities = array('240', '360', '480', '720', '1080');
    $dimensions = array(
      '240' => array('width' => '424', 'video_bitrate' => '576', 'audio_bitrate' => '64'), 
      '360' => array('width' => '640', 'video_bitrate' => '896', 'audio_bitrate' => '64'), 
      '480' => array('width' => '848', 'video_bitrate' => '1536', 'audio_bitrate' => '96'), 
      '720' => array('width' => '1280', 'video_bitrate' => '3072', 'audio_bitrate' => '128'),
      '1080' => array('width' => '1920', 'video_bitrate' => '4992', 'audio_bitrate' => '128')
    );
    $possibleQualities = array();
    foreach ($basicQualities as $quality) {
      if ($quality <= $videoHeight) {
        $finalDimensions = $dimensions[$quality];
        if ($finalDimensions['width'] > $videoWidth) {
          $finalDimensions['width'] = $videoWidth;
        }

        $possibleQualities[$quality] = $finalDimensions;
      }
    }

    if (empty($possibleQualities)) {
      $possibleQualities['240'] = $dimensions['240'];
    }

    return $possibleQualities;
  }

  public function process() {      
    $this->logs->write("Log file for video : $this->filename");
    $this->logs->write("Fetching video details...");
    $rawVideoDetails = $this->details($this->path, false);
    $videoDetails = json_decode($rawVideoDetails, true)['streams'];
    $this->logs->write("Video details : $rawVideoDetails");
    
    if ($videoDetails[0]['codec_type'] == 'video') {
      $videoSection = $videoDetails[0];
      $audioSection = $videoDetails[1];
    } else {
      $videoSection = $videoDetails[1];
      $audioSection = $videoDetails[0];
    }

    if (!isset($videoSection['duration'])) {
      $videoSection['duration'] = timeSeconds($videoSection['tags']['DURATION']);
    }
    
    $this->width = $videoSection['width'];
    $this->height = $videoSection['height'];

    # ffmpeg -i input.avi -s 720x480 -c:a copy output.mkv
    
    $resoloutionCommands = array();
    $outputFiles = array();
    $outputFiles['details']['duration'] = $videoSection['duration'];
    $this->logs->write("Getting ready to generate thumbs");
    $generatedThumbs = $this->createThumbnails($this->path);
    $this->logs->write("Generated thumbs are : \n" . implode("\n", $generatedThumbs));
    $this->logs->write("Listing possible video qualities to be converted");
    $pendingResouloutions = $this->possibleQualities($this->width, $this->height);

    $finalPossibleResolotuions = array_keys($pendingResouloutions);
    $this->logs->write("Possible video qualties to be converted are : \n" . implode("\n", $finalPossibleResolotuions));
    $outputFiles['details']['possibleQualities'] = implode(',', $finalPossibleResolotuions);
    $this->logs->write("Looping through qualities and building commands");
    
    foreach ($pendingResouloutions as $height => $resoloution) {
      $currentOutputFile = VIDEOS_DIRECTORY . '/' . $this->directory . '/' . $this->filename . '-' . $height . '.mp4';
      $outputFiles['files'][] = $currentOutputFile;
      $this->remove($currentOutputFile);
      # $currentCommand = "$this->ffmpeg -i $this->path -s $resoloution -c:a copy $currentOutputFile 2>&1";
      $width = $resoloution['width'];
      $videoBitrate = $resoloution['video_bitrate'];
      $audioBitrate = $resoloution['audio_bitrate'];

      $currentCommand = "$this->ffmpeg -i $this->path -vcodec libx264 -vprofile baseline -preset medium -b:v {$videoBitrate}k -maxrate {$videoBitrate}k -vf scale=$width:$height -threads 0 -acodec libfdk_aac -ab {$audioBitrate}k $currentOutputFile 2>&1";

      # exit($currentCommand);
      # command becomes
      # /usr/bin/ffmpeg -i /var/www/html/clipfox/trunk/media/temporary/2018/02/06/LS1lCcHrKxsWEAb.mp4 -vcodec libx264 -vprofile baseline -preset medium -b:v 576k -maxrate 576k -vf scale=424:240 -threads 0 -acodec libfdk_aac -ab 64k /var/www/html/clipfox/trunk/media/videos/2018/02/06/LS1lCcHrKxsWEAb-240.mp4

      $this->logs->write("Command for resoloution {$height}p is : $currentCommand");
      $resoloutionCommands[] = $currentCommand;
    }

    foreach ($resoloutionCommands as $key => $currentVideoResoloutionCommand) {
      $this->logs->write("Executing command: $currentVideoResoloutionCommand");
      $commandOutput = shell_exec($currentVideoResoloutionCommand); 
      $this->logs->write("Command output \n $commandOutput");
    }

    foreach ($outputFiles['files'] as $key => $outputFile) {
      if (file_exists($outputFile) && $this->duration($outputFile)) {
        $outputFiles['files'][$key] = array('status' => 'success', 'file' => $outputFile);
      } else {
        $outputFiles['files'][$key] = array('status' => 'failed', 'file' => $outputFile);
      }
    }

    $this->logs->write("File conversion is done and returing output to other function");    
    return $outputFiles;
  }
}