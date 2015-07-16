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
function onYouTubeIframeAPIReady(){var e={autoplay:0,controls:0,disablekb:0,enablejsapi:0,iv_load_policy:3,loop:0,modestbranding:1,rel:0,showinfo:0};video.segmented&&(e.start=video.start,e.end=video.end),video.player=new YT.Player(video.selector,{width:"640",height:"360",videoId:video.vId,playerVars:e}),video.player.addEventListener("onReady",function(){video.segmented?video.duration=video.end-video.start:video.duration=video.player.getDuration(),$(".progress_bar .time .duration").html(moment(1e3*video.duration).format("mm:ss")),$("#videoPlayBtn").on("click",function(){$.post("includes/start_exercise.php",{begin:1},function(){$("#videoPlayBtn").hide(),video.player.playVideo()})})}),video.player.addEventListener("onStateChange",function(e){var t=e.target.getPlayerState();switch(t){case YT.PlayerState.ENDED:$(".lbplus_wrapper").showTransition("Video Ended","Calculating results. Please wait..."),$("#videoPlayBtn").html("ENDED").show();for(var i=0;i<$(".btn[data-action-id]").length;i++)$(".btn[data-action-id]:eq("+i+")").addClass("disabled");$(".btn.rewind").addClass("disabled"),clearInterval(updatePrgrsInterval),$(".progress_bar .progressed").css("width","100%"),$(".progress_bar .time .elapsed").html(moment(1e3*video.duration).format("mm:ss")),setTimeout(function(){$.fn.writeToFile()},3e3);break;case YT.PlayerState.PLAYING:if(!video.rewinded){for(var s=0;s<$(".btn[data-action-id]").length;s++)$(".btn[data-action-id]:eq("+s+")").removeClass("disabled"),$(".btn[data-action-id]:eq("+s+")").clickAction();$(".btn.rewind").removeClass("disabled"),$(".btn.rewind").clickAction(),updatePrgrsInterval=setInterval(updateProgress,100),$.fn.tagHoverAction()}}})}function updateProgress(){var e=video.player.getCurrentTime();video.segmented&&(e=video.player.getCurrentTime()-video.start);var t=Math.floor(100/video.duration*e),i=moment(1e3*e).format("mm:ss");$(".progress_bar .progressed").css("width",t+"%"),$(".progress_bar .time .elapsed").html(i)}var video={player:null,selector:"ytv",vId:null,segmented:!1,start:0,end:0,duration:null,rewinded:!1},tagCount=0,updatePrgrsInterval,studentResponses=[];$(function(){"use strict";if($("#google_revoke_connection").length&&$("#google_revoke_connection").click(function(){return $("#disconnect-confirm").dialog({dialogClass:"no-close",title:"Disconnect Google Account",position:{my:"center",at:"center",of:$(".signin_view")},resizable:!1,draggable:!1,width:300,height:215,modal:!0,buttons:{OK:function(){$(this).dialog("close"),$(".lbplus_wrapper").showTransition("Revoke Access","Disconnecting Google account. Please wait..."),setTimeout(function(){$.post("includes/disconnect_google.php",{revoke:1},function(){location.reload()})},3e3)},Cancel:function(){$(this).dialog("close")}}}),!1}),video.vId=$("#"+video.selector).data("video-id"),video.vId){var e=$("#"+video.selector).data("start"),t=$("#"+video.selector).data("end");e!==Number("-1")&&(video.start=moment.duration(e,"mm:ss").asSeconds()/60,video.end=moment.duration(t,"mm:ss").asSeconds()/60,video.start>=0&&void 0!==video.start&&video.start<video.end&&(video.segmented=!0)),$.fn.loadYouTubeAPI()}$("select").length&&$("select").each(function(){var e=$(this),t=!1,i=$(this).children("option").length,s=e.attr("class");e.addClass("select-hidden"),e.wrap('<div class="select"></div>'),e.after('<div class="select-styled"></div> ');var n=e.next("div.select-styled");n.addClass(s),n.text(e.children("option").eq(0).text());for(var o=$("<ul />",{"class":"select-options"}).insertAfter(n),a=0;i>a;a++)$("<li />",{text:e.children("option").eq(a).text(),rel:e.children("option").eq(a).val()}).appendTo(o);var d=o.children("li");n.click(function(e){e.stopPropagation(),t?(n.removeClass("active"),o.hide(),t=!1):($("div.select-styled.active").each(function(){$(this).removeClass("active").next("ul.select-options").hide()}),$(this).toggleClass("active").next("ul.select-options").toggle(),t=!0)}),d.click(function(i){i.stopPropagation();var s=$(this).attr("rel");n.text($(this).text()).removeClass("active"),e.val(s),o.hide(),t=!1,$(".selection_view").length&&($(".exercise_info").remove(),$.post("includes/exercise_info.php",{id:s},function(e){if(e){var t=JSON.parse(e);$(".select").after('<div class="exercise_info"><div class="description_box"><p><strong>Description:</strong></p><div class="description"></div></div><p class="meta"></p></div>'),$(".exercise_info .description_box .description").html(t.description),Number(t.allow_retake)?$(".exercise_info .meta").html("Number of attempts: <strong>unlimited</strong>"):$(".exercise_info .meta").html("Number of attempts: <strong>"+t.attempts+"</strong>"),$(".exercise_info .meta").append(t.time_limit>0?" | Time limit: <strong>"+t.time_limit+"</strong>":"")}}))}),$(document,n).click(function(){n.removeClass("active"),o.hide(),t=!1})})}),$.fn.loadYouTubeAPI=function(){var e=document.createElement("script"),t=document.getElementsByTagName("script")[0];e.src="https://www.youtube.com/iframe_api",t.parentNode.insertBefore(e,t)},$.fn.clickAction=function(){$(this).on("click",function(){if(!$(this).hasClass("disabled")){if($(this).hasClass("rewind")){var e=Number($(this).data("length")),t=video.player.getCurrentTime(),i=null;e=e>=t?0:t-e,$(".btn.disabled").length&&(i=$(".btn.disabled"),i.each(function(){$(this).find("span.progress").stop().animate({width:0},1e3)})),video.player.pauseVideo(),video.player.seekTo(e),updateProgress(),$("#videoPlayBtn").html('<span class="icon-paused"></span><br /><small>PAUSED</small>').addClass("paused").show(),$(".lbplus_status_msg").html("Video paused ... will resume shortly.").removeClass("hide").addClass("blink"),setTimeout(function(){$("#videoPlayBtn").hide().removeClass("paused").html("START"),$(".lbplus_status_msg").html("").addClass("hide").removeClass("blink"),video.player.playVideo(),null!==i&&i.extendedCooldown()},3e3),video.rewinded=!0}$(this).addTag(),$(this).cooldown()}})},$.fn.addTag=function(){var e=video.player.getCurrentTime(),t=$(this).data("action-id"),i=$(this).find(".action_name").html(),s=moment(1e3*e).format("mm:ss"),n=$(".progress_bar .progressed").width()+10,o=$(this).find(".icon").html(),a='<span class="tag" data-action-id="'+t+'" data-time="'+s+'" data-count="'+tagCount+'" style="left:'+n+"px;z-index:"+tagCount++ +'">'+o+"</span></span>",d={id:t,name:i,timestamped:s};studentResponses.push(d),$(".progress_bar_holder").prepend(a)},$.fn.tagHoverAction=function(){$(".progress_bar_holder").on("mouseover",".tag",function(){$(this).css("z-index",99)}),$(".progress_bar_holder").on("mouseout",".tag",function(){$(this).css("z-index",$(this).data("count"))})},$.fn.cooldown=function(){var e=$(this).index(".btn"),t=$(this),i=t.width(),s=1e3*Number(t.attr("data-cooldown")),n=t.find(".cooldown .progress"),o=$(n.selector+":eq("+e+")"),a=$(this).find(".limits"),d=Number(a.html());o.width()>=i&&(o.width(0),t.addClass("disabled")),d--,a.html(d),0>=d?$(this).addClass("disabled"):o.animate({width:i},s,function(){t.removeClass("disabled")})},$.fn.extendedCooldown=function(){$(this).each(function(){var e=$(this),t=e.width(),i=1e3*Number(e.attr("data-cooldown")),s=5*i,n=e.find(".cooldown .progress");n.animate({width:t},s,function(){e.removeClass("disabled")})})},$.fn.showTransition=function(e,t){$(this).prepend('<div class="transition_overlay"><div class="heading">'+e+'</div><div class="subheading">'+t+'</div><div class="loading"><span class="icon-spinner spin"></span></div></div>'),$(".transition_overlay").css("display","none").fadeIn()},$.fn.hideTransition=function(){$(".transition_overlay").fadeOut(function(){$(this).remove()})},$.fn.writeToFile=function(){studentResponses.length<=0&&(studentResponses=-1),$.post("includes/student_input.php",{student:studentResponses},function(e){e&&$.get("includes/views/score_view.php",function(e){$.fn.hideTransition(),$(".lbplus_wrapper .lbplus_container").html(e).hide().fadeIn(1e3)})})};