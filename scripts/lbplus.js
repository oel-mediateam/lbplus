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
var soundEffects={click:"click",powerUp:"power_up"};$(document).ready(function(){$.fn.loadSoundEffects();for(var t=0;t<$(".btn[data-action]").length;t++)$(".btn[data-action]:eq("+t+")").cooldown(),$(".btn[data-action]:eq("+t+")").clicked();$(".progress_bar").progress()}),$.fn.loadSoundEffects=function(){$.each(soundEffects,function(t,o){var n=o;soundEffects[t]=document.createElement("audio"),soundEffects[t].setAttribute("src","sounds/"+n+".mp3")})},$.fn.clicked=function(){$(this).on("click",function(){$(this).hasClass("disabled")||soundEffects.powerUp.play()})},$.fn.cooldown=function(){var t=$(this),o=t.width(),n=1e3*Number(t.attr("data-cooldown")),e=t.find(".cooldown .progress"),s=$(e.selector);s.width()>=o&&(s.width(0),t.addClass("disabled")),s.animate({width:o},n,function(){t.removeClass("disabled"),setTimeout(function(){t.cooldown()},n+1e4)})},$.fn.progress=function(){var t=$(this),o=t.width(),n=t.find(".progressed"),e=$(n.selector);e.width()>=o&&e.width(0),e.animate({width:o},3e4,function(){setTimeout(function(){t.progress()},1e4)})};
//# sourceMappingURL=./lbplus.js.map