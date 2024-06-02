<?php




function wp_post_heatmap_enqueue_scripts() {
    wp_enqueue_script('echarts', 'https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js', array(), null, true);
    wp_enqueue_script('heatmap', plugin_dir_url(__FILE__) . 'js/heatmap.js', array('echarts'), null, true);
}
add_action('wp_enqueue_scripts', 'wp_post_heatmap_enqueue_scripts');

function wp_post_heatmap_shortcode() {
    ob_start();
    ?>
    <style>
    #heatmap {
        max-width: 700px;
        height: 110px;
        margin-bottom: 40px;
    }
    </style>
    <div id="heatmap"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('heatmap', 'wp_post_heatmap_shortcode');

function wp_post_heatmap_localize_script() {
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

    wp_localize_script('heatmap', 'heatmapData', array('posts' => $data));
}
add_action('wp_enqueue_scripts', 'wp_post_heatmap_localize_script');
