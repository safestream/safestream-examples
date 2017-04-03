var streamready = false;
var stream = "";
var pollurl;
var starttime = 0;
var endtime = 0;
var prerollurl = 'http://static.mediasilo.com.s3.amazonaws.com/safestreamvideos/ss-bug-720p-bbb2.mp4';
var user = {};


$( document ).ready(function() {
  $( "#loginform" ).submit(function( event ) {
      event.preventDefault();
      var user = {};
      user.name = $("#demo-name").val();
      user.email = $("#demo-email").val();
      user.company = $("#demo-company").val();

      $.ajax({
        type: "POST",
        url: "safestream.php",
        data: user,
        success: function(e){
          var watermark = $.parseJSON(e);
          $("#login-button").remove();
          initPreRoll(watermark.href,user);
        }
      });
    });
});



function checkWatermarkStatus() {
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": pollurl,
        "method": "GET",
        "statusCode": {
            401: function() {
                showError(401);
            },
            404: function(){
                showError(404);
            }
        }
    };

    $.ajax(settings).done(function (response) {
        if(response.status === "READY"){
            stop();
            streamready = true;
            stream = response.containers.m3u8;
            playStream();
        } else {
            setTimeout(checkWatermarkStatus, 3000);
        }
    })
}


function initPreRoll(data,userdata){
    user =  userdata;
    checkWatermarkStatus();
    $("#status").html("SafeStream is now watermarking every frame of your video. Standby.")
    pollurl = data;

    var player = videojs('my-player');

    player.ready(function() {
        starttime = new Date().getTime();
        player.src({
          src: prerollurl,
          type: 'video/mp4'
        });
        player.play();
    });
}

function checkPlayStream(){
    if(streamready){
        var player = videojs('my-player');
        playStream();
    } else {
        setTimeout(checkPlayStream, 1000);
    }
}

function playStream(){
   console.log("Entered playstream");
    endtime = new Date().getTime();
    var diff = (endtime - starttime)/1000;

    $("#status").html("Your watermarked video is ready. It took " + diff + " seconds.")
    var player = videojs('my-player');

    player.src({
      src: stream,
      type: 'application/x-mpegURL'
    });
    player.play();
}
