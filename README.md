# wp-social-share-links

Initialize an instance of the library by defining which services will be initialized. These services will have their JS SDKs injected in `wp_head`.

Facebook's rich share functions require an appID token, initialize the library with your app ID to enable Facebook sharing. Twitter and LinkedIn are enabled by default, set them to false to disable.

Heres an example which initializes Facebook and Twitter but disables LinkedIn:

``` php
new ShareLink(['facebook' => '1223334444', 'linkedin' => false]);
```

To inject a custom text link, just call the static methods:

``` php
ShareLink::facebook('Share on Facebook');
```

Any image or other linkable HTML element can be used. Here's an example using an SVG blob:

``` php
$fb_icon = '<svg>...</svg>';
ShareLink::facebook($fb_icon);
```

The shared url defaults to the current post's permalink, but it can be customized by sending a second argument:

```php
$twitter_icon = '<img src="...">';
ShareLink::facebook($twitter_icon, 'http://github.com/ideasonpurpose');
```


### TODO: 

- Add Tests
- Make Facebook work without an appID (fallback to share url)
- installation instructions
- 
