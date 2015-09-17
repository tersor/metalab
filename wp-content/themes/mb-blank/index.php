<?php get_header(); ?>
<?php
    // Start the loop.
    while ( have_posts() ) : the_post();

    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

      <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
      </header><!-- .entry-header -->

      <div class="entry-content">
        <?php the_content(); ?>
      </div><!-- .entry-content -->

    </article>
    <?php

    // End the loop.
    endwhile;
    ?>
<?php get_footer(); ?>