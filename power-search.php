<?php
/*/
Plugin Name: Power Search
Plugin URI: BcaZone.com
Description: A Search Engine which search content from Posts or Pages or Comments.
Version: 1.3.2
Author: Priyank Patel
Author URI: HDClicks.com
/*/


/*  Copyright 2014  Priyank_Patel  (email : priyank780@yahoo.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
require_once('db.php');

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function power_search_add_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'Power Search',         // Widget slug.
                 'Power Search',         // Title.
                 'power_search_dashboard_widget_function' // Display function.
        );

// Globalize the metaboxes array, this holds all the widgets for wp-admin 
 	global $wp_meta_boxes;
 	
 	// Get the regular dashboard widgets array 
 	// (which has our new widget already but at the end)
 	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
 	
 	// Backup and delete our new dashboard widget from the end of the array
 	$example_widget_backup = array( 'power_search_dashboard_widget' => $normal_dashboard['power_search_dashboard_widget'] );
 	unset( $normal_dashboard['power_search_dashboard_widget'] );
 
 	// Merge the two arrays together so our widget is at the beginning
 	$sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
 
 	// Save the sorted array back into the original metaboxes  
 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	
}

add_action( 'wp_dashboard_setup', 'power_search_add_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function power_search_dashboard_widget_function() {

	// Display whatever it is you want to show.
	echo "<form name='frm' action='admin.php'>
			Search <input type='text' name='key' placeholder='Enter Keyword to search'>
				<input type='hidden' name='cat' value='All'>
				<input type='hidden' name='page' value='power-search'>
				<input type='submit' value='Search'></form>";
} 


add_action('admin_menu', 'Power_search');

// Define new menu page parameters
function Power_search() {
	add_menu_page( 'Power Search', 'Power Search', 'activate_plugins', 'power-search', 'fun_pw_search', '');
}

// Define new menu page content

function filter_string($key){
	$k=mysql_real_escape_string($key);
	$k=stripslashes($k);
	$k=strip_tags($k);
	$k=str_replace('%', '', $k);
	return $k;
}


function fun_pw_search() {
?>
	<div class="wrap">
		<h2><font color=red>Power Search</font></h2>
		<form name=frm action=admin.php>
			<input type=hidden name=page value=power-search>
			<table>
				<tr>
					<td>Search</td>
					<td><select name=cat>
							<option <?php echo (isset($_GET['cat']) && $_GET['cat']=='All')?'Selected':'';?> >All</option>
							<option <?php echo (isset($_GET['cat']) && $_GET['cat']=='Post')?'Selected':'';?>>Post</option>
							<option <?php echo (isset($_GET['cat']) && $_GET['cat']=='Page')?'Selected':'';?>>Page</option>
							<option <?php echo (isset($_GET['cat']) && $_GET['cat']=='Comment')?'Selected':'';?>>Comment</option>
						</select>
					</td>
					<td>	<input type=text name=key placeholder='Enter keyword for search' value='<?php echo isset($_GET['key'])?$_GET['key']:'';?>'>
							<input type=submit value=search>
					</td>
				</tr>
			</table>
		</form>
		<hr>
		<?php 
		if(isset($_GET['key']) && isset($_GET['cat'])){
			$key=filter_string($_GET['key']);
			if($key==''){
				echo "<font color=red>Please enter some keyword for search.</font>";
			}else{	
				$cat=filter_string($_GET['cat']);
				echo "Search result for: <b>".$key."</b>";	
				switch($cat){
					case "All":	printPostData($key);
								printPageData($key);
								printCommentData($key);			break;
					case "Post":	printPostData($key);		break;
					case "Page":	printPageData($key);		break;
					case "Comment":	printCommentData($key);		break;
				}
			}
		}
		?>
		<hr>
		<b><a href="https://plus.google.com/+PriyankPatelmcp/about" rel="author">Developed by Priyank Patel</a></b>
	</div>
<?php
}

function printPostData($key){
	$data=db::searchPost($key);
	if(!empty($data)){
?>
		<fieldset>
			<legend><h2>Posts</h2></legend>
			<table class="widefat fixed comments">
				<thead>
				<tr>	<th>Title
						<th>Author
						<th>Date
				</tr>
				</thead>
				<?php
				if (is_array($data)){
					foreach($data as $pData){
						$ptitle=str_replace($key, '<b>'.$key.'</b>', $pData->post_title);
						echo "<tr><td><a href='post.php?post=".$pData->ID."&action=edit'>".$ptitle."</a></td>
						<td>".db::getUserName($pData->post_author)."</td>
						<td>".$pData->post_date."</td></tr>";
					}
				}
				?>
			</table>
		</fieldset>
<?php
	}
}

function printPageData($key){
	$data=db::searchPage($key);
	if(!empty($data)){
?>
	<fieldset>
		<legend><h2>Pages</h2></legend>
			<table class="widefat fixed comments">
			<thead>
				<tr>	<th>Title
						<th>Author
						<th>Date
				</tr>
			</thead>
			<?php
				if (is_array($data)){
					foreach($data as $pData){
						$ptitle=str_replace($key, '<b>'.$key.'</b>', $pData->post_title);
						echo "<tr><td><a href='post.php?post=".$pData->ID."&action=edit'>".$ptitle."</a></td>
									<td>".db::getUserName($pData->post_author)."</td>
									<td>".$pData->post_date."
								</tr>";
					}
				}
			?>
			</table>
	</fieldset>
<?php
	}
}

function printCommentData($key){
	$data=db::searchComment($key);
	if(!empty($data)){
?>
		<fieldset>
			<legend><h2>Comments</h2></legend>
				<table class="widefat fixed comments">
				<thead>
				<tr>	<th class='manage-column column-cb check-column'>Author
						<th class='manage-column column-cb check-column'>Comment
						<th class='manage-column column-cb check-column'>Details
				</tr>
				</thead>
				<?php
				if (is_array($data)){
					foreach ($data as $comment):
						$p_id=$comment->comment_post_ID;
						$post_data=get_post($p_id);	?>
						<tr>	
							<td style="width:70px"><?php 
														echo str_replace($key, '<b>'.$key.'</b>', $comment->comment_author); 
													?>
							<td style="width:500px"><?php 
														echo str_replace($key, '<b>'.$key.'</b>', $comment->comment_content); 
													?>
							<a href="#">Approve</a> | <a href="#">Spam</a> | <a href="#">Trash</a>
							<td>	<table>
										<tr>	
											<td>Post:</td>
											<td style="width:200px">
												<a href="<?php get_permalink($post_data->ID); ?>">
												<?php 
													echo $post_data->post_title;
												?>
												</a></td>
										</tr>
					  					<tr>	<td>Comment Status: </td>
												<td><?php 
													echo $comment->comment_approved; 
												?></td>
										</tr>
					  					<tr>	<td>Date and Time: </td>
												<td><?php 
														echo $comment->comment_date_gmt; 
													?></td>
										</tr>
					  					<tr>	<td>IP: </td>
												<td><?php 
														echo $comment->comment_author_IP; 
													?>
												</td>
										</tr>
									</table>
						</tr>
				<?php 
					endforeach; 
				}
				?>
				</table>
		</fieldset>
<?php
	}
}
?>