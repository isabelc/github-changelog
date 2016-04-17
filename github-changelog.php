<?php
/*
Plugin Name: Github Changelog
Plugin URI: http://isabelcastillo.com/docs/category/github-changelog-wordpress-plugin
Description: Display the release notes for all releases of a GitHub-hosted repo.
Version: 1.1
Author: Isabel Castillo
Author URI: http://isabelcastillo.com
License: GPL2

Copyright 2015-2016 Isabel Castillo

Github Changelog is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

Github Changelog is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Github Changelog; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Replace label tags with proper content
*
* @param string $label Label to search for tags
* @param string $tag Version name of the release
* @param string $repo Repo name
*
* @since 1.0
*
* @return string Label with tags filtered out.
*/
function gc_do_label_tags( $label, $tag, $repo ) {
	$search = array( '{version}', '{name}' );
	$replace = array( $tag, $repo );
	$new_label = str_replace ( $search, $replace, $label );
	return $new_label;
}

/**
 * Query the GitHub API for a repo's releases
 *
 */
function gc_get_github_api_releases( $username, $repo, $accessToken = '' ) {

	// Query the GitHub API
	$url = "https://api.github.com/repos/{$username}/{$repo}/releases";
		 
	// Access token for private repos
	if ( ! empty( $accessToken ) ) {
		$url = esc_url_raw( add_query_arg( array( "access_token" => $accessToken ), $url ) );
	}
		 
	$github_api_result = wp_remote_retrieve_body( wp_remote_get( $url ) );

	if ( ! empty( $github_api_result ) ) {
		$github_api_result = @json_decode( $github_api_result );
	}
	return $github_api_result;
}

/**
 * Returns a changelog for a GitHub repo consisting of all Release notes for all releases.
 */
function gc_get_github_changelog( $username, $repo, $accessToken = '' ) {

	$github_api_result = gc_get_github_api_releases( $username, $repo, $accessToken );

	if ( ! $github_api_result ) {
		return;
	}

	$changelog = '';

	if ( ! class_exists( 'Parsedown' ) ) {
		include_once plugin_dir_path( __FILE__ ) . 'Parsedown.php';
	}

	foreach ( $github_api_result as $release ) {

		$date = substr( $release->published_at, 0, strpos( $release->published_at, 'T') );
		$changelog .= '<h3 class="github-changelog-tag">' . $release->tag_name . '</h3>';
		$changelog .= "(released $date)<br />";
		$changelog .= '<div class="github-changelog-section">' . Parsedown::instance()
   		->setBreaksEnabled(true) # enables automatic line breaks
   		->text( $release->body ) . '</div>';

	}

	return $changelog;
}

/**
 * Returns a download link to the zip of the latest release.
 */
function gc_get_github_latest_release( $username, $repo, $label = '' ) {

	$github_api_result = gc_get_github_api_releases( $username, $repo );

	if ( ! $github_api_result ) {
		return;
	}
	
	$tag = reset( $github_api_result )->tag_name; // get version tag of latest release
	
	if ( $label ) {
		// replace any special tags in labels
		$label = gc_do_label_tags( $label, $tag, $repo );
	}

	$label = $label ? $label : "Download $repo $tag";

	return "<a href='https://github.com/$username/$repo/archive/$tag.zip'>$label</a>";

}

/**
* The changelog shortcode.
*
* @since   1.0
* @param   null $content
*/
function gc_github_changelog_shortcode( $atts, $content = null ) {

    $username	= empty( $atts['username'] ) ? '' : $atts['username'];
    $repo 		= empty( $atts['repo'] ) ? '' : $atts['repo'];
    $token 		= empty( $atts['token'] ) ? '' : $atts['token'];

    if ( $username && $repo ) {

		ob_start();
		echo gc_get_github_changelog( $username, $repo, $token );
		$display = ob_get_clean();
		return $display;

    }

}

add_shortcode( 'github_changelog', 'gc_github_changelog_shortcode' );

/**
* The Latest Release Download shortcode.
*
* @since   1.0
* @param   null $content
*/
function gc_github_latest_release_shortcode( $atts, $content = null ) {

	$atts = shortcode_atts(
		array(
		'username'	=> '',
		'repo'		=> '',
		'label'		=> ''
		), $atts, 'github_latest_release_zip' );

    if ( $atts['username'] && $atts['repo'] ) {

		ob_start();

		echo gc_get_github_latest_release( $atts['username'], $atts['repo'], $atts['label'] );

		$display = ob_get_clean();
		return $display;

    }
}
add_shortcode( 'github_latest_release_zip', 'gc_github_latest_release_shortcode' );
