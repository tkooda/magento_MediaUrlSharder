# magento_MediaUrlSharder
Support additional automatic domain name sharding in Magneto.


# ABOUT:
This module will just override Magento's stock `getMediaUrl()` function so that you're able to use '%d' in the Media Base URL to reference multiple media domain hostnames / aliases.

  To use this, just:

   1) Install this MediaUrlSharder module with `rsync -rR app /path/to/magento/html/`

   2) Configure your DNS and CDN (e.g. Amazon Web Services (AWS) CloudFront distribution) to support all of the domain name hostnames / aliases you'd like to use (10 by default).  e.g. it should answer for "https://media0.cdn.example.com/media/..." up to "https://media9.cdn.example.com/media/...".

   3) Configure this MediaUrlSharder module with (i.e.) : `INSERT INTO core_config_data (path,value) VALUES ('tkooda/mediaurlsharder/base_media_url','https://media%d.cdn.example.com/media/');`
      (this config with '%d' has to be separate from the stock 'web/unsecure/base_media_url' config string because some code still calls Mage::getBaseUrl('media') directly)


..then all calls to `getMediaUrl( $file )` will return a consistant (statistaically dispersed) hostname for each `$file`.
