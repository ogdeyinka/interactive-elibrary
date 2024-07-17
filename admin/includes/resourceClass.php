<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of resourceClass
 *
 * @author OG Deyinka
 */
class resourceClass {
    //put your code here
    function  Resinputs($request_values){
             //Start of Non-Ajax File upload
    if (isset($request_values['r_icon'])) {ImageIconUpload (); }
    global $link_names,$source,$r_info,$link_ids,$tags,$res_id,$multilink,$subpage_show,$restypes;
    $res_id = filter_var($request_values['resource_id'], FILTER_SANITIZE_NUMBER_INT);
    $r_iconname = filter_var($request_values['r_img'], FILTER_SANITIZE_STRING);
    $r_title = filter_var($request_values['r_title'], FILTER_SANITIZE_STRING);
    $r_info = filter_var($request_values['r_info'], FILTER_SANITIZE_STRING);
  //  $r_info = htmlentities(filter_var(esc($request_values['r_info']), FILTER_SANITIZE_STRING));
    $source = filter_var($request_values['source'], FILTER_SANITIZE_STRING);
    $link_names = $request_values["link_name"];
     $link_urls = $request_values["link_url"];
    if (isset($request_values["link_ids"])) { $link_ids = $request_values["link_ids"]; }
    if (isset($request_values["restypes"])) {$restypes = $request_values["restypes"]; }
    if (isset($request_values["rlevels"])) { $rlevels = $request_values["rlevels"]; }
    if (isset($request_values['topics_id'])) {$topics_id = explode(',', $request_values['topics_id']);}
    if (isset($request_values['r_tags'])) {$tags = explode(',', $request_values['r_tags']);}
    if (!isset($request_values['subjectshow'])) { $subpage_show = 0;}else{$subpage_show = 1;}
    if (count($request_values["link_url"]) > 1) { $multilink = 1; } else {$multilink = 0;}
    // validate form
    inputsError($r_iconname,"Resource icon image is required");
    inputsError($r_title,"Resource title is required.");
    inputsError($topics_id,"Atleast a topic is required for your resource.");
    inputsError($link_urls,"Resource link URL is required and must be valid URL");
    inputsError($rlevels,"Resource School level is required, select atleast one level");
    inputsError($restypes,"Atleast a resource type is required");
      }

}
