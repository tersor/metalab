<?php

add_action( 'save_post', array('_247_Products','updateProduct'), 10,2  );
add_action( 'create_term', array('_247_Products','saveCategory2'), 10, 1 );
add_action( 'edit_term', array('_247_Products','saveCategory2'), 10, 1 );


class _247_Products extends ProductService{

	function __construct(){
		parent::__construct();
	}

	function getProducts(){

		if ( $categories = $this->soap_getCategories() ){
			$this->insertCategories($categories);
		}

		$products = $this->soap_getProducts();

		// $products = null;
		if ( is_array($products) ){
			foreach ($products as $key => $Product) {
	    	$wp_post = self::findProductBy247Id($Product->Id);

	    	$wp_post_id = null;
	    	if ( $wp_post){
	    		$wp_post_id = $wp_post[0]->ID;
	    	}

	    	self::insertProduct($Product, $wp_post_id);
			}
		}
	}


	function insertTerm($new_term, $repeat = 0){
		$term_name = $new_term;

		$term_exists = term_exists( $new_term, 'prodcut_cat' );

		if ( $repeat > 0||$term_exists){
			$term_name .= '-'.$repeat;
		}

		$term = wp_insert_term( $term_name, 'product_cat' );

		if ( is_array($term) && isset($term['term_id']) ){
		 	return $term['term_id'];
		}
		else{
		 	return $this->insertTerm($new_term, ($repeat+1) );
		}
	}

	function insertCategories($categories){
		$_247_categories = get_option('247_categories');

		if ( !$_247_categories ){
			$_247_categories = array();
		}

		$cats = array();

		foreach ($categories as $key => $Category) {
			if ( !isset($_247_categories[$Category->Id]) ){
				$term_id = $this->insertTerm( $Category->Name );

				if ( is_numeric($term_id) ){
					$cats[$Category->Id]['name'] = $Category->Name;
					$cats[$Category->Id]['term_id'] = $term_id;
				}
				else{
					continue;
				}
			}
			else{
				$cats[$Category->Id] = $_247_categories[$Category->Id];
			}
		}

		update_option('247_categories', $cats);
	}

	public static function saveCategory2($term_id){

		$_247_Products = new _247_Products();
		if ( is_numeric($term_id) && !isset($_POST['update']) ){
			$_247_categories = get_option('247_categories');

			$category_id = null;
			if ( is_array($_247_categories) ){
				foreach ($_247_categories as $key => $category) {
					if ( isset($category['term_id']) && $category['term_id'] == $term_id ){
						$category_id = $key;
					}
				}
			}

			if ( $term = get_term_by( 'id', $term_id, 'product_cat') ){

				$args = array(
					'Id' 				=> $category_id,
					'Name' 			=> ( isset($_POST['name']) ) ? $_POST['name'] : $term->name,
					'No'				=> null,
					'ParentId' 	=> $term->parent
					);

				$result = $_247_Products->soap_saveCategories($args);

				if ( isset($result->SaveCategoriesResult->Category->Id) ){
					$_247_categories = get_option('247_categories');
					$_247_categories[$result->SaveCategoriesResult->Category->Id]['name'] = $result->SaveCategoriesResult->Category->Name;
					$_247_categories[$result->SaveCategoriesResult->Category->Id]['Name'] = $term_id;

					update_option('247_categories', $_247_categories);
				}
			}
		}
	}


	public static function insertProduct($Product, $wp_post_id = null){
		/*
	  [Id] => 1
	  [Name] => P30 Swirl Remover Foam (Blue) 150x30mm
	  [Stock] => 0.0000
	  [StatusId] => 1
	  [CategoryId] => 1
	  [InPrice] => 0.0000
	  [Price] => 108.0000
	  [No] => 89810
	  [DateChanged] => 2014-10-06T06:43:00Z
	  [Weight] => 0.0000
	  [MinimumStock] => 0.0000
	  [OrderProposal] => 0.0000
	*/
		$post = array(
     'post_author' 	=> 1,
     'post_content'	=> '',
     'post_status'	=>  ($Product->StatusId == 1) ?  "publish" : "draft",
     'post_title'		=> $Product->Name,
     'post_parent'	=> '',
     'post_type'		=> "product",
     'post_date'		=> $Product->DateChanged,
		);

		if( $wp_post_id ){
			$post['ID'] = $wp_post_id;
		}
      //Create post
     $post_id = wp_insert_post( $post );

    if($post_id){
    	update_post_meta( $post_id, '247_id', $Product->Id );

    	update_post_meta( $post_id, '_visibility', 'visible' );
			update_post_meta( $post_id, '_stock_status',  ( $Product->Stock > 0 ) ? 'instock' : null );
			update_post_meta( $post_id, 'total_sales', '0');
			// update_post_meta( $post_id, '_downloadable', 'yes');
			// update_post_meta( $post_id, '_virtual', 'yes');
			update_post_meta( $post_id, '_regular_price', $Product->Price );
			// update_post_meta( $post_id, '_sale_price', "1" );
			// update_post_meta( $post_id, '_purchase_note', "" );
			// update_post_meta( $post_id, '_featured', "no" );
			// update_post_meta( $post_id, '_weight', $Product->Weight );
			// update_post_meta( $post_id, '_length', "" );
			// update_post_meta( $post_id, '_width', "" );
			// update_post_meta( $post_id, '_height', "" );
			update_post_meta($post_id, '_sku', $Product->Price);
			// update_post_meta( $post_id, '_product_attributes', array());
			// update_post_meta( $post_id, '_sale_price_dates_from', "" );
			// update_post_meta( $post_id, '_sale_price_dates_to', "" );
			update_post_meta( $post_id, '_price', $Product->Price );
			// update_post_meta( $post_id, '_sold_individually', "" );
			update_post_meta( $post_id, '_manage_stock', "no" );
			update_post_meta( $post_id, '_backorders', "no" );
			update_post_meta( $post_id, '_stock', $Product->Stock );
    }

    $_247_categories = get_option('247_categories');

    if ( isset($_247_categories[$Product->CategoryId]) ){
    	 wp_set_object_terms( $post_id, $_247_categories[$Product->CategoryId]['term_id'], 'product_cat' );
    }


    return $post_id;
	}



	public static function getCategoryIdByTermID($term_id){
		$_247_categories = get_option('247_categories');

		$category_id = null;
		foreach ($_247_categories as $key => $category) {
			if( $category['term_id'] == $term_id ){
				$category_id = $key;
				break;
			}
		}

		return $category_id;
	}


	public static function updateProduct($post_id, $post){

		if ( $post->post_type == 'product' && !isset($_POST['update']) ){

			$_247_Products = new _247_Products();
			$meta = get_post_custom($post->ID );

			$terms = wp_get_post_terms( $post->ID, 'product_cat' );

			$CategoryId = null;
			if ( $terms && is_array($terms) ){
				$CategoryId = self::getCategoryIdByTermID( $terms[0]->term_id );
			}

			if ( isset($meta['247_id'][0]) ){
				$Id =  $meta['247_id'][0];
			}
			else{
				$Id = $post->ID;
			}

			$args = array(
				'Id' 						=> $Id,
				'Name' 					=> $post->post_title,
				'TaxRate' 			=> null,
				'Stock' 				=> ( isset($meta['_stock'][0]) ) ? FormatUtil::formatDecimal($meta['_stock'][0]) : 0,
				'StatusId'  		=> null,
				'CategoryId' 		=> $CategoryId,
				'PriceGroupID' 	=> null,
				'InPrice' 			=> null,
				'Description' 	=> $post->post_content,
				'Cost' 					=> null,
				'EAN1' 					=> null,
				'Price' 				=> (isset($meta['_price'][0])) ? FormatUtil::formatDecimal($meta['_price'][0]) : 0,
				'No' 						=> null,
				'DateChanged' 	=> FormatUtil::formatDate($post->post_modified),
				// 'APIException' => null,
				// 'Type',
				// 'Message',
				// 'StackTrace',
				'Weight' 				=>  (isset($meta['_weight'][0])) ? FormatUtil::formatDecimal($meta['_weight'][0]) : 0,
				'MinimumStock' 	=> 0,
				'OrderProposal' => null,
				'StockLocation' => null,
				'SupplierProductCode' => null

				);


			$result = $_247_Products->soap_saveProducts($args);

			if ( isset($result->SaveProductsResult->Product->Id) && is_numeric($result->SaveProductsResult->Product->Id) ){
				update_post_meta( $post->ID, '247_id', $result->SaveProductsResult->Product->Id );
			}
		}
	}



	public static function findProductBy247Id($_247_id, $debug = false){
    /*
    $args = array(
      'post_type'       => 'page',
      'posts_per_page'  => '-1',
      'paged'           => 0,
      'meta_value'      => null,
      'meta_key'        => null,
      'meta_query'        => null,
      'orderby'         => 'ASC',
      'order'           => 'menu_order',
      'include'         => null,
      'exclude'         => null,
      'cats'            => null,
      'taxonomy'        => null,
      's'               => null,
    );
    */

		$requested_args = array(
			'post_type'       => 'product',
			'posts_per_page'  => '1',
			'meta_key'      => '247_id',
			'meta_value'        => $_247_id,
		);


    $cats = $taxonomy = $s = null;
    $args = $requested_args;

    if ( !isset( $requested_args['post_type']) )
      $args['post_type'] = "page";

    if ( !isset( $requested_args['posts_per_page']) )
      $args['posts_per_page'] = -1;

     if ( !isset( $requested_args['paged']) )
      $args['paged'] = 0;

    if ( !isset( $requested_args['orderby']) )
      $args['orderby'] = 'title';

    if ( !isset( $requested_args['meta_value']) )
      $args['meta_value'] = null;

    if ( !isset( $requested_args['meta_key']) )
      $args['meta_key'] = null;

    if ( !isset( $requested_args['meta_query']) )
      $args['meta_query'] = null;

    if ( !isset( $requested_args['order']) )
      $args['order'] = 'ASC';

    if ( !isset( $requested_args['include']) )
      $args['include'] = null;

    if ( !isset( $requested_args['exclude']) )
      $args['post__not_in'] = null;
    else
      $args['post__not_in'] = $requested_args['exclude'];

    if ( isset( $requested_args['cats']) )
      $cats = $requested_args['cats'];

    if ( isset($requested_args['taxonomy']) )
      $taxonomy = $requested_args['taxonomy'];


    // filter on search
    if( isset($requested_args['s']) && $requested_args['s']){
      $args['s'] = $requested_args['s'];
    }



    // filter on taxonomy
    if($cats){
      $args['tax_query'] = array(
        array(
        'taxonomy' => $taxonomy,
        'terms' => explode(",",$cats),
        'field' => 'slug'
        )
      );
    }

    //echo '<pre>' . print_r($args, true) . '</pre>';
    $query = new WP_Query($args);
    //echo '<pre>' . print_r($query, true) . '</pre>';

    if ( $debug ){
      echo "<div class='large-12 columns'>";
      var_dump("args");
      print_r('<pre>');
      var_dump($args);
      print_r('</pre>') ;
      var_dump("wp_query");

      print_r('<pre>');
      var_dump($query);
      print_r('</pre>') ;


      echo "</div>";
    }


    return $query->posts;

  }


}