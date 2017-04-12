<?php

namespace ideasonpurpose;

class ShareLink
{
    /**
     * Add action to wp_head which injects the Google analytics code snippet
     * @param string $ga_ua       The Google Analytics tracking ID
     * @param string $fallback_ua A placeholder ID for use in development
     */
    public function __construct($config = ['twitter' => true, 'linkedin' => true])
    {
        $config['twitter'] = isset($config['twitter']) ? $config['twitter'] :  true;
        $config['linkedin'] = isset($config['linkedin']) ? $config['linkedin'] :  true;
        $config['facebook'] = isset($config['facebook']) ? $config['facebook'] :  false;

        if (array_key_exists('facebook', $config) && !empty($config['facebook'])) {
            $this->fbAppId = $config['facebook'];
            $this->facebookInit();
        }
        if (array_key_exists('twitter', $config) && (bool) $config['twitter']) {
            $this->twitterInit();
        }
        if (array_key_exists('linkedin', $config) && (bool) $config['linkedin']) {
            $this->linkedinInit();
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
      $url = ($url) ?: get_the_permalink($post->ID);
      $onclick = "event.preventDefault(); window.FB.ui({ method: 'share', href: '$url'})";
      return sprintf(
        '<a class="facebook" onclick="%s" href="https://www.facebook.com/sharer/sharer.php?u=%s">%s</a>',
        $onclick,
        urlencode($url),
        $content
      );
    }

    private function facebookInit()
    {
        add_action('wp_head', [$this, 'injectFacebookSDK']);
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
      $url = ($url) ?: get_the_permalink($post->ID);
      $url = urlencode($url);
      $content = ($content) ?: 'Tweet';
      return sprintf('<a class="twitter" href="https://twitter.com/intent/tweet?url=%s">%s</a>', $url, $content);
    }

    private function twitterInit()
    {
        add_action('wp_head', [$this, 'injectTwitterWidgetsLib']);
    }

    /**
     * Inject Twitter's Widgets JS Library
     * https://dev.twitter.com/web/javascript/loading
     */
    public function injectTwitterWidgetsLib()
    {
      $sdk = file_get_contents(__DIR__ . '/src/lib/twitter-sdk.html');
      echo $sdk;
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
      $url = ($url) ?: get_the_permalink($post->ID);
      $urlencoded = urlencode($url);

      $onclick = "event.preventDefault(); IN.UI.Share().params({ url: '$url' }).place()";
      return sprintf(
        '<a class="linkedin" onclick="%s" href="https://www.linkedin.com/shareArticle?mini=true&url=%s">%s</a>',
        $onclick,
        urlencode($url),
        $content
      );
    }

    private function linkedinInit()
    {
        add_action('wp_head', [$this, 'injectLinkedInLib']);
    }


    public function injectLinkedInLib()
    {
      $sdk = file_get_contents(__DIR__ . '/src/lib/linkedin-sdk.html');
      echo $sdk;
    }

}
