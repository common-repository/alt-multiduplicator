<?php
/*
  Plugin Name: AlT Multiduplicator
  Version: 1.2.0
  Plugin URI: http://alt.lived.fr/plugins/alt-multiduplicator/
  Description: Permet de dupliquer des posts sur un site multisite en respectant le référencement 
  Author: AlTi5, Loris 
  Author URI: http://alt.lived.fr/
 */
if (is_admin()) {
    if (!class_exists('Alt_Multiduplicator')) {

        class Alt_Multiduplicator {

            public static function hooks() {
                if (is_admin()) {
                    //   add_action('admin_menu', array(__CLASS__, 'add_settings_panels'));
                    add_action('save_post', array(__CLASS__, 'save_post'));
                }
                add_action('plugins_loaded', array(__CLASS__, 'plugins_loaded'));
                register_activation_hook(__FILE__, array(__CLASS__, 'plugin_activation'));
              //  add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'add_action_links'));
                add_action('add_meta_boxes', array(__CLASS__, 'initialisation_metaboxes'));
                add_action('save_post', array(__CLASS__, 'save_metaboxes'), 999);
                remove_action('wp_head', 'rel_canonical');
                add_action('wp_head', array(__CLASS__, 'alt_canonical'));
            }

            /**
             * 
             */
            protected static function lib_require() {
                require_once(dirname(__FILE__) . '/bdd/bdd.php');
            }

            /**
             * 
             */
            public static function plugin_activation() {
                self::lib_require();
                AltMD_Bdd::create_bdd_tables();
            }

            /**
             * 
             */
            public static function plugins_loaded() {
                self::lib_require();
            }

            /*
              public static function add_settings_panels() {
              add_submenu_page(
              'options-general.php', __('AlT Multiduplicator'), __('AlT Multiduplicator'), 'administrator', 'alt-multiduplicator', array(__CLASS__, 'tblbords')
              );
              }
             * 
             */

            /**
             * 
             * @param type $option
             * @param type $value
             */
            private static function set_option($option, $value) {
                if (get_option($option) !== FALSE) {
                    update_option($option, $value);
                } else {
                    add_option($option, $value, '', 'no');
                }
            }

            /**
             * 
             * @param type $post_id
             * @return type
             */
            public static function save_post($post_id) {
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                    return $post_id;
                if (isset($_POST['post_type'])) {
                    if ('distributeurs' != $_POST['post_type'] || !current_user_can('edit_post', $post_id)) {
                        return $post_id;
                    }
                }
                if ($parent_post_id = wp_is_post_revision($post_id)) {
                    $post_id = $parent_post_id;
                }
            }

            /**
             * 
             * @param type $links
             * @return type
             */
        /*    public static function add_action_links($links) {
                $mylinks = array(
                    '<a href="' . admin_url('options-general.php?page=alt-multiduplicator') . '">Paramètres</a>',
                );
                return array_merge($links, $mylinks);
            }

            /**
             * 
             */
            public static function initialisation_metaboxes() {
                add_meta_box('id_ma_meta', 'Multi duplicator', array(__CLASS__, 'meta_function'), 'post', 'side', 'low');
                add_meta_box('id_ma_meta', 'Multi duplicator', array(__CLASS__, 'meta_function'), 'page', 'side', 'low');
            }

            public static function meta_function($post) {
                global $wpdb;

// on récupère la valeur actuelle pour la mettre dans le champ
                $blog_id = get_current_blog_id();
                switch_to_blog(1);
                $good_prefix = $wpdb->prefix;
                restore_current_blog();
//liste les blogs
                $list_blog = Alt_Bdd_Model_Multiduplicator::List_blogs($good_prefix);
//récupére la clef lier au blog
                $get_key = Alt_Bdd_Model_Multiduplicator::Get_key($blog_id, $post);
//affiche les articler lier a la clef
                $liaisons_multiduplicator = Alt_Bdd_Model_Multiduplicator::Liaisons_MD($get_key);

                $table_liaison = array();
                $source = '';
                foreach ($liaisons_multiduplicator as $liaison) {
                    $table_liaison[$liaison->altmd_blog_id]['altmd_id'] = $liaison->altmd_id;
                    $table_liaison[$liaison->altmd_blog_id]['altmd_article_id'] = $liaison->altmd_article_id;
                    $table_liaison[$liaison->altmd_blog_id]['altmd_synchro'] = $liaison->altmd_synchro;
                    $table_liaison[$liaison->altmd_blog_id]['altmd_source'] = $liaison->altmd_source;
                    if ($liaison->altmd_source != '' && $liaison->altmd_source != 0) {
                        $source = $liaison->altmd_source;
                    }
                }
                if ($source == '') {
                    $source = $blog_id;
                }
// Parcours des resultats obtenus
                ?>
                <table style="width: 100%">
                    <thead>
                        <tr>       
                            <th style=" font-size: 10px">Blog</th>
                            <th style=" font-size: 10px">Source</th>
                            <th style=" font-size: 10px">ID Article</th>
                            <th style=" font-size: 10px">synchro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?Php
                        $http = ($_SERVER['HTTPS'] == 'on') ? '"https://"' : 'http://';

                        foreach ($list_blog as $blog) {
                            $domaine = explode('.', $blog->domain);
                            $title = $domaine[0] . ' ';
                            $title .= str_replace('/', '', $blog->path);
                            $title = ($title == '') ? 'origin' : $title;
                            $article_value = (isset($table_liaison[$blog->blog_id]['altmd_article_id'])) ? $table_liaison[$blog->blog_id]['altmd_article_id'] : '0';
                            if ($blog->blog_id == $blog_id) {
                                $title = '<b>' . $title . '*</b>';
                                $article_value = $post->ID;
                            }
                            ?>
                            <tr>
                                <td>
                                    <span style=" font-size: 10px">
                                        <?php
                                        echo $title;
                                        ?>
                                    </span>
                                </td>
                                <td>
                        <center>
                            <input type="radio" name="multiduplicator_source" value="<?php echo $blog->blog_id ?>" <?php echo ($table_liaison[$blog->blog_id]['altmd_source'] == 1 || $source == $blog->blog_id) ? 'checked' : '' ?>/>
                        </center>
                    </td>
                    <td>
                    <center>
                        <input type="number" name="multiduplicator_article[<?php echo $blog->blog_id ?>]"  <?php echo ($blog->blog_id == $blog_id) ? 'disabled' : '' ?>  style=" width: 100% " value="<?php echo $article_value ?>"/> 
                    </center>
                    </td>
                    <td>
                    <center>
                        <input type="checkbox" name="multiduplicator_synchro[<?php echo $blog->blog_id ?>]" <?php echo (isset($table_liaison[$blog->blog_id]['altmd_synchro']) && $table_liaison[$blog->blog_id]['altmd_synchro'] == 1) ? 'checked' : '' ?>/> 
                        <input type="hidden" name="altmd_id[<?php echo $blog->blog_id ?>]" value="<?php echo $table_liaison[$blog->blog_id]['altmd_id'] ?>"/> 
                        <?php
                        if ($article_value != 0) {
                            echo'<a href="' . $http . '' . $blog->domain . $blog->path . 'wp-admin/post.php?post=' . $article_value . '&action=edit">+</a>';
                        }
                        ?>
                    </center>
                    </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                </table>
                <?Php
            }

            public static function save_metaboxes($post_ID) {

                if (isset($_POST['multiduplicator_article'])) {
                    $post_infos = get_post($post_ID);
//pres a être syncrho
                    $blog_id = get_current_blog_id();
                    $id_element = $blog_id . $post_ID;

                    $synchro = (isset($_POST['multiduplicator_synchro'][$blog_id])) ? '1' : '0';
                    $source = ($_POST['multiduplicator_source'] == $blog_id) ? '1' : '0';

                    $article = array(
                        'altmd_key' => $id_element,
                        'altmd_blog_id' => $blog_id,
                        'altmd_article_id' => $post_ID,
                        'altmd_synchro' => $synchro,
                        'altmd_source' => $source);

                    if ($_POST['altmd_id'][$blog_id] != '') {
                        $article['altmd_id'] = sanitize_text_field($_POST['altmd_id'][$blog_id]);

                        $MultiduplicatorAdd = new Alt_Bdd_Object_Multiduplicator($article);
                        Alt_Bdd_Model_Multiduplicator::update($MultiduplicatorAdd);
                    } else {
                        $MultiduplicatorAdd = new Alt_Bdd_Object_Multiduplicator($article);
                        Alt_Bdd_Model_Multiduplicator::add($MultiduplicatorAdd);
                    }

                    foreach ($_POST['multiduplicator_article'] as $key => $value) {

                        if (isset($value) && $value != '') {
                            $synchro = (isset($_POST['multiduplicator_synchro'][$key])) ? '1' : '0';
                            $source = ($_POST['multiduplicator_source'] == $key) ? '1' : '0';

                            //si $synchro

                            if ($synchro == '1') {
                                global $wpdb;
                                switch_to_blog($key);

                                $post_infos = (array) $post_infos;
                                unset($post_infos['filter']);
                                unset($post_infos['ID']);
                                if ($value != 0) {
                                    $where['ID'] = $value;
                                    $wpdb->update($wpdb->posts, $post_infos, $where, $format = null, $where_format = null);
                                } else {
                                    $wpdb->insert($wpdb->posts, $post_infos, $format = null);  //insséré ou update le post ici $wpdb->posts
                                    $value = $wpdb->insert_id;
                                }

                                restore_current_blog();
                            }
                            //si value egale a 0
                            if ($synchro == '1' && ($value == '' || $value == '0')) {
                                $value = $save_post->ID;
                            }

                            $article = array(
                                'altmd_key' => $id_element,
                                'altmd_blog_id' => $key,
                                'altmd_article_id' => $value,
                                'altmd_synchro' => $synchro,
                                'altmd_source' => $source);

                            if ($_POST['altmd_id'][$key] != '') {
                                $article['altmd_id'] = sanitize_text_field($_POST['altmd_id'][$key]);
                                $MultiduplicatorAdd = new Alt_Bdd_Object_Multiduplicator($article);
                                Alt_Bdd_Model_Multiduplicator::update($MultiduplicatorAdd);
                                self::duplicate_image($key, $post_ID, $value);
                            } else {

                                $MultiduplicatorAdd = new Alt_Bdd_Object_Multiduplicator($article);
                                Alt_Bdd_Model_Multiduplicator::add($MultiduplicatorAdd);
                                self::duplicate_image($key, $post_ID, $value);
                            }
                            //si synchrom alors je met a jour le titre du port lier et tous son contennu
                        }
                    }
                }
            }

            public static function duplicate_image($blog_id, $post_id, $target_id) {
                $post_thumbnail_id = get_post_thumbnail_id($post_id);

                $image_url = wp_get_attachment_image_src($post_thumbnail_id, 'full');

                $image_url = $image_url[0];


                switch_to_blog($blog_id); // switch to target blog
// Add Featured Image to Post
                $upload_dir = wp_upload_dir(); // Set upload folder
                $image_data = file_get_contents($image_url); // Get image data

                $filename = basename($image_url); // Create image file name
// Check folder permission and define file location
                if (wp_mkdir_p($upload_dir['path'])) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }

// Create the image  file on the server
                file_put_contents($file, $image_data);

// Check image file type
                $wp_filetype = wp_check_filetype($filename, null);


// Set attachment data
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );


// Create the attachment
                $attach_id = wp_insert_attachment($attachment, $file, $target_id);

// Include image.php
                require_once(ABSPATH . 'wp-admin/includes/image.php');

// Define attachment metadata
                $attach_data = wp_generate_attachment_metadata($attach_id, $file);

// Assign metadata to attachment
                wp_update_attachment_metadata($attach_id, $attach_data);

// And finally assign featured image to post
                set_post_thumbnail($target_id, $attach_id);

                restore_current_blog(); // return to original blog
            }

            public static function alt_canonical() {
                global $wpdb, $post;

                $blog_id = get_current_blog_id();
                $get_key = Alt_Bdd_Model_Multiduplicator::Get_key($blog_id, $post);

                //  si source alors j'ajoute rien sinon je recherche
                if ($get_key[0]->altmd_source != 1) {
                    $liaisons = $wpdb->get_results("SELECT * FROM multiduplicator WHERE altmd_key=" . $get_key[0]->altmd_key . " AND altmd_source=1");
                    switch_to_blog($liaisons[0]->altmd_blog_id);
                    echo '<link rel="canonical" href="' . get_permalink($liaisons[0]->altmd_article_id) . '" />';
                    restore_current_blog();
                }
            }

        }

        Alt_Multiduplicator::hooks();
    }
}