<?php

class MensagiaCountry
{
    public static function getMensagiaCountryByISO($code)
    {
        global $wpdb;

        $query = '
            SELECT *
            FROM `'.$wpdb->prefix.'mensagia_countries` 
            WHERE code ="'.$code.'"';

        $results = $wpdb->get_results($query, ARRAY_A);

        if (isset($results[0]['id'])) {
            return $results[0];
        } else {
            return null;
        }
    }
}
