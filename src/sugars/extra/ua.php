<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

use froq\util\Util;

/**
 * Gets user agent.
 *
 * @param  bool $cut
 * @return ?string
 */
function get_user_agent(bool $cut = true): ?string
{
    static $ret; $ret ?? $ret = (
        Util::getClientUserAgent()
    );

    if ($ret && $cut) {
        $ret = substr($ret, 0, 250); // Safe.
    }

    return $ret;
}

/**
 * Detect whether user agent is a bot or not.
 *
 * @return bool
 */
function is_bot(): bool
{
    static $ret; if ($ret === null) {
        $ret = !($ua = get_user_agent()) || (
            stripos($ua, '+http') !== false || // Some speed..
            preg_match('~bot|slurp|(crawl|archiv|spid)er|fetch‎(er)?~i', $ua) ||
            preg_match('~google|yahoo|yandex|bing|msn|lycos|baidu|altavista|netcraft|alexa~i', $ua) ||
            preg_match('~facebook|whatsapp|(twit|tweet.*)~i', $ua) ||
            preg_match('~'
                .'zyborg|rambler|scooter|estyle|scrubby|scrapy|aspseek|accoona|jack|peerindex'
                .'|topsy|butterfly|ningmetauri|js-kit|unwindfetchor|kraken|digg|twit|tweet|inagist|longurl|newsme'
                .'|mail\.ru|ia_?archiver|voyager|goo|wordpress|amsu|mj12bot|majestic\d*'
                .'|(link|url).*(controller|resolver|checker)'
                .'|ahref|grapeshot|semrush|bbbike|sogou|ichiro|youdao|cyberduck|iframely|openlinkprofiler'
                .'|curl|libwww|wget|moget|lwp|java|nmap'
                .'|cortex|adreview|ttd-content|admantx'
            .'~i', $ua)
        );
    }

    return !!$ret;
}

/**
 * Detect whether user agent is a Google bot or not.
 *
 * @return bool
 */
function is_google_bot(): bool
{
    static $ret; if ($ret === null) {
        $ret = ($ua = get_user_agent()) && (
            preg_match('~'
                .'Googlebot'
                .'|(AdsBot|Mediapartners|FeedFetcher|DuplexWeb)-Google'
                .'|Google(-Read-Aloud| Favicon)'
            .'~i', $ua)
        );
    }

    return !!$ret;
}

/**
 * Detect whether user agent is a mobile browser or not.
 *
 * @return bool
 */
function is_mobile(): bool
{
    static $ret; if ($ret === null) {
        $ret = ($ua = get_user_agent()) && (
            stripos($ua, 'mobile') !== false || // Some speed..
            preg_match('~android|ip(hone|ad|od)|opera *m(ob|in)i|windows *(ce|phone)|blackberry|bb\d+~i', $ua) ||
            preg_match('~bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|webos|wos~i', $ua) ||
            // Source: http://detectmobilebrowsers.com/
            preg_match('/meego|avantgo|bada\/|blazer|compal|elaine|fennec|iris|kindle|lge |maemo|midp|mmp|netfronti|palm( os)?|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|xda|xiino/i', $ua) ||
            preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($ua, 0, 4))
        );
    }

    return !!$ret;
}

/**
 * Detect whether user agent is a tablet browser or not.
 *
 * @return bool
 */
function is_tablet(): bool
{
    static $ret; if ($ret === null) {
        $ret = ($ua = get_user_agent()) && (
            preg_match('~tablet|ipad~i', $ua) || (
                stripos($ua, 'silk') !== false && stripos($ua, 'mobile') === false
            )
        );
    }

    return !!$ret;
}

/**
 * Detect whether user agent is a desktop browser or not.
 *
 * @return bool
 */
function is_desktop(): bool
{
    return !is_mobile() && !is_tablet();
}
