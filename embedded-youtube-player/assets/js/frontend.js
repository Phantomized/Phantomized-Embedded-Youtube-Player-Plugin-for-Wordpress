function onYouTubeIframeAPIReady() {
  //note: eytPlayerData is defined through wp_localize_script

  for (i = 0; i < eytPlayerData.length; i++) {
    var id = eytPlayerData[i]['id'];
    var args = JSON.parse(eytPlayerData[i]['args']);

    var player = new YT.Player('ytplayer_' + id, args);
    player.args = args;
    player.eventSettings = JSON.parse(eytPlayerData[i]['eventSettings']);
  }
}

function eytpOnPlayerReady(event) {

  //Get player
  var player = event.target;

  //Get event settings
  var eventSettings = player.eventSettings;

  //If video is set to autoplay, wait with displaying until video is done buffering
  if (eventSettings.autoplay) {
    player.addEventListener('onStateChange', 'eytpOnPlayStateUnhide');

    //Plays video
    //Temporary mute fixes google crome bug where autoplay often fails
    //if the video is not muted.
    player.mute();
    player.playVideo();
  } else {
    player.getIframe().style.opacity = 1;
  }

  //If controls set to disable, disable pointer events.
  if (eventSettings.disable_controls) {
    player.getIframe().style.pointerEvents = 'none';
  }

  //If looping, setup loop event.
  if (eventSettings.loop) {
    player.addEventListener('onStateChange', 'eytpOnEndedStatePlay')
  }
}

function eytpOnPlayStateUnhide(event) {
  if (event.data == YT.PlayerState.PLAYING) {
    if (!event.target.args.mute) //Chrome fix - If not set as mute, unmute again.
      event.target.unMute();
    event.target.getIframe().style.opacity = 1;

    event.target.removeEventListener('onStateChange', 'eytpOnPlayStateUnhide');
  }
}

function eytpOnEndedStatePlay(event) {
  if (event.data == YT.PlayerState.ENDED) {
    event.target.playVideo();
  }
}


(function() {
  var tag = document.createElement('script');
  tag.src = "https://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
})();