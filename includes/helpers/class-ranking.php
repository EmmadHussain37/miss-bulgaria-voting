<?php

if (!defined('ABSPATH')) {
    exit;
}


class MBV_Ranking
{

    public function __construct()
    {

        add_action(
            'save_post_mbv_candidate',
            array(__CLASS__, 'update_rankings')
        );

        add_filter(
            'posts_clauses',
            array(__CLASS__, 'filter_ranking_clauses'),
            PHP_INT_MAX,
            2
        );

    }

    /**
     * Intercept and force the orderby clause at PHP_INT_MAX priority to override
     * any theme ordering, menu_order, post order plugins, or default sorting.
     */
    public static function filter_ranking_clauses($clauses, $query)
    {
        if (!$query->get('mbv_force_ranking')) {
            return $clauses;
        }

        global $wpdb;

        // Ensure postmeta is LEFT JOINed so candidates with 0 votes or no meta are still returned
        if (strpos($clauses['join'], 'mbv_pm') === false) {
            $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS mbv_pm ON ({$wpdb->posts}.ID = mbv_pm.post_id AND mbv_pm.meta_key = '_mbv_votes') ";
        }

        // Primary: highest votes first (COALESCE to handle NULL/empty as 0)
        // Secondary tie-breaker: candidate creation date (post_date DESC)
        // Tertiary tie-breaker: post ID DESC (guarantees deterministic, stable order)
        $clauses['orderby'] = " COALESCE(CAST(mbv_pm.meta_value AS UNSIGNED), 0) DESC, {$wpdb->posts}.post_date DESC, {$wpdb->posts}.ID DESC ";

        $clauses['distinct'] = 'DISTINCT';

        return $clauses;
    }

    /**
     * Get candidates ordered strictly by votes descending with tie-breakers.
     *
     * @param int $posts_per_page
     * @param array $extra_args
     * @return WP_Query
     */
    public static function get_ranked_candidates($posts_per_page = -1, $extra_args = array())
    {
        $args = array_merge(array(
            'post_type'           => 'mbv_candidate',
            'post_status'         => 'publish',
            'posts_per_page'      => $posts_per_page,
            'suppress_filters'    => false,
            'mbv_force_ranking'   => true,
            'ignore_sticky_posts' => true,
        ), $extra_args);

        return new WP_Query($args);
    }

    /**
     * Update stored _mbv_rank meta for all published candidates.
     * Safe against recursive calls.
     *
     * @param int $post_id
     */
    public static function update_rankings($post_id = 0)
    {
        static $updating = false;

        if ($updating) {
            return;
        }

        $updating = true;

        $candidates = self::get_ranked_candidates(-1);

        $rank = 1;

        if (!empty($candidates->posts)) {
            foreach ($candidates->posts as $candidate) {
                $cand_id = is_object($candidate) ? $candidate->ID : intval($candidate);

                update_post_meta(
                    $cand_id,
                    '_mbv_rank',
                    $rank
                );

                $rank++;
            }
        }

        $updating = false;
    }

}