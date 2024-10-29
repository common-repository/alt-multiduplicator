<?php

//permet de gérer l'objet, vérifier s'il est bien saisi et faire des controles pour pas inssérer nimporte quoi en base
class Alt_Bdd_Model_Multiduplicator {

    const table_name = 'multiduplicator';

    public static function create_bdd_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        //TODO : mettre les vrais champs de la table
        $sql = "CREATE TABLE IF NOT EXISTS " . self::table_name . " (
 `altmd_id` int NULL AUTO_INCREMENT PRIMARY KEY,
  `altmd_key` int NULL,
  `altmd_blog_id` int NULL,
  `altmd_article_id` int NULL,
  `altmd_synchro` int NULL,
  `altmd_source` int NULL);";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

//add
    public static function add(Multiduplicator $value) {
        global $wpdb;

        $data['altmd_id'] = $value->altmd_id();
        $data['altmd_key'] = $value->altmd_key();
        $data['altmd_blog_id'] = $value->altmd_blog_id();
        $data['altmd_article_id'] = $value->altmd_article_id();
        $data['altmd_synchro'] = $value->altmd_synchro();
        $data['altmd_source'] = $value->altmd_source();


        $wpdb->insert(self::table_name, $data, $format = null);
        return $wpdb->insert_id;
    }

    //edition
    public static function update(Multiduplicator $value) {
        global $wpdb;

        $data['altmd_key'] = $value->altmd_key();
        $data['altmd_blog_id'] = $value->altmd_blog_id();
        $data['altmd_article_id'] = $value->altmd_article_id();
        $data['altmd_synchro'] = $value->altmd_synchro();
        $data['altmd_source'] = $value->altmd_source();

        $where['altmd_id'] = $value->altmd_id();

        $wpdb->update(self::table_name, $data, $where, $format = null, $where_format = null);
    }

    //suppression
    public static function delete($id) {
        global $wpdb;
        $where['altmd_id'] = $id;
        $wpdb->delete(self::table_name, $where, $where_format = null);
    }

    public static function getByPro($id) {
        global $wpdb;
        $packs = array();
        $packs = $wpdb->get_results("SELECT * FROM  " . self::table_name . " WHERE  `altmd_id` =$id LIMIT 1");
        if (isset($packs[0])) {
            return $packs[0];
        }
    }

    public static function List_blogs($good_prefix) {
        global $wpdb;


        $packs = array();
        $packs = $wpdb->get_results("SELECT *
FROM `{$good_prefix}blogs`
WHERE public=1
AND archived=0
AND mature =0
AND spam=0
AND deleted=0
order by blog_id");

        return $packs;
    }

    public static function Get_key($blog_id, $post) {
        global $wpdb;
        $packs = array();
        $packs = $wpdb->get_results("SELECT *
FROM multiduplicator
WHERE altmd_blog_id=" . $blog_id . "
AND altmd_article_id=" . $post->ID . " LIMIT 1");

        return $packs;
    }

    public static function Liaisons_MD($get_key) {
        global $wpdb;
        $packs = array();
        $packs = $wpdb->get_results("SELECT *
FROM multiduplicator
WHERE altmd_key=" . $get_key[0]->altmd_key . "");

        return $packs;
    }

}
