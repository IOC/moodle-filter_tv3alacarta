<?php
//  TV3 a la carta filter plugin for Moodle
//  Copyright © 2012  Institut Obert de Catalunya
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  TV3 a la carta filtering
 *
 *  This filter will replace any tv3 a la carta link with an embedded player
 *
 * @package    filter
 * @subpackage tv3alacarta
 * @copyright  Marc Català  {mcatala@ioc.cat}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_tv3alacarta extends moodle_text_filter {

    function filter($text, array $options = array()) {
        global $CFG;

        if (!is_string($text) or empty($text)) {
            // non string data can not be filtered anyway
            return $text;
        }

        $newtext = '';
        $match = '~<a\s[^>]*href="https?://www\.tv3\.cat/(?:3alacarta/#/)?videos/([0-9]+)(?:\/[^"]*)?"[^>]*>(.*?)</a>~is';
        if (preg_match($match, $text, $matches)) {
            $video = $this->filter_tv3_alacarta($matches);
            $newtext = preg_replace($match, $video, $text);
        }
        if (empty($newtext)) {
            // error or not filtered
            unset($newtext);
            return $text;
        }

        return $newtext;
    }


    /**
    * Change TV3 links into embedded TV3 videos
    *
    * @param  $link
    * @return string
    */
    function filter_tv3_alacarta($link) {
        global $CFG;

        $videoid = $link[1];
        $info    = s(strip_tags($link[2]));

        $width  = CORE_MEDIA_VIDEO_WIDTH;
        $height = CORE_MEDIA_VIDEO_HEIGHT;

        $output = <<<OET
<span class="mediaplugin mediaplugin_tv3alacarta">
<object title="$info" type="application/x-shockwave-flash" data="http://www.tv3.cat/ria/players/3ac/evp/Main.swf" width="$width" height="$height" id="EVP{$videoid}IE">
 <param name="movie" value="http://www.tv3.cat/ria/players/3ac/evp/Main.swf" />
 <param name="scale" value="noscale" />
 <param name="align" value="tl" />
 <param name="swliveconnect" value="true" />
 <param name="menu" value="true" />
 <param name="allowFullScreen" value="true" />
 <param name="allowScriptAccess" value="always" />
 <param name="wmode" value="transparent" />
 <param name="FlashVars" value="themepath=themes/evp_advanced.swf&amp;videoid=$videoid&amp;subtitols=true&amp;refreshlock=true&amp;haspodcast=true&amp;controlbar=true&amp;basepath=http://www.tv3.cat/ria/players/3ac/evp/&amp;backgroundColor=#ffffff&amp;opcions=true&amp;comentaris=false&amp;votacions=true&amp;relacionats=true&amp;hasinsereix=true&amp;instancename=playerEVP_0_$link[1]&amp;hassinopsi=true&amp;hascomparteix=true&amp;hasrss=true&amp;mesi=true&amp;hasenvia=true&amp;minimal=false&amp;autostart=false&amp;relacionats_canals=true&amp;basepath=http://www.tv3.cat/ria/players/3ac/evp/&amp;xtm=true" />
 <param name="allowFullScreen" value="true" />
$link[0]</object>
</span>
OET;
        return $output;
    }
}