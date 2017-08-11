<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package AskewBrook
 * @subpackage Bing Search
 * @since 0.0.3
 * @version 0.3
 */

get_header();?>

<style type="text/css">
	<?= get_option('abbs_bing_search_custom_css'); ?>
</style>

<div class="wrap">
	<header class="page-header">
		<h1 class="page-title">Search Results for: <?=$_GET['s'];?></h1>

	</header><!-- .page-header -->
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php if (get_option('abbs_bing_inline_search')): ?>
			<?php get_search_form() ?>
			<br>
		<?php endif ?>
		<?php if ($bing_query_object != 'ERROR' && $bing_query_object != 'NORESULTS'): ?>
			<?php foreach ($bing_query_object as $search_item): ?>

				<article>
					<header class="entry-header">
						<h2 class="entry-title"><a href="<?=$search_item->url;?>" rel="bookmark"><?=$search_item->name;?></a></h2>
					</header>
					<div class="entry-content">
						<p class="url"><?= $search_item->displayUrl ?></p>
						<?=$search_item->snippet;?>
					</div>
				</article>
			<?php endforeach;?>
			<div class="navigation clear">
				<div class="nav-previous alignleft"><?= $bing_query_options['prev_page'] ?></div>
				<div class="nav-next alignright"><?= $bing_query_options['next_page'] ?></div>
			</div>
		<?php elseif($bing_query_object == 'ERROR'): ?>
			<?php if (current_user_can('editor') || current_user_can('administrator')) {?>
			    <p>Search Error: Please check the API Key provided in the plugin settings.</p>
			<?php } else {?>
				<p class="error">Oops! Something has gone wrong! Please try again later.</p>
			<?php } ?>
		<?php else: ?>
			<p class="error">No search results found.</p>
		<?php endif;?>
		<hr>
		<p>Powered By Bing</p>
		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar();?>
</div><!-- .wrap -->

<?php get_footer();
