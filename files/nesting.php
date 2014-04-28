<?php
/*
 * Plugin Name: Nested Shortcode
 * Plugin URI: http://jltweb.info/realisations/wp-parallax-content-plugin/
 * Description: A customizable JQuery content slider with CSS3 animations and parallax effects
 * Author URI: http://jltweb.info/
 * Author: Julien Le Thuaut (MBA Multimedia)
 * Version: 0.9.6
 * Licence: GPLv2
 *
 */
$tags_array=array();
$ebs_tags_array=array('toggles','toggle','restabs','restab','list','li','notification','row','column','table','table_head','th_column','table_body','table_row','row_column','tooltip','iconheading','panel','panel-header','panel-content','panel-footer','oscpopover','dropdown','dropdownhead','dropdownbody','dropdownitem','label','well','buttongroup','btngrptoolbar','dl','dlitem','frame','pt_row','pt_column','pt_column_head','pt_column_features','feature','pt_button','testimonial','sectionheading','dropcaps','separator','lead','pageheader','servicebox','highlights');
function parsenestedshortcode($input='',$tag='',$counter=0){
    global $tags_array;
    $counter++;
    $withtag='';
    if(($pos = strpos($input,'['.$tag.' '))!==false){
        $withtag=' ';
    }else if(($pos = strpos($input,'['.$tag.']'))!==false){
        $withtag=']';
    }
    if($pos!==false){
        $input = preg_replace('/\['.$tag.$withtag.'/','['.$counter.'_'.$tag.$withtag,$input,1);
        $temp_str =  substr($input,0, $pos+strlen('['.$counter.'_'.$tag));
        $input = substr($input,$pos+strlen('['.$counter.'_'.$tag));
        $input = parsenestedshortcode($input,$tag,$counter);
        $input = preg_replace('/\[\/'.$tag.'\]/','[/'.$counter.'_'.$tag.']',$input,1);
        $temp_str .= $input;
        $tags_array[$counter.'_'.$tag]=$tag;
    } else {
        $temp_str = $input;
    }

    return $temp_str;
}

function shortcode_inception($content) {
    global $shortcode_tags,$tags_array,$ebs_tags_array;
    if (empty($shortcode_tags) || !is_array($shortcode_tags))
        return $content;

    foreach($shortcode_tags as $tag=>$method){
        if(in_array($tag,$ebs_tags_array)){
            $content=parsenestedshortcode($content,$tag);
        }
    }
    if(count($tags_array)){
        foreach($tags_array as $func=>$t){
            $shortcode_tags[$func]=$shortcode_tags[$t];
        }
    }

    $pattern = get_shortcode_regex();
    return preg_replace_callback( "/$pattern/s", 'do_shortcode_tag', $content );

}

// remove the native do_shortcode filter
remove_filter('the_content', 'do_shortcode', 11);
// replace the filter with the shortcode_inception function
add_filter('the_content', 'shortcode_inception', 11);

