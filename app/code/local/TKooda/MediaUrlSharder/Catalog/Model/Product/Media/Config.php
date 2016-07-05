<?php

class tkooda_MediaUrlSharder_Catalog_Model_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config {

    /**
     * Add ability to use "%d" in Media Base URL(s) where "%d" will be automatically replaced with the same
     * (statistically/evenly dispersed) integer (between 0 and $num_shards-1, inclusive) every time for any
     * given URL (NOTE: %d can result in a different integer if you change $num_shards, to ensure statistical/even
     * dispersment across that new range of shards).
     * 
     * e.g. if "Base Media URL" is configured with "http://media%d.cdn.example.com/media/",
     * and getConfig( "tkooda/mediaurlsharder/num_shards" ) == 9:
     *   getMediaUrl( "/media/imageA.png" ) will always return "http://media3.cdn.example.com/media/imageA.png"
     *   getMediaUrl( "/media/imageB.png" ) will always return "http://media8.cdn.example.com/media/imageB.png"
     *   etc..
     * 
     * Example usage: setup an AWS CloudFront CDN distribution with media[0-9].cdn.example.com aliases,
     *   and set "Base Media URL" to be "http://media%d.cdn.example.com/media/".
     * 
     * @return string
     */
    public function getMediaUrl( $file ) {
        
        $config_base_media_url = Mage::app()->getStore()->getConfig( "tkooda/mediaurlsharder/base_media_url" );
        
        if ( $config_base_media_url ) {
            $config_num_shards = Mage::app()->getStore()->getConfig( "tkooda/mediaurlsharder/num_shards" );
            $num_shards = (int) ( $config_num_shards ? $config_num_shards : 10 ); // default of 10 produces shards numbered 0-9
            
            /* implement core functionality of original getMediaUrl() but with our str_replace() on our own 'tkooda/mediaurlsharder/base_media_url' instead of the stock 'web/secure/base_media_url' so that Mage::getBaseUrl('media') can still be called directly */
            if ( substr( $file, 0, 1 ) == '/' ) {
                $media_url = str_replace( '%d', (string) ( hexdec( substr( sha1( $file ), 0, 15 ) ) % $num_shards ), $config_base_media_url ) . $file;
            } else {
                $media_url = str_replace( '%d', (string) ( hexdec( substr( sha1( $file ), 0, 15 ) ) % $num_shards ), $config_base_media_url ) . '/' . $file;
            }

        } else {
            $media_url = parent::getMediaUrl( $file );
        }
        
        Mage::log( "getMediaUrl(): " . $media_url ); // DEBUG
        return $media_url;
    }

}
