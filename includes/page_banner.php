<?php
/**
 * Reusable page banner component
 * 
 * Usage:
 * include_once 'includes/page_banner.php';
 * display_page_banner('Page Title', 'Optional subtitle text');
 */

/**
 * Display the page banner with consistent styling
 * 
 * @param string $title The page title
 * @param string $subtitle Optional subtitle or description
 * @param string $custom_bg Optional custom background image
 */
function display_page_banner($title, $subtitle = '', $custom_bg = '') {
    // Get settings for banner
    $banner_bg = !empty($custom_bg) ? $custom_bg : get_setting('page_banner_bg', 'assets/img/banner-bg.jpg');
    $banner_overlay = get_setting('page_banner_overlay', 'rgba(0, 0, 0, 0.6)');
    $banner_height = get_setting('page_banner_height', '200px');
    $banner_padding = get_setting('page_banner_padding', '50px 0');
    
    // Primary color for potential styling
    $primary_color = get_setting('primary_color', '#005bac');
    
    echo '<style>
        .page-banner {
            background: linear-gradient('.$banner_overlay.', '.$banner_overlay.'), url("'.$banner_bg.'");
            background-size: cover;
            background-position: center;
            color: white;
            padding: '.$banner_padding.';
            text-align: center;
            margin-bottom: 40px;
        }
        .page-banner h1 {
            color: #fff;
            margin-bottom: 15px;
        }
        .page-banner .lead {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            .page-banner {
                padding: 30px 0;
            }
            .page-banner .lead {
                font-size: 1rem;
            }
        }
    </style>';
    
    echo '<section class="page-banner">
        <div class="container">
            <h1>'.htmlspecialchars($title).'</h1>';
    
    if (!empty($subtitle)) {
        echo '<p class="lead">'.htmlspecialchars($subtitle).'</p>';
    }
    
    echo '</div>
    </section>';
}
?>
