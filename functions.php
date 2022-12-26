<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme = wp_get_theme();

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $the_theme->get( 'Version' ) );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $the_theme->get( 'Version' ), true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );

//# JWR custom theme fns ToC

/*
1. JWR post meta
2. JWR ToC function - incomplete
*/

//# 1. JWR post meta
function jwr_post_meta() {

	// gather data
	if( is_admin() ) { return; }
	global $post;
	$post_type = get_post_type($post);

	if( $post_type == "post" ){
		$link = '/';
	}else {
		$link = '/';
	}

	$post_author = get_the_author();
	$post_date = get_the_date('F j, Y');
	$mod_date = get_the_modified_date('F j, Y');
	$category = get_the_term_list( get_the_ID( ), 'category', "Filed under: ",', ');

	// build output
	$this_output = "<div class='jwr-post-meta'>";
	$this_output .= "<div>This <a href='$link'>$post_type</a> was published by&nbsp;$post_author on&nbsp;$post_date.";

	if( $post_date != $mod_date ) {
		$this_output .= "<br>updated:&nbsp;$mod_date";
	}
	$this_output .= "</div>";

	if( isset($category) ){ // add conditionals for each subitem/cpt - add HR for any of them
		$this_output .= "<hr>";
	}

	if( isset($category) ){
		$this_output .= "<div>$category</div>";
	}

	$this_output .= "</div>";

	// display result
	echo $this_output;
}

//# 2. JWR ToC function
function jwr_page_toc() { // in dev
	/*
	
	*/
	//check for ACF repeater field
	$toc_entries = 1; // !?!
	if( isset($toc_entries) ){ ?>
		<div class = 'toc-container border mt-4'>
			<h4 class='toc-header text-center my-1'>Table of contents</h4>
			<div class='toc-body my-1'>
				<ol id='toc-list'></ol>
				<script>
					jQuery( document ).ready( readyFn );
					function readyFn( $ ) {
						/**
						 * 1. Finds all H2s in .entry-content
						 * 2. Gives them an ID based on their name
						 * 3. Updates the ToC with a linked list item
						 * 
						 * [] Expand to H3s
						 * [] Move to seperate plugin
						 */
						const headers = document.querySelectorAll('.entry-content h2');
						if( headers.length === 0 ){
							console.log('ToC: no headers');
							hideToC();
						}
						headers.forEach(processHeader);
					}
					function processHeader(header){
						var header_name = header.innerText;
						header_name = header_name.toLowerCase();
						header_name = header_name.replace(/[^a-z]/g,'_');

						addID(header, header_name);
						createLink(header, header_name);
					}
					function addID(header, header_name){
						header.setAttribute('id', header_name);
					}
					function createLink(header, header_name){
						var link = "<li><a href='#"+header_name+"'>"+header.innerText+"</a></li>";
						jQuery('#toc-list').append(link);
					}
					function hideToC(){
						jQuery('.toc-container').hide();
					}
				</script>
				<noscript>
					<div>This function requires JavaScript.</div>
				</noscript>
				
			</div>
		</div>
	<?php
	}
}

//# 3. JWR Related content
function jwr_related_content() { // in dev
	$count = 4;
	$query_max = $count; // ?? what is this for? why 2 variables?

	/*
	!! WARNING 
	The inital query (for tags) ignores post status. 
	You may need to query more than you need to ensure you get enough published posts.
	*/

	// get data
	global $wpdb;
	$post_id = get_the_ID();
	$post_tag_objects = get_the_terms( $post_id, 'post_tag' ); // term objs of this post's tags
	if(is_wp_error( $post_tag_objects ) || $post_tag_objects == false ){ // bail if no tags 
		return; // abort if no tags
	}

	// term_string is basically a comma separated version of $post_tags for use in a custom query
	// final format ("3", "345", "123")
	$term_count = 0;
	$term_string = "("; 
	foreach( $post_tag_objects as $post_tag_object ){
		if( $term_count != 0 ){
			$term_string .= ", "; // prepend id with comma unless 1st element
		}else{
			$term_count = 1;
		}
		$term_string .= '"' . $post_tag_object->term_id . '"'; // weird quotes to match SQL query format
	}
	$term_string .= ")";



	//query for matches to term_id
	
	$query = "SELECT * FROM `$wpdb->term_relationships` WHERE `term_taxonomy_id` IN $term_string";
	// SECURITY NOTE: this query only uses values querried for and generated within this fn

	$results = $wpdb->get_results( $query );
	if( !isset($results) ){ return; }
	$tally = array(); 
	wp_reset_postdata();
	
	// counting the results. give query results. get array with id as key and count as value
	foreach( $results as $result ){
		$this_post_id = $result->object_id;

		if( $this_post_id == $post_id ){
			continue; //skip if is current post
		}
		$this_post_id = (string) $this_post_id; //?? no matter what I do I get undefined offset grrr
		if( isset($tally[$this_post_id]) ) {
			$tally[$this_post_id] = $tally[$this_post_id] + 1; // increment on matches
		} else {
			$tally[$this_post_id] = 1;
		}
	}
	
	if( !isset($tally) || count($tally) < 1 ){
		return;
	}

	//order array
	arsort($tally);
	$related_items = array_keys($tally); // $related_items in now the ordered master list of related IDs

	// get related items
	

	$related_query_results = new WP_Query( array(  //switch to get posts or something
		'post__in' 			=> $related_items, 
		'post_status'		=> array('publish'), // should be array?
		'post_type'			=> 'any', // this won't be an issue because query is limited by post_ids
		'posts_per_page'	=>	$query_max,
		) 
	);	
	$related_loop = $related_query_results->posts; //$related_loop is the post data from the query
	
	//output

	ob_start();

	if( !isset($related_loop) ){ 
		// return;
		echo "Nothing related found";
		wp_reset_postdata();
		$thisOutput = ob_get_clean();
		echo $thisOutput;
	}else {
		echo "<div class='related-items'>";
		echo "<h2>Related articles</h2>";
	}
	// return buffit($related_loop);
	// start list output
	
	echo "<ul>";
	$this_count = 0;
	$this_key = -3;
	$last_key = -1;
	// return "$this_key, $last_key"; //** good */

	// create simpler list of values
	$column_ids = array_column( $related_loop, 'ID' ); // create array of values
	//** good to here */
	// return buffit($related_loop);
	foreach( $related_items as $related_item ){	// related_items is an array: ordered list of post IDs

		if( $this_count >= $count ){ //$count defined at start of fn // should have called it max
			break;
		}

		$this_key = array_search( $related_item, $column_ids ); // array_search returns the key of the match

		// return "$this_key, $last_key";
		
		if( $this_key == $last_key ){
			break; // end loop if using same data as previous loop iteration
		}

		$this_post = $related_loop[$this_key]; // get a specific post from query results

		// return buffit($related_loop);
		
		
		
	 	$this_post_type = ucfirst($this_post->post_type);
		$this_title = $this_post->post_title;
		$this_link = get_the_permalink($this_post->ID);
		echo "<li><a href='$this_link'><span>$this_post_type: </span>$this_title</a></li>";

		$last_key = $this_key;
		$this_count++;
	}
	//!! ISSUE: 
	// Items in trash still have data in term_relationships. They will affect the score. And they will trigger additional loops. 
	//!! Current solution: 
	// keep the trash empty
	echo "</ul>";

	//return output
	wp_reset_postdata();
	$thisOutput = ob_get_clean();
	return $thisOutput;
}


function jwr_post_topics() {
	$post_id = get_the_ID();
	$my_tags = get_the_term_list( $post_id, 'post_tag', "Topics: ",', ' );
	if( isset($my_tags) ){
		echo "<div class='topics-container my-4'>$my_tags</div>";
	} 
}






//# 100. Assorted tools

function dumpit($it){
	echo "<pre>";
	var_dump($it);
	echo "</pre>";
}
function buffit($it){
	ob_start();
	dumpit($it);
	return ob_get_clean();

}