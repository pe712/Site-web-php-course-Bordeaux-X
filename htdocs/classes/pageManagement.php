<?php

class PageListing
{
    // name est la valeur à passer au paramètre page en méthode GET 
    const page_list = array(
        array(
            "name" => "Accueil",
            "title" => "Course Bordeaux-Polytechnique",
        ),
        array(
            "name" => "About",
            "title" => "A propos",
        ),
        array(
            "name" => "Inscription",
            "title" => "Inscription",
        ),
        array(
            "name" => "Contact",
            "title" => "Nous contacter",
        ),
        array(
            "name" => "Admin",
            "title" => "Modifier les page",
            "admin" => true,
        ),
        array(
            "name" => "Troncons",
            "title" => "Tronçons sur le parcours",
        ),
        array(
            "name" => "Connect",
            "title" => "Page de connexion",
        ),
        array(
            "name" => "Unconnect",
            "title" => "Page de déconnexion",
        ),
        array(
            "name" => "EspacePerso",
            "title" => "Mon Espace Personnel",
            "connected" => true,
        ),
    );

    public static function findPage($name)
    {
        foreach (PageListing::page_list as $page) {
            if ($name == $page["name"] && PageListing::access_authorized($page))
                return $page;
        }
        return PageListing::findPage("Accueil");
    }

    public static function getCurrent()
    {
        if (!array_key_exists("page", $_GET))
            $name = "Accueil";
        else
            $name = $_GET["page"];
        return PageListing::findPage($name);
    }

    public static function access_authorized($page)
    {
        // root or connected access needed
        if ((array_key_exists("admin", $page) && $page["admin"] && !Users::isRoot()) || ((array_key_exists("connected", $page) && $page["connected"] && !Users::isConnected()))) {
            return false;
        } else return true;
    }

    public static function load($name)
    {
        require("pages/$name/$name.php");
        
        $sections = Content::getPage($name);
        $page = new $name($sections);
        if ($page->erreur)
            echo $msg_erreur;
        elseif ($page->load != null){
            $name = $page->load;
            $page_info = PageListing::findPage($name);
            extract($page_info);
            PageListing::load($name);
        }
        else {
            require("pages/includes/navbar.php");
            echo $page->content;
        }
    }
}

class Page
{
    public $content;
    public $erreur = false;
    public $msg_erreur;
    public $load;

    public function __construct($sections) {
        $this->content = $this->buffer($sections);
    }

    public function buffer($sections)
    {
        global $conn;
        $path = "pages/".get_class($this)."/".get_class($this)."Content.php";
        ob_start();
        require($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
