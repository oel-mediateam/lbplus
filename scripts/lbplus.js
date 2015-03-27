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
var soundEffects={click:"click",powerUp:"power_up",odd:"no_mercy"};$(document).ready(function(){$.fn.loadSoundEffects();for(var t=0;t<$(".btn[data-action]").length;t++)$(".btn[data-action]:eq("+t+")").clicked();$(".progress_bar").progress()}),$.fn.loadSoundEffects=function(){$.each(soundEffects,function(t,o){var n=o;soundEffects[t]=document.createElement("audio"),soundEffects[t].setAttribute("src","sounds/"+n+".mp3")})},$.fn.clicked=function(){$(this).on("click",function(){$(this).hasClass("disabled")||($(this).hasClass("odd")?soundEffects.odd.play():soundEffects.powerUp.play(),$(this).cooldown())})},$.fn.cooldown=function(){var t=$(this).index(".btn"),o=$(this),n=o.width(),s=1e3*Number(o.attr("data-cooldown")),e=o.find(".cooldown .progress"),d=$(e.selector+":eq("+t+")");d.width()>=n&&(d.width(0),o.addClass("disabled")),d.animate({width:n},s,function(){o.removeClass("disabled")})},$.fn.progress=function(){var t=$(this),o=t.width(),n=t.find(".progressed"),s=$(n.selector);s.width()>=o&&s.width(0),s.animate({width:o},3e4,function(){setTimeout(function(){t.progress()},1e4)})};
//# sourceMappingURL=./lbplus.js.map