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
var soundEffects={click:"click",powerUp:"power_up",odd:"no_mercy"};$(document).ready(function(){$.fn.loadSoundEffects();for(var o=0;o<$(".btn[data-action]").length;o++)$(".btn[data-action]:eq("+o+")").cooldown(),$(".btn[data-action]:eq("+o+")").clicked();$(".progress_bar").progress()}),$.fn.loadSoundEffects=function(){$.each(soundEffects,function(o,t){var n=t;soundEffects[o]=document.createElement("audio"),soundEffects[o].setAttribute("src","sounds/"+n+".mp3")})},$.fn.clicked=function(){$(this).on("click",function(){$(this).hasClass("disabled")||($(this).hasClass("odd")?soundEffects.odd.play():soundEffects.powerUp.play())})},$.fn.cooldown=function(){var o=$(this),t=o.width(),n=1e3*Number(o.attr("data-cooldown")),e=o.find(".cooldown .progress"),s=$(e.selector);s.width()>=t&&(s.width(0),o.addClass("disabled")),s.animate({width:t},n,function(){o.removeClass("disabled"),setTimeout(function(){o.cooldown()},n+1e4)})},$.fn.progress=function(){var o=$(this),t=o.width(),n=o.find(".progressed"),e=$(n.selector);e.width()>=t&&e.width(0),e.animate({width:t},3e4,function(){setTimeout(function(){o.progress()},1e4)})};
//# sourceMappingURL=./lbplus.js.map