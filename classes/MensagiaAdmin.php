<?php

class MensagiaAdmin
{
    public static function countAdmins()
    {
        global $wpdb;

        $results = $wpdb->get_results('SELECT count(*) as count
            FROM '.$wpdb->prefix.'mensagia_admins', OBJECT);

        if (isset($results[0]->count)) {
            return $results[0]->count;
        } else {
            return 0;
        }
    }

    public static function getAdmins()
    {
        global $wpdb;

        $results = $wpdb->get_results('SELECT *
            FROM '.$wpdb->prefix.'mensagia_admins'." ORDER BY `id` ASC", OBJECT);

        return $results;
    }

    public static function create($name, $number)
    {
        global $wpdb;

        $query = "INSERT INTO " . $wpdb->prefix . "mensagia_admins
         (`name`, `number`) VALUES ('".$name."', '".$number."');";

        $result = $wpdb->query($query);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public static function exists($number)
    {
        global $wpdb;

        $results = $wpdb->get_results("SELECT *
            FROM ".$wpdb->prefix."mensagia_admins"." WHERE number='".$number."';", OBJECT);

        if (isset($results[0])) {
            return true;
        } else {
            return false;
        }
    }

    public static function remove($id)
    {
        global $wpdb;

        $query = "DELETE FROM " . $wpdb->prefix . "mensagia_admins WHERE id=".$id;

        $result = $wpdb->query($query);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
