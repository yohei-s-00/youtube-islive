<?php
/*
Plugin Name: YoutubeIsLive
Plugin URI:
Description: live状況取得
Version: 1.0.0
Author: 
Author URI: 
License: GPLv2
*/
/**
 * [youtube_is_live] returns live string.
 * @return string live
*/

function json_get($url, $query = array(), $assoc = false) { 
    if ($query) $url .= ('?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986));
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
    $responseString = curl_exec($curl);
    curl_close($curl);
    return ($responseString !== false) ? json_decode($responseString, $assoc) : false;
}

add_shortcode( 'youtube_is_live', 'isLive_youtube' );
function youtube_is_init(){
    function isLive_youtube($atts){
        $atts = shortcode_atts(array(
            "id" => '',
            "key" => ''
        ),$atts);
        function get_livestatus($_viedo_id,$_api_key){
            $response = json_get('https://www.googleapis.com/youtube/v3/videos', array(
                'key' => $_api_key,
                'id' => $_viedo_id,
                'part'=>'snippet',
                'fields'=>'items(id,snippet(liveBroadcastContent))'
            ), true);
            if($response["items"]==null){
                return 'error';
            }
            return  $response["items"][0]["snippet"]["liveBroadcastContent"];
        }
        $response = get_livestatus($atts['id'],$atts['key']);
        
        if($response == "live"){
            $isLive = "IS LIVE";
        }else{
            $isLive = "準備中";
        };
        $output = "<p>{$isLive}</p>" . PHP_EOL;
        return $output;
    }
};
add_action('init', 'youtube_is_init');
?>