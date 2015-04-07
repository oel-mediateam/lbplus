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
var soundEffects={click:"click",powerUp:"power_up",odd:"no_mercy"};$(document).ready(function(){$.fn.loadSoundEffects();for(var n=0;n<$(".btn[data-action]").length;n++)$(".btn[data-action]:eq("+n+")").clicked();$(".progress_bar").progress()}),$.fn.loadSoundEffects=function(){$.each(soundEffects,function(n,s){var i=s;soundEffects[n]=document.createElement("audio"),soundEffects[n].setAttribute("src","sounds/"+i+".mp3")})},$.fn.clicked=function(){$(this).on("click",function(){$(this).hasClass("disabled")||($(this).hasClass("odd")?soundEffects.odd.play():soundEffects.powerUp.play(),$(this).cooldown())})},$.fn.cooldown=function(){var n=$(this).index(".btn"),s=$(this),i=s.width(),o=1e3*Number(s.attr("data-cooldown")),t=s.find(".cooldown .progress"),d=$(t.selector+":eq("+n+")");d.width()>=i&&(d.width(0),s.addClass("disabled")),d.animate({width:i},o,function(){s.removeClass("disabled")})},$.fn.progress=function(){var n=$(this),s=n.width(),i=n.find(".progressed"),o=$(i.selector);o.width()>=s&&o.width(0),o.animate({width:s},3e4,function(){setTimeout(function(){n.progress()},1e4)})},$.fn.showTransition=function(n,s){$(this).prepend('<div class="transition_overlay"><div class="heading">'+n+'</div><div class="subheading">'+s+'</div><div class="loading"><span class="icon-spinner spin"></span></div></div>'),$(".transition_overlay").css("display","none").fadeIn()},$.fn.hideTransition=function(){$(".transition_overlay").fadeOut(function(){$(this).remove()})};
//# sourceMappingURL=./lbplus.js.map