// Mike's rip of the Google example at:
// https://developers.google.com/youtube/iframe_api_reference#Operations

// This code loads the IFrame Player API code asynchronously.
var tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// This function creates an <iframe> after the API code downloads.
var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        height: '360',
        width: '640',
        videoId: 'GCRgfImEqaY',
        playerVars: {
            'autoplay': 0,
            'controls': 0,
            'rel': 0,
            'showinfo': 0,
            'disablekb': 1,
            'enablejsapi': 1,
            'modestbranding': 1,
            'loop': 0
        },
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        }
    });
}

// The API will call this function when the video player is ready.
// Not necessarily any use to us.
function onPlayerReady(event) {

}

// The API calls this function when the player's state changes.
// The function indicates that when playing a video (state=1),
// the player should play for six seconds and then stop.
function onPlayerStateChange(event) {
    // If it's paused, just play it again.
    if (event.data == YT.PlayerState.ENDED) {
        player.destroy();
    } else if (event.data == YT.PlayerState.PAUSED) {
        player.playVideo();
    }
}
// We may not need this function.
// function stopVideo() {
//     player.stopVideo();
// }
