<?php

/**
 * Gestion des tables propres au plugin et des requètes qui vont avec :)
 */
class AltMD_Bdd {

    public static function run() {
        self::require_models();
        self::require_class();
    }

    protected static function require_models() {
        require_once( dirname(__FILE__) . '/models/MultiduplicatorManager.php' );
    }

    protected static function require_class() {
        require_once( dirname(__FILE__) . '/models/Multiduplicator.php' );
    }

    public static function create_bdd_tables() {
        //Création des différentes tables
        Alt_Bdd_Model_Multiduplicator::create_bdd_table();
    }

    /*     * **********************************************************
     * Définir ici les fonctions de récupération en base utiles
     * depuis l'extérieur ou faisant appel à plusieurs models simultanément.
     */
}

AltMD_Bdd::run();

