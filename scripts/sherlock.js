/*
 * LiveButtons+
 * @author: Ethan Lin, Mike Kellum
 * @uri: https://github.com/oel-mediateam/sherlock
 * @version: 0.0.1 (alpha)
 * @license: The Artistic License 2.0
 *
 * Copyright (c) 2015 University of Wisconsin-Extension,
 * Divison of Continuing Education, Outreach & E-Learning
 *
*/
function onYouTubeIframeAPIReady(){var e={autoplay:0,controls:0,disablekb:0,enablejsapi:0,iv_load_policy:3,loop:0,modestbranding:1,rel:0,showinfo:0};video.segmented&&(e.start=video.start,e.end=video.end),video.player=new YT.Player(video.selector,{width:"640",height:"360",videoId:video.vId,playerVars:e}),video.player.addEventListener("onReady",function(){video.segmented?video.duration=video.end-video.start:video.duration=video.player.getDuration(),trainingMode&&$.post("includes/get_exercise_from_session.php",{id:1},function(e){for(var t=e.length,i=$(".tag_hints_holder").width(),s=0;t>s;s++)for(var n=e[s].positions.length,a=0;n>a;a++){var o=$.fn.toSecond(e[s].positions[a].begin),r=$.fn.toSecond(e[s].positions[a].end),d=(r-o)/2+o,l=Math.floor(i*(100/video.duration*o)/100),c=Math.floor(i*(100/video.duration*r)/100),p=c-l,u=l+15+p/2,h='<span class="hint_tag" style="left:'+u+'px;"><span>'+$.fn.initialism(e[s].name)+"</span></span>";$(".tag_hints_holder").append('<div class="hint" style="left:'+l+"px; width:"+p+'px;" data-begin="'+o+'" data-end="'+r+'" data-mid="'+d+'" data-id="'+e[s].id+'"></div>'),$(".progress_bar_holder").append(h)}}),$(".progress_bar .time .duration").html(moment(1e3*video.duration).format("mm:ss")),$("#videoPlayBtn").html("START").removeClass("paused"),$("#videoPlayBtn").on("click",function(){$.post("includes/start_exercise.php",{begin:1},function(e){e>=1?($("#videoPlayBtn").hide(),video.player.playVideo()):$(".sherlock_wrapper").showTransition("SORRY!",'You already attempted this exercise.<br /><a href="?page=exercises">Back to Exercises</a>')})})}),video.player.addEventListener("onStateChange",function(e){var t=e.target.getPlayerState();switch(t){case YT.PlayerState.ENDED:$(".sherlock_wrapper").showTransition("Video Ended","Calculating results. Please wait..."),$("#videoPlayBtn").html("ENDED").show();for(var i=0;i<$(".btn[data-action-id]").length;i++)$(".btn[data-action-id]:eq("+i+")").addClass("disabled");$(".btn.rewind").addClass("disabled"),clearInterval(updatePrgrsInterval),$(".progress_bar .progressed").css("width",$(".progress_bar").width()+"px"),$(".progress_bar .scrubber").css("left",$(".progress_bar").width()+"px"),$(".progress_bar .time .elapsed").html(moment(1e3*video.duration).format("mm:ss")),setTimeout(function(){$.fn.writeToFile()},3e3);break;case YT.PlayerState.PLAYING:if(!video.started){for(var s=0;s<$(".btn[data-action-id]").length;s++)$(".btn[data-action-id]:eq("+s+")").removeClass("disabled"),$(".btn[data-action-id]:eq("+s+")").clickAction();$(".btn.rewind").removeClass("disabled"),$(".btn.rewind").clickAction(),video.started=!0}updatePrgrsInterval=setInterval(function(){$.fn.updateProgress(video)},100),$.fn.tagHoverAction(),$("#videoPlayBtn").hide().removeClass("paused").html("START");break;case YT.PlayerState.BUFFERING:$("#videoPlayBtn").html('<span class="icon-spinner"></span><br /><small>BUFFERING</small>').addClass("paused").show();for(var n=0;n<$(".btn[data-action-id]").length;n++)$(".btn[data-action-id]:eq("+n+")").addClass("disabled");break;case YT.PlayerState.PAUSED:clearInterval(updatePrgrsInterval)}})}var video={player:null,selector:"ytv",vId:null,segmented:!1,start:0,end:0,duration:null,rewinded:!1,started:!1},tagCount=0,updatePrgrsInterval,studentResponses=[],trainingMode=!1,pauseOnce=!0,preCount=null;$(function(){"use strict";if($("#google_revoke_connection").length&&$("#google_revoke_connection").click(function(){return $("#disconnect-confirm").dialog({dialogClass:"no-close",title:"Disconnect Google Account",position:{my:"center",at:"center",of:$(".signin_view")},resizable:!1,draggable:!1,width:300,height:215,modal:!0,buttons:{OK:function(){$(this).dialog("close"),$(".sherlock_wrapper").showTransition("Revoke Access","Disconnecting Google account. Please wait..."),setTimeout(function(){$.post("includes/disconnect_google.php",{revoke:1},function(){location.reload()})},3e3)},Cancel:function(){$(this).dialog("close")}}}),!1}),$("#videoPlayBtn").html('<span class="icon-spinner"></span><br /><small>WAIT</small>').addClass("paused"),"training"===$(".sherlock_view").data("mode")&&($(".sherlock_mode_msg").html("Training Mode").removeClass("hide"),trainingMode=!0),video.vId=$("#"+video.selector).data("video-id"),video.vId){var e=$("#"+video.selector).data("start"),t=$("#"+video.selector).data("end");e!==Number("-1")&&(video.start=moment.duration(e,"mm:ss").asSeconds()/60,video.end=moment.duration(t,"mm:ss").asSeconds()/60,video.start>=0&&void 0!==video.start&&video.start<video.end&&(video.segmented=!0)),$.fn.loadYouTubeAPI()}$("select").length&&$("select").each(function(){var e=$(this),t=!1,i=$(this).children("option").length,s=e.attr("class");e.addClass("select-hidden"),e.wrap('<div class="select"></div>'),e.after('<div class="select-styled"></div> ');var n=e.next("div.select-styled");n.addClass(s),n.text(e.children("option").eq(0).text());for(var a=$("<ul />",{"class":"select-options"}).insertAfter(n),o=0;i>o;o++)$("<li />",{text:e.children("option").eq(o).text(),rel:e.children("option").eq(o).val()}).appendTo(a);var r=a.children("li");n.click(function(e){e.stopPropagation(),t?(n.removeClass("active"),a.hide(),t=!1):($("div.select-styled.active").each(function(){$(this).removeClass("active").next("ul.select-options").hide()}),$(this).toggleClass("active").next("ul.select-options").toggle(),t=!0)}),r.click(function(i){i.stopPropagation();var s=$(this).attr("rel");n.text($(this).text()).removeClass("active"),e.val(s),a.hide(),t=!1,$(".selection_view").length&&($(".exercise_info").remove(),$.post("includes/exercise_info.php",{id:s},function(e){if(e){var t=JSON.parse(e);$(".select").after('<div class="exercise_info"><div class="description_box"><p><strong>Description:</strong></p><div class="description"></div></div><p class="meta"></p></div>'),$(".exercise_info .description_box .description").html(t.description),Number(t.allow_retake)?$(".exercise_info .meta").html("Number of attempts: <strong>unlimited</strong>"):$(".exercise_info .meta").html("Number of attempts: <strong>"+t.attempts+"</strong>"),$(".exercise_info .meta").append(t.exrs_type_id>0?" | Exercise type: <strong>"+$.fn.getExerciseType(t.exrs_type_id)+"</strong>":"")}}))}),$(document,n).click(function(){n.removeClass("active"),a.hide(),t=!1})}),$("#lti_selection").click(function(){var e=$('input[name="return_url"]').val(),t=$("option:selected").val(),i=$('input[name="type"]').val();return"hide"!==t?$.ajax({url:"includes/get_lti_link.php",type:"POST",data:{return_url:e,id:t,type:i},success:function(e){window.location.href=e}}):$("h1").after('<div class="callout danger">No exercise was selected. Please select an exercise.</div>'),!1})}),$.fn.loadYouTubeAPI=function(){var e=document.createElement("script"),t=document.getElementsByTagName("script")[0];e.src="https://www.youtube.com/iframe_api",t.parentNode.insertBefore(e,t)},$.fn.clickAction=function(){$(this).on("click",function(){if(!$(this).hasClass("disabled")){if($(this).hasClass("rewind")){var e=Number($(this).data("length")),t=video.player.getCurrentTime(),i=null;e=e>=t?0:t-e,$(".btn.disabled").length&&(i=$(".btn.disabled"),i.each(function(){$(this).find("span.progress").stop().animate({width:0},1e3)})),video.player.pauseVideo(),video.player.seekTo(e),preCount=null,$("#videoPlayBtn").html('<span class="icon-paused"></span><br /><small>PAUSED</small>').addClass("paused").show(),$(".sherlock_status_msg").html("Video paused ... will resume shortly.").removeClass("hide").addClass("blink"),setTimeout(function(){$(".sherlock_status_msg").html("").addClass("hide").removeClass("blink"),video.player.playVideo(),null!==i&&i.extendedCooldown()},3e3),video.rewinded=!0}else trainingMode&&(video.player.playVideo(),pauseOnce=!1);$(this).addTag(),$(this).cooldown()}})},$.fn.addTag=function(){var e=video.player.getCurrentTime(),t=$(this).data("action-id"),i=$(this).find(".action_name").html(),s=moment(1e3*e).format("mm:ss"),n=$(".progress_bar .progressed").width()+10,a=$(this).find(".icon").html(),o='<span class="tag" data-action-id="'+t+'" data-time="'+s+'" data-count="'+tagCount+'" style="left:'+n+"px;z-index:"+tagCount++ +'">'+a+"</span>",r={id:t,name:i,timestamped:s};studentResponses.push(r),$(".progress_bar_holder").prepend(o)},$.fn.tagHoverAction=function(){$(".progress_bar_holder").on("mouseover",".tag",function(){$(this).css("z-index",99)}),$(".progress_bar_holder").on("mouseout",".tag",function(){$(this).css("z-index",$(this).data("count"))}),trainingMode&&($(".progress_bar_holder").on("mouseover",".hint_tag",function(){$(this).css("z-index",99)}),$(".progress_bar_holder").on("mouseout",".hint_tag",function(){$(this).css("z-index",0)}))},$.fn.cooldown=function(){var e=$(this).index(".btn"),t=$(this),i=t.width(),s=1e3*Number(t.attr("data-cooldown")),n=t.find(".cooldown .progress"),a=$(n.selector+":eq("+e+")"),o=$(this).find(".limits"),r=Number(o.html());a.width()>=i&&(a.width(0),t.addClass("disabled")),r--,o.html(r),0>=r?$(this).addClass("disabled"):a.animate({width:i},s,function(){t.removeClass("disabled")})},$.fn.extendedCooldown=function(){$(this).each(function(){var e=$(this),t=e.width(),i=1e3*Number(e.attr("data-cooldown")),s=5*i,n=e.find(".cooldown .progress");n.animate({width:t},s,function(){e.removeClass("disabled")})})},$.fn.showTransition=function(e,t,i){i="undefined"!=typeof i?i:!1,$.fn.hideTransition(),i===!1?$(this).prepend('<div class="transition_overlay"><div class="heading">'+e+'</div><div class="subheading">'+t+'</div><div class="loading"><span class="icon-spinner spin"></span></div></div>'):$(this).prepend('<div class="transition_overlay"><div class="heading">'+e+'</div><div class="subheading">'+t+"</div></div>"),$(".transition_overlay").css("display","none").fadeIn()},$.fn.hideTransition=function(){$(".transition_overlay").fadeOut(function(){$(this).remove()})},$.fn.writeToFile=function(){studentResponses.length<=0&&(studentResponses=-1),$.post("includes/student_input.php",{student:studentResponses},function(e){1===e||"1"===e?$.get("includes/views/score_view.php",function(e){$.fn.hideTransition(),$(".sherlock_wrapper .sherlock_container").html(e).hide().fadeIn(1e3)}):$(".sherlock_wrapper").showTransition("Something went wrong...","Sherlock lost his writting pen.")})},$.fn.updateProgress=function(e){var t=e.player.getCurrentTime();e.segmented&&(t=e.player.getCurrentTime()-e.start);var i=Math.floor(100/e.duration*t),s=$(".progress_bar").width()*(i/100),n=moment(1e3*t).format("mm:ss");if($(".progress_bar .progressed").css("width",s+"px"),$(".progress_bar .scrubber").css("left",s+"px"),$(".progress_bar .time .elapsed").html(n),trainingMode){var a=$(".scrubber").hitTestObject(".hint");if(a)for(var o=0;o<a.length;o++){var r=Number(a[o].attributes[2].nodeValue),d=Number(a[o].attributes[3].nodeValue),l=Number(a[o].attributes[4].nodeValue);if(t>r&&d>t){$(".progress_bar_holder .hint_tag:eq("+o+")").animate({opacity:1}),$(".progress_bar_holder .hint_tag:eq("+preCount+")").removeClass("blink-faster"),preCount!==o&&($(".progress_bar_holder .hint_tag:eq("+preCount+")").removeClass("blink-faster"),pauseOnce=!0,preCount=o),t>l&&d>t&&pauseOnce&&(e.player.pauseVideo(),$(".progress_bar_holder .hint_tag:eq("+o+")").addClass("blink-faster"),pauseOnce=!1);break}}}},$.fn.getExerciseType=function(e){var t=null;switch(Number(e)){case 1:t="Demonstration";break;case 2:t="Development Testing Purposes";break;case 3:t="Training";break;case 4:t="Assignment";break;default:t=null}return t},$.fn.toSecond=function(e){var t=e.split(":");return 60*Number(t[0])+Number(t[1])},$.fn.initialism=function(e){var t=e.indexOf(" "),i=e.slice(0,1);if(t>0){var s=e.slice(t+1,t+2);return i+s}return i},$.fn.hitTestObject=function(e){for(var t=$(e),i=this.size(),s=t.size(),n=0;i>n;n++)for(var a=this.get(n).getBoundingClientRect(),o=0;s>o;o++){var r=t.get(o).getBoundingClientRect();if(!(a.right<r.left||a.left>r.right||a.bottom<r.top||a.top>r.bottom))return $(e)}return!1};
//# sourceMappingURL=./sherlock.js.map