<?php

namespace ideasonpurpose;

class ShareLinks
{
    /**
     * Add action to wp_head which injects the Google analytics code snippet
     * @param string $ga_ua       The Google Analytics tracking ID
     * @param string $fallback_ua A placeholder ID for use in development
     */
    public function __construct($config = ['twitter' => true, 'linkedin'])
    {
        if (array_key_exists('facebook', $config) && !empty($config['facebook'])) {
            $this->fbAppId = $config['fb'];
            $this->facebookInit();
        }

        if (array_key_exists('twitter', $config) && (bool) $config['twitter']) {
            $this->twitterInit();
        }
    }

    private function facebookInit()
    {
        add_action('wp_head', [$this, injectFacebookSDK]);
    }

    public function injectFacebookSDK($appId)
    {
        if (!isset($this->fbAppId) || empty($this->fbAppId)) {
            return;
        }
        ?>
          <script>/* eslint-disable */
            window.fbAsyncInit = function() {
              FB.init({
                appId      : '<?= $this->fbAppId ?>',
                xfbml      : true,
                version    : 'v2.8'
              });
              FB.AppEvents.logPageView();
            };

            (function(d, s, id){
               var js, fjs = d.getElementsByTagName(s)[0];
               if (d.getElementById(id)) {return;}
               js = d.createElement(s); js.id = id;
               js.src = "//connect.facebook.net/en_US/sdk.js";
               fjs.parentNode.insertBefore(js, fjs);
             }(document, 'script', 'facebook-jssdk'));
          </script>
        <?php
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
    ?>
      <script>/* eslint-disable */
        window.twttr = (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
          t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function(f) {
          t._e.push(f);
        };

        return t;
      }(document, "script", "twitter-wjs"));</script>
    <?php
    }

    private function linkedinInit()
    {
        add_action('wp_head', [$this, 'injectTwitterWidgetsLib']);
    }


    public function injectLinkedIn()
    {
    ?>
        <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
    <?php
        // <script type="IN/Share" data-url="http://github.com/joemaller" data-counter="top"></script>
    }

}

