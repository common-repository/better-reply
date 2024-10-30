<?php
/*
 * Plugin Name: Better @ Reply
 * Plugin URI: http://wordpress.org/extend/plugins/better-at-reply/
 * Description: This plugin allows you to add Twitter-like @reply links to comments. Shows old comment after hover on link.
 * Version: 1.0
 * Author: Valentin
 * Author URI: http://picomol.de/
 * URI: http://picomol.de/better-at-reply/
 * License: GPLv3
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
 
/*
 * This code is based on @ reply from Yus: http://wordpress.org/extend/plugins/reply-to/
 */

wp_enqueue_script('jquery');
if (!is_admin()) {
     add_action('comment_form', 'bar_reply_js');
     if (get_option('thread_comments')) {
          add_filter('comment_reply_link', 'bar_reply_threaded');
     } else {
          add_filter('comment_text', 'bar_reply_arr');
     }
}

function bar_reply_js() { ?>
	<style>
	.yarr { visibility:hidden; position:relative }
	.yarr span { cursor:pointer; position:absolute; bottom:0; right:0 }
	.yarr img { vertical-align:-2px }
	.comment:hover .yarr { visibility:visible }
	.balloonquote { position: absolute; background: #f0f0f0; text-shadow: 1px 1px 0 #fff; border: 1px solid #ccc; max-width: 570px; padding: 10px 10px 0; z-index: 2; }
	.balloonquote .comment-reply-link, .balloonquote a.comment-edit-link { display: none; }
	.balloonquote p { margin-bottom: 10px; padding-bottom: 0px;}
	</style>

	<script type="text/javascript">
		function bar_replyTo(e,t){var n='@<a href="'+e+'">'+t+"</a>: ";var r;if(document.getElementById("comment")&&document.getElementById("comment").type=="textarea"){r=document.getElementById("comment")}else{return false}if(document.selection){r.focus();sel=document.selection.createRange();sel.text=n;r.focus()}else if(r.selectionStart||r.selectionStart=="0"){var i=r.selectionStart;var s=r.selectionEnd;var o=s;r.value=r.value.substring(0,i)+n+r.value.substring(s,r.value.length);o+=n.length;r.focus();r.selectionStart=o;r.selectionEnd=o}else{r.value+=n;r.focus()}}

		var $bar = jQuery.noConflict();
		$bar(function(){	
			$bar('a').mouseenter(function(){	
				var commentId = $bar(this).attr("href");
				var commentNonId = commentId.replace(/\d+/g, '');		
				if (commentNonId == '#comment-') {
					var commentContent = $bar(commentId + ' .comment-content').html();
					$bar('<div class="balloonquote">' + commentContent + '</div>').insertAfter(this);	
				}
				$bar(this).mouseleave(function(){
					$bar('.balloonquote').remove();
				});
			});
		});
	</script>
<?php }


function bar_reply_threaded($reply_link) {
     $comment_ID = '#comment-' . get_comment_ID();
     $comment_author = esc_html(get_comment_author());
     $bar_reply_link = 'onclick=\'return bar_replyTo("' . $comment_ID . '", "' . $comment_author . '"),';
     return str_replace("onclick='return", "$bar_reply_link", $reply_link);
}


function bar_reply_arr($comment_text) {
	if (!is_feed()) {
		if (comments_open() && have_comments() && get_comment_type() == 'comment') {
			if(get_option('page_comments')) {
				$comment_ID = esc_url(get_comment_link());
			} else {
				$comment_ID = '#comment-' . get_comment_ID();
			}
			$comment_author = esc_html(get_comment_author());
			$bar_r = '<div><a class="comment-reply-link" href="javascript:;" onclick=\'bar_replyTo("' . $comment_ID . '", "' . $comment_author . '")\' title="' . __('Reply to this comment') . '">' . __('Reply') . '</a></div>';
			return $comment_text . $bar_r;
		} else { return $comment_text; }
	} else { return $comment_text; }
}

?>
