<?php

class db{
	static function searchPost($key){
		global $wpdb;
		$sql_str="select * from $wpdb->posts where post_status='publish' and post_type='post' and (post_title like '%$key%' or post_content like '%$key%')";
		return $wpdb->get_results($sql_str);
	}

	static function searchPage($key){
		global $wpdb;
		$sql_str="select * from $wpdb->posts where post_status='publish' and post_type='page' and (post_title like '%$key%' or post_content like '%$key%')";
		return $wpdb->get_results($sql_str);
	}

	static function searchComment($key){
		global $wpdb;
		$sql_str="select * from $wpdb->comments where (comment_content like '%$key%' or comment_author like '%$key%')";
		return $wpdb->get_results($sql_str);
	}
	static function getUserName($id){
		global $wpdb;
		$sql_str="select display_name from $wpdb->users where ID=$id";
		return $wpdb->get_var($sql_str);	
	}
}
?>