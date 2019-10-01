<?php
/*
Plugin Name: Simple SEO Pack
Plugin URI: 
Description: TitleやDescription、KeywordなどのMeta情報などを設定するプラグインです。
Version: 191001
Author: Hikari Kato
Author URI: http://mukaibi.com/
License: GPL2
*/
?>
<?php
/*  Copyright 2017 Hikari Kato (email : info@mukaibi.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
     published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
	// SEO Setting
	// 余分なPタグ削除
	remove_filter('term_description','wpautop');

	// _Title
	function get_title() {
		global $post;
		$title = "";		
		
		if ( is_home() ) {
			// コメントアウトのDescriptionは任意で設定
			$title = get_bloginfo()/* . " | " . get_option('set-description')*/;
		} 
		
		elseif ( is_category() ) {
			$title = get_the_title() . " | " . get_bloginfo();
		} 
		
		elseif ( is_post_type_archive() ) {
			$title = esc_html(get_post_type_object(get_post_type())->label) . " | " . get_bloginfo();
            
            // Custom Post Type - information
            if( is_post_type_archive('information') ) {
				$title = お知らせ . " | " . get_bloginfo();;
			}
            // Custom Post Type - recruit
            if( is_post_type_archive('recruit') ) {
				$title = 採用情報 . " | " . get_bloginfo();;
			}
            // Custom Post Type - faq
            if( is_post_type_archive('faq') ) {
				$title = よくあるお問い合わせ . " | " . get_bloginfo();;
			}
		}
		
		elseif( is_tax() ) {
			$title = single_tag_title($prefix, FALSE) . " | " . get_bloginfo();
		}
		
		elseif( is_404() ) {
			$title = "ページが見つかりませんでした" . " | " . get_bloginfo();
		}
		
		else {
			$ctm = get_post_meta($post->ID, 'my_title', true);
			if (empty($ctm)) {
				$title = get_the_title() . " | " . get_bloginfo();
			} else {
				$title = post_custom('my_title') . " | " . get_bloginfo();
			}
		}
		
		return $title;
	}
	
	// __echo get title tag
	function echo_get_title_tag() {
		echo '<title>' . get_title() . '</title>' . "\n";
	}

	
	// _Description
	function get_meta_description() {
		global $post;
		$description = "";		
		
		if ( is_home() ) {
			$description = get_option('set-description');
		}
		
		elseif ( is_category() ) {
			if ($description = category_description()) {
				$description = category_description();
			} else {
				$description = get_option('set-description');
			}
		}
		
		elseif( is_post_type_archive() ) {
			$description = get_option('set-description');
			
			// Custom Post Type - information
			if( is_post_type_archive('information') ) {
				$description = "当クリニックからの最新のお知らせ、診療時間、休診日などを掲載しております。";
			}
            // Custom Post Type - recruit
//			elseif( is_post_type_archive('recruit') ) {
//				$description = "採用情報では、一緒に働いてくださるスタッフを募集しています。採用・求人情報、募集条件の詳細は直接お問い合わせください。";
//			}
            // Custom Post Type - faq
//			elseif( is_post_type_archive('faq') ) {
//				$description = "みなさまからよく寄せられるご質問をまとめさせていただきました。予防接種や乳幼児健診の予約、当クリニックの院内感染への配慮、診療時間外の診療について掲載しております。";
//			}
		}
		 
		elseif ( is_single() ) {
			$ctm = get_post_meta($post->ID, 'my_description', true);
			if (empty($ctm)) {
				$description = strip_tags($post->post_content);
				$description = str_replace("\n", "", $description);
				$description = str_replace("\r", "", $description);
				$description = mb_substr($description, 0, 120, 'utf-8');
				if(mb_strlen($description, 'utf-8') >= 120) {
					$description .= '…';    
				}
			} else {
				$description = post_custom('my_description');
			}
		}
		
		elseif( is_tax() ) {
			if (term_description()) {
				$description = term_description();
			} else {
				$description = get_bloginfo('description');
			}
		}
		
		elseif (is_page()) {
			$ctm = get_post_meta($post->ID, 'my_description', true);
			if (empty($ctm)) {
				$description = strip_tags($post->post_content);
				$description = str_replace("\n", "", $description);
				$description = str_replace("\r", "", $description);
				$description = mb_substr($description, 0, 120, 'utf-8');
				if(mb_strlen($description, 'utf-8') >= 120) {
					$description .= '…';
				}
			} else {
				$description = post_custom('my_description');
			}
		} 
		
		else {
			;
		}
		
		return $description;
	}
	 
	// __echo meta description tag (ホーム、カテゴリー、投稿、カスタム投稿アーカイブ、カスタム投稿タクソノミーで出力)
	function echo_meta_description_tag() {
		if ( is_home() || is_category() || is_single() || is_post_type_archive() || is_tax() || is_page() ) {
			echo '<meta name="description" content="' . get_meta_description() . '" />' . "\n";
		}
	}
	

	// meta keywords 用のキーワードを取得する関数
	function get_meta_keywords() {
		global $post;
		$keywords = "";
		
		if ( is_home() ) {
//			$keywords = "ホームキーワード1, ホームキーワード2";
			$keywords = get_option('set-kywords');
		}
		
		elseif ( is_category() ) {
			// カテゴリーページではカテゴリー名を設定
			$keywords = get_option('set-kywords') . "," . single_cat_title('', false);
		}
		
		elseif( is_post_type_archive() ) {
			$keywords = get_option('set-kywords') . "," . get_post_type_object(get_post_type())->label;
			// 任意で追加・変更可
			if( is_post_type_archive('information') ) {
				$keywords = "産婦人科, お知らせ, 新着情報, 休診, 診療時間";
			}
//            elseif( is_post_type_archive('recruit') ) {
//				$keywords = "なないろキッズクリニック, 小児科, 採用情報, 求人, スタッフ募集, 看護師, 医療事務";
//			}
//            elseif( is_post_type_archive('faq') ) {
//				$keywords = "なないろキッズクリニック, 小児科, お問い合わせ, 予防接種, 乳幼児健診, 予約, 院内感染, 診療時間外, 診療";
//			}
		}
		
		elseif ( is_single() ) {
			$ctm = get_post_meta($post->ID, 'my_keywords', true);
			if (empty($ctm)) {
				foreach( get_the_category() as $index => $category ) {
					if ($index >= 1) {
						$keywords .= ',';
					}
					$keywords = get_option('set-kywords') . "," . single_cat_title('', false);
				}
			} else {
				$keywords = post_custom('my_keywords');
			}
		}
		
		elseif( is_tax() ) {
			$keywords = get_option('set-kywords') . "," . get_post_type_object(get_post_type())->label . "," . single_tag_title($prefix, FALSE);
		}
		
		elseif (is_page()) {
			$ctm = get_post_meta($post->ID, 'my_keywords', true);
			if (empty($ctm)) {
				$keywords = get_option('set-kywords') . "," . get_the_title();
			} else {
				$keywords = post_custom('my_keywords');
			}
		} 
		
		else {
			;
		}
		
		return $keywords;
	}
	 
	// meta keywords のタグを出力する関数 (ホーム、カテゴリー、投稿、カスタム投稿アーカイブ、カスタム投稿タクソノミーで出力)
	function echo_meta_keywords_tag() {
		if ( is_home() || is_category() || is_single() || is_post_type_archive() || is_tax() || is_page() ) {
			echo '<meta name="keywords" content="' . get_meta_keywords() . '" />' . "\n";
		}
	}
	
	/**  まず、表示したい関数を制作。 */
	function simple_seo_pack(){
	    echo echo_get_title_tag();
	    echo echo_meta_description_tag();
	    echo echo_meta_keywords_tag();
	}
	/** それを反映させるためにフック */
	add_action('wp_head', 'simple_seo_pack', 1);



	// Add Menu
	add_action('admin_menu', 'TopPageSeo');
	function TopPageSeo() {
		add_options_page('Simple SEO', 'Simple SEO', 'manage_options', 'tps', 'TopPageSeo_ops');
		add_action('admin_init', 'TopPageSeo_reg');
	}

	function TopPageSeo_reg() {
		register_setting('seo-setting-group', 'set-kywords');
		register_setting('seo-setting-group', 'set-description');
	}

	function TopPageSeo_ops(){ ?>
	<div class="wrap">
		<h2>Simple SEOの設定</h2>
		<h3>ホーム (トップページ)</h3>
		<form method="post" action="options.php">
			<?php
				settings_fields('seo-setting-group');
				do_settings_sections('seo-setting-group');
			?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="set-kywords">ホームのキーワード</label>
						</th>
						<td>
							<textarea id="set-kywords" rows="5" class="large-text code" name="set-kywords"><?php echo get_option('set-kywords'); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="set-description">ホームのディスクリプション</label>
						</th>
						<td>
							<textarea id="set-description" rows="5" class="large-text code" name="set-description"><?php echo get_option('set-description'); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php } ?>