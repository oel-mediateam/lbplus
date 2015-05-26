/*
 * LiveButtons+
 * @author: Ethan Lin, Mike Kellum
 * @uri: https://github.com/oel-mediateam/lbplus
 * @version: 0.0.1 (alpha)
 * @license: The Artistic License 2.0
 *
 * Copyright (c) 2015 University of Wisconsin-Extension,
 * Divison of Continuing Education, Outreach & E-Learning
 *
*/
function onYouTubeIframeAPIReady(){var e={autoplay:0,controls:0,disablekb:1,enablejsapi:1,iv_load_policy:3,loop:0,modestbranding:1,rel:0,showinfo:0};video.segmented&&(e.start=video.start,e.end=video.end),video.player=new YT.Player(video.selector,{width:"640",height:"360",videoId:video.vId,playerVars:e}),video.player.addEventListener("onReady",function(){video.segmented?video.duration=video.end-video.start:video.duration=video.player.getDuration(),$(".progress_bar .time .duration").html(moment(1e3*video.duration).format("mm:ss")),$("#videoPlayBtn").on("click",function(){$(this).remove(),video.player.playVideo()})}),video.player.addEventListener("onStateChange",function(e){var t=e.target.getPlayerState();switch(t){case YT.PlayerState.ENDED:$(".lbplus_wrapper").showTransition("Video Ended","Calculating results. Please wait..."),$(".lbplus_media .overlay").html('<div id="videoPlayBtn">ENDED</div>');for(var i=0;i<$(".btn[data-action-id]").length;i++)$(".btn[data-action-id]:eq("+i+")").addClass("disabled");$(".btn.rewind").addClass("disabled"),clearInterval(updatePrgrsInterval),$(".progress_bar .progressed").css("width","100%"),$(".progress_bar .time .elapsed").html(moment(1e3*video.duration).format("mm:ss")),$.fn.writeToFile();break;case YT.PlayerState.PLAYING:if(!video.rewinded){for(var a=0;a<$(".btn[data-action-id]").length;a++)$(".btn[data-action-id]:eq("+a+")").removeClass("disabled"),$(".btn[data-action-id]:eq("+a+")").clickAction();$(".btn.rewind").removeClass("disabled"),$(".btn.rewind").clickAction(),updatePrgrsInterval=setInterval(updateProgress,100),$.fn.tagHoverAction()}}})}function updateProgress(){var e=video.player.getCurrentTime();video.segmented&&(e=video.player.getCurrentTime()-video.start);var t=Math.floor(100/video.duration*e),i=moment(1e3*e).format("mm:ss");$(".progress_bar .progressed").css("width",t+"%"),$(".progress_bar .time .elapsed").html(i)}var video={player:null,selector:"ytv",vId:null,segmented:!1,start:0,end:0,duration:null,rewinded:!1},tagCount=0,updatePrgrsInterval,studentResponses=[];$(function(){video.vId=$("#"+video.selector).data("video-id");var e=$("#"+video.selector).data("start"),t=$("#"+video.selector).data("end");e!==Number("-1")&&(video.start=moment.duration(e,"mm:ss").asSeconds()/60,video.end=moment.duration(t,"mm:ss").asSeconds()/60,video.start>=0&&void 0!==video.start&&video.start<video.end&&(video.segmented=!0)),$.fn.loadYouTubeAPI()}),$.fn.loadYouTubeAPI=function(){var e=document.createElement("script"),t=document.getElementsByTagName("script")[0];e.src="https://www.youtube.com/iframe_api",t.parentNode.insertBefore(e,t)},$.fn.clickAction=function(){$(this).on("click",function(){if(!$(this).hasClass("disabled")){if($(this).hasClass("rewind")){var e=Number($(this).data("length")),t=video.player.getCurrentTime();e=e>=t?0:t-e,video.player.seekTo(e),video.rewinded=!0}$(this).addTag(),$(this).cooldown()}})},$.fn.addTag=function(){var e=video.player.getCurrentTime(),t=$(this).data("action-id"),i=$(this).find(".action_name").html(),a=moment(1e3*e).format("mm:ss"),n=$(".progress_bar .progressed").width()+10,d=$(this).find(".icon").html(),s='<span class="tag" data-action-id="'+t+'" data-time="'+a+'" data-count="'+tagCount+'" style="left:'+n+"px;z-index:"+tagCount++ +'">'+d+"</span></span>",o={id:t,name:i,timestamped:a};studentResponses.push(o),$(".progress_bar_holder").prepend(s)},$.fn.tagHoverAction=function(){$(".progress_bar_holder").on("mouseover",".tag",function(){$(this).css("z-index",99)}),$(".progress_bar_holder").on("mouseout",".tag",function(){$(this).css("z-index",$(this).data("count"))})},$.fn.cooldown=function(){var e=$(this).index(".btn"),t=$(this),i=t.width(),a=1e3*Number(t.attr("data-cooldown")),n=t.find(".cooldown .progress"),d=$(n.selector+":eq("+e+")"),s=$(this).find(".limits"),o=Number(s.html());d.width()>=i&&(d.width(0),t.addClass("disabled")),o--,s.html(o),0>=o?$(this).addClass("disabled"):d.animate({width:i},a,function(){t.removeClass("disabled")})},$.fn.showTransition=function(e,t){$(this).prepend('<div class="transition_overlay"><div class="heading">'+e+'</div><div class="subheading">'+t+'</div><div class="loading"><span class="icon-spinner spin"></span></div></div>'),$(".transition_overlay").css("display","none").fadeIn()},$.fn.hideTransition=function(){$(".transition_overlay").fadeOut(function(){$(this).remove()})},$.fn.writeToFile=function(){studentResponses.length<=0&&(studentResponses=-1),$.post("includes/student_input.php",{student:studentResponses},function(e){$("body").prepend(e)})};