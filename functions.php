<?php

if (!function_exists('wp_post_heatmap_get_post_data')) {
    function wp_post_heatmap_get_post_data()
    {
        $posts = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'post',
            'post_status' => 'publish'
        ));
    
        $data = array();

        foreach ($posts as $post) {
            $date = get_the_date('Y-m-d', $post);
            $permalink = get_permalink($post);
            $title = get_the_title($post);
            $word_count = mb_strlen(strip_tags(strip_shortcodes($post->post_content)), 'UTF8');
    
            if (!isset($data[$date])) {
                $data[$date] = array();
            }
    
            $data[$date][] = array('link' => $permalink, 'word_count' => $word_count, 'url' => $permalink, 'title' => $title);
        }

        return $data; // Return the data array
    }
}

if (!function_exists('wp_heatmap_shortcode')) {
    function wp_heatmap_shortcode()
    {
        // Get heatmap data
        $heatmap_data = wp_post_heatmap_get_post_data();

        // Enqueue scripts
        wp_enqueue_script('echarts-js', plugin_dir_url(__FILE__) . 'js/echarts.min.js', array(), null, true);
        wp_enqueue_script('wp-heatmap-js', plugin_dir_url(__FILE__) . 'js/heatmap.js', array('jquery'), null, true);
        
        // Localize script with data
        wp_localize_script('wp-heatmap-js', 'heatmapData', array('posts' => $heatmap_data));

        return '<div id="heatmap" style="max-width: 700px; height: 110px; padding: 2px; text-align: center; margin: 0 auto;"></div>';
    }

    add_shortcode('heatmap', 'wp_heatmap_shortcode');
}

if (!function_exists('wp_heatmap_register_scripts')) {
    function wp_heatmap_register_scripts()
    {
        wp_register_script('echarts-js', plugin_dir_url(__FILE__) . 'https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js', array(), null, true);
        wp_register_script('wp-heatmap-js', plugin_dir_url(__FILE__) . 'js/heatmap.js', array('jquery'), null, true);
    }

    add_action('wp_enqueue_scripts', 'wp_heatmap_register_scripts');
}
?>

