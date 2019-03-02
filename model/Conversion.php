<?php
  
  /**
  * 
  */
  class Conversion {
    
    function __construct($ffmpegPath, $videoFileName, $fileDirectory, $fullFilePath) {
      $this->ffmpegPath = $ffmpegPath;
      $this->originalFileName = $videoFileName;
      $this->originalDirectory = $fileDirectory; // e.g 2017/04/28
      $this->originalFullFilePath = $fullFilePath;
      $this->originaWidth = false;
      $this->originalHeight = false;
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
        "original" => $this->originalWidth . 'x' . $this->originalHeight,
        'lowest' => '168x105',
        'small' => '416x260',
        'medium' => '632x395',
        'highest' => '768x432'
        );

      foreach ($thumbOptions as $optionName => $size) {
        $thumbOutputPath = str_replace('//', '/', THUMBNAILS_DIRECTORY . '/' . $this->originalDirectory . '/' . $this->originalFileName . '_' . $optionName . '.jpg');
        $this->remove($thumbOutputPath);
        $thumbsCommand = "$this->ffmpegPath -deinterlace -an -ss 00:00:02 -i $path -f image2 -t 1 -r 1 -y -s $size -q:v 1 $thumbOutputPath";
        shell_exec($thumbsCommand);
        if (file_exists($thumbOutputPath)) {
          $generatedThumbs[$optionName] = $thumbOutputPath;
        }
      }
      return $generatedThumbs;
    }

    private function possibleQualities($videoWidth, $videoHeight) {
      $basicQualities = array('240', '360', '480', '720');
      $dimensions = array(
        '240' => array('width' => '424', 'video_bitrate' => '576', 'audio_bitrate' => '64'), 
        '360' => array('width' => '640', 'video_bitrate' => '896', 'audio_bitrate' => '64'), 
        '480' => array('width' => '848', 'video_bitrate' => '1536', 'audio_bitrate' => '96'), 
        '720' => array('width' => '1280', 'video_bitrate' => '3072', 'audio_bitrate' => '96')
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
      $rawVideoDetails = $this->details($this->originalFullFilePath, false);
      $videoDetails = json_decode($rawVideoDetails, true)['streams'];
      
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
      
      $this->originalWidth = $videoSection['width'];
      $this->originalHeight = $videoSection['height'];

      # ffmpeg -i input.avi -s 720x480 -c:a copy output.mkv
      
      $resoloutionCommands = array();
      $outputFiles = array();
      $outputFiles['details']['duration'] = $videoSection['duration'];
      $generatedThumbs = $this->createThumbnails($this->originalFullFilePath);
      $pendingResouloutions = $this->possibleQualities($this->originalWidth, $this->originalHeight);

      $finalPossibleResolotuions = array_keys($pendingResouloutions);
      $outputFiles['details']['possibleQualities'] = implode(',', $finalPossibleResolotuions);
      
      foreach ($pendingResouloutions as $height => $resoloution) {
        $currentOutputFile = VIDEOS_DIRECTORY . '/' . $this->originalDirectory . '/' . $this->originalFileName . '-' . $height . '.mp4';
        $outputFiles['files'][] = $currentOutputFile;
        $this->remove($currentOutputFile);
        # $currentCommand = "$this->ffmpegPath -i $this->originalFullFilePath -s $resoloution -c:a copy $currentOutputFile 2>&1";
        $width = $resoloution['width'];
        $videoBitrate = $resoloution['video_bitrate'];
        $audioBitrate = $resoloution['audio_bitrate'];

        $currentCommand = "$this->ffmpegPath -i $this->originalFullFilePath -vcodec libx264 -vprofile baseline -preset medium -b:v {$videoBitrate}k -maxrate {$videoBitrate}k -vf scale=$width:$height -threads 0 -acodec libfdk_aac -ab {$audioBitrate}k $currentOutputFile 2>&1";

        # exit($currentCommand);
        # command becomes
        # /usr/bin/ffmpeg -i /var/www/html/clipfox/trunk/media/temporary/2018/02/06/LS1lCcHrKxsWEAb.mp4 -vcodec libx264 -vprofile baseline -preset medium -b:v 576k -maxrate 576k -vf scale=424:240 -threads 0 -acodec libfdk_aac -ab 64k /var/www/html/clipfox/trunk/media/videos/2018/02/06/LS1lCcHrKxsWEAb-240.mp4

        $resoloutionCommands[] = $currentCommand;
      }

      foreach ($resoloutionCommands as $key => $currentVideoResoloutionCommand) {
        $commandOutput = shell_exec($currentVideoResoloutionCommand); 
      }

      foreach ($outputFiles['files'] as $key => $outputFile) {
        if (file_exists($outputFile) && $this->duration($outputFile)) {
          $outputFiles['files'][$key] = array('status' => 'success', 'file' => $outputFile);
        } else {
          $outputFiles['files'][$key] = array('status' => 'failed', 'file' => $outputFile);
        }
      }
      
      return $outputFiles;
    }
  }