<?php

//permet de gérer l'objet, vérifier s'il est bien saisi et faire des controles pour pas inssérer nimporte quoi en base
class Alt_Bdd_Object_Multiduplicator {

    public $altmd_id,
            $altmd_key,
            $altmd_blog_id,
            $altmd_article_id,
            $altmd_synchro,
            $altmd_source;

    public function __construct(array $donnees) {
        $this->hydrate($donnees);
        $this->type = strtolower(get_class($this));
    }

    public function hydrate(array $donnees) {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    //Getter 

    public function altmd_id() {
        return $this->altmd_id;
    }

    public function altmd_key() {
        return $this->altmd_key;
    }

    public function altmd_blog_id() {
        return $this->altmd_blog_id;
    }

    public function altmd_article_id() {
        return $this->altmd_article_id;
    }

    public function altmd_synchro() {
        return $this->altmd_synchro;
    }

    public function altmd_source() {
        return $this->altmd_source;
    }

    //Setter

    public function setAltmd_id($valeur) {
        if (is_numeric($valeur)) {
            return $this->altmd_id = trim($valeur);
        } else {
            trigger_error('Altmd_pro_id est un ID de type int');
        }
    }

    public function setAltmd_key($valeur) {
        if (is_numeric($valeur)) {
            return $this->altmd_key = trim($valeur);
        } else {
            trigger_error('setAltmd_key est un ID de type int');
        }
    }

    public function setAltmd_blog_id($valeur) {
        if (is_numeric($valeur)) {
            return $this->altmd_blog_id = trim($valeur);
        } else {
            trigger_error('Altmd_lenght doit être numeric');
        }
    }

    public function setAltmd_article_id($valeur) {
        if (is_numeric($valeur)) {
            return $this->altmd_article_id = trim($valeur);
        } else {
            trigger_error('Altmd_max_lenght  doit être numeric');
        }
    }

    public function setAltmd_synchro($valeur) {
        return $this->altmd_synchro = trim($valeur);
    }

    public function setAltmd_source($valeur) {
        return $this->altmd_source = trim($valeur);
    }

}
