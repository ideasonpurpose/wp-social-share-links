<?php

namespace IdeasOnPurpose;

class ShareLink
{
    /**
     * Setup the social share libraries, inject register ids and inject scripts
     *
     * Twitter and LinkedIn should be booleans, but facebook requires an appID to
     * iniitalize, so $config['facebook'] should be the appID
     *
     * @param  array  $config  keyed array for Twitter, LinkedIn and Facebook
     * @param  boolean $scriptInHead Where to inject the scripts, wp_head or wp_footer
     * @return void
     */
    public function __construct($config = ['twitter' => true, 'linkedin' => true], $scriptInHead = false)
    {
        $config['twitter'] = $config['twitter'] ?? true;
        $config['linkedin'] = $config['linkedin'] ?? true;
        $config['facebook'] = $config['facebook'] ?? false;

        $scriptLocation = $scriptInHead ? 'wp_head' : 'wp_footer';

        if (!empty($config['facebook'])) {
            $this->fbAppId = $config['facebook'];
            add_action($scriptLocation, [$this, 'injectFacebookSDK']);
        }
        if ((bool) $config['twitter']) {
            add_action($scriptLocation, [$this, 'injectTwitterWidgetsLib']);
        }
        if ((bool) $config['linkedin']) {
            add_action($scriptLocation, [$this, 'injectLinkedInLib']);
        }
    }

    /**
     * Returns a link tag for sharing to Facebook
     * @param  string $content The content which will be linked, can be an image tag, svg or text... Anything linkable
     * @param  string $url     The url to share, defaults to the current $post's permalink
     * @return string          An a tag for sharing to Facebook via the Facebook JS SDK
     */
    public static function facebook($content = 'Share', $url = null)
    {
        global $post;
        $url = $url ?: get_the_permalink($post->ID);
        $onclick = "event.preventDefault(); window.FB.ui({ method: 'share', href: '$url'})";
        return sprintf(
            '<a class="facebook" onclick="%s" href="https://www.facebook.com/sharer/sharer.php?u=%s">%s</a>',
            $onclick,
            urlencode($url),
            $content
        );
    }

    public function injectFacebookSDK()
    {
        if (isset($this->fbAppId) && !empty($this->fbAppId)) {
            $sdk = file_get_contents(__DIR__ . '/src/lib/facebook-sdk.html');
            $sdk = str_replace('%%APPID%%', $this->fbAppId, $sdk);
            echo $sdk;
        }
    }

    /**
     * Returns a link tag for sharing to Twitter
     * @param  string $content The content which will be linked, can be an image tag, svg or text... Anything linkable
     * @param  string $url     The url to share, defaults to the current $post's permalink
     * @return string          An a tag for sharing to Twitter via the Twitter JS SDK
     */
    public static function twitter($content = 'Tweet', $url = null)
    {
        global $post;
        $url = $url ?: get_the_permalink($post->ID);
        $url = urlencode($url);
        $content = $content ?: 'Tweet';
        return sprintf('<a class="twitter" href="https://twitter.com/intent/tweet?url=%s">%s</a>', $url, $content);
    }

    /**
     * Inject Twitter's Widgets JS Library
     * https://dev.twitter.com/web/javascript/loading
     */
    public function injectTwitterWidgetsLib()
    {
        require __DIR__ . '/src/lib/twitter-sdk.html';
    }

    /**
     * Returns a link tag for sharing to Linkedin
     * @param  string $content The content which will be linked, can be an image tag, svg or text... Anything linkable
     * @param  string $url     The url to share, defaults to the current $post's permalink
     * @return string          An a tag for sharing to LinkedIn via the (undocumented) LinkedIn JS SDK
     */
    public static function linkedin($content = 'Share', $url = null)
    {
        global $post;
        $url = $url ?: get_the_permalink($post->ID);
        $urlencoded = urlencode($url);

        $onclick = "event.preventDefault(); IN.UI.Share().params({ url: '$url' }).place()";
        return sprintf(
            '<a class="linkedin" onclick="%s" href="https://www.linkedin.com/shareArticle?mini=true&url=%s">%s</a>',
            $onclick,
            urlencode($url),
            $content
        );
    }

    public function injectLinkedInLib()
    {
        require __DIR__ . '/src/lib/linkedin-sdk.html';
    }
}
