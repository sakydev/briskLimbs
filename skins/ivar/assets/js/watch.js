var startsAt = "{if isset($smarty.get.starts)}{$smarty.get.starts|timeSeconds}{/if}";

(function() {
  function fancyTimeFormat(time) {   
    // Hours, minutes and seconds
    var hrs = ~~(time / 3600);
    var mins = ~~((time % 3600) / 60);
    var secs = time % 60;

    // Output like "1:01" or "4:03:59" or "123:03:59"
    var ret = "";

    if (hrs > 0) {
        ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
    }

    ret += "" + mins + ":" + (secs < 10 ? "0" : "");
    ret += "" + secs;
    return ret;
  }

  var player = videojs('video-player', {
    plugins: {
      videoJsResolutionSwitcher: {
        default: '360'
      }
    }
  });

  // setting static height
  player.height(430);

  if (startsAt) {
    player.currentTime(parseInt(startsAt));
  }

  player.on('timeupdate', function() {
    var finalTime = fancyTimeFormat(Math.round(player.currentTime()));
    $('.start-time').val(finalTime);
  });

  var qualitiesObject = [],
  currentQuality = false;
  videojs('video-player').videoJsResolutionSwitcher()
})();