<?php

function mensagiasms_jal_install()
{
    global $wpdb;
    global $mensagiasms_jal_db_version;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // install mensagia_countries
    $table_name = $wpdb->prefix . 'mensagia_countries';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            `id` INT(9) NOT NULL AUTO_INCREMENT,
            `code` varchar(2) NOT NULL DEFAULT '',
            `name` varchar(50) NOT NULL DEFAULT '',
            `native` varchar(50) NOT NULL DEFAULT '',
            `phone` varchar(15) NOT NULL DEFAULT '',
            `continent` varchar(2) NOT NULL DEFAULT '',
            `capital` varchar(50) NOT NULL DEFAULT '',
            `currency` varchar(30) NOT NULL DEFAULT '',
            `languages` varchar(30) NOT NULL DEFAULT '',
            `min_length_number` int4 DEFAULT 6,
            `max_length_number` int4 DEFAULT 15,
            `allowed_firsts_number` varchar(255) DEFAULT '',
            `name_en` varchar(50) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
	) $charset_collate;";
    dbDelta($sql);


    //install mensagia sms_notifications
    $table_name = $wpdb->prefix . 'mensagia_sms_notifications';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            `id` INT(9) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(100) CHARACTER SET utf8 NOT NULL,
            `hook` VARCHAR(100) CHARACTER SET utf8 NOT NULL,
            `option_name` VARCHAR(100) CHARACTER SET utf8 NOT NULL,
            `active` TINYINT(1)  NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($sql);

    //install mensagia sms_notifications
    $table_name = $wpdb->prefix . 'mensagia_sms_notifications_lang';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            `id` INT(9) NOT NULL AUTO_INCREMENT,
            `mensagia_sms_notification_id` INT(9),
            `lang_code` VARCHAR(100) CHARACTER SET utf8 NOT NULL,
            `message` TEXT CHARACTER SET utf8 ,
            PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($sql);


    //install mensagia admins
    $table_name = $wpdb->prefix . 'mensagia_admins';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            `id` INT(9) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
            `number` VARCHAR(20) CHARACTER SET utf8 NOT NULL,      
            PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($sql);

    
    add_option('mensagiasms_jal_db_version', $mensagiasms_jal_db_version);
    add_option('MENSAGIA_PREFIX_MODE', 'check_prefixs');
}

function mensagiasms_jal_install_data()
{
    require_once(__DIR__.'/MensagiaSMSNotification.php');

    global $wpdb;
    $table_name = $wpdb->prefix . 'mensagia_countries';

    $count = $wpdb->query('SELECT * from '.$table_name);

    if (!$count) {
        $query = "INSERT INTO " .$table_name." VALUES
         ('1', 'ES', 'España', 'España', '34', 'EU', 'Madrid', 'EUR', 'es,eu,ca,gl,oc', '11', '11', '6,7', ''),
         ('2', 'AD', 'Andorra', 'Andorra', '376', 'EU', 'Andorra la Vella', 'EUR', 'ca', '6', '15', '', ''),
         ('3', 'AE', 'United Arab Emirates',
          'دولة الإمارات العربية المتحدة'
         , '971', 'AS', 'Abu Dhabi', 'AED','ar', '6', '15', '', ''),
         ('4', 'AF', 'Afghanistan', 'افغانستان', '93', 'AS', 'Kabul', 'AFN', 'ps,uz,tk', '6', '15', '', ''),
         ('5', 'AG', 'Antigua and Barbuda', 'Antigua and Barbuda', '1268', 'NA', 'Saint John''s', 'XCD', 'en',
          '6', '15', '', ''),
         ('6', 'AI', 'Anguilla', 'Anguilla', '1264', 'NA', 'The Valley', 'XCD', 'en', '6', '15', '', ''),
         ('7', 'AL', 'Albania', 'Shqipëria', '355', 'EU', 'Tirana', 'ALL', 'sq', '6', '15', '', ''),
         ('8', 'AM', 'Armenia', 'Հայաստան', '374', 'AS', 'Yerevan', 'AMD', 'hy,ru', '6', '15', '', ''),
         ('9', 'AO', 'Angola', 'Angola', '244', 'AF', 'Luanda', 'AOA', 'pt', '6', '15', '', ''),
         ('10', 'AQ', 'Antarctica', '', '', 'AN', '', '', '', '6', '15', '', ''),
         ('11', 'AR', 'Argentina', 'Argentina', '54', 'SA', 'Buenos Aires', 'ARS', 'es,gn', '6', '15', '', ''),
         ('12', 'AS', 'American Samoa', 'American Samoa', '1684', 'OC', 'Pago Pago', 'USD', 'en,sm', '6', '15',
          '', ''),
         ('13', 'AT', 'Austria', 'Österreich', '43', 'EU', 'Vienna', 'EUR', 'de', '6', '15', '', ''),
         ('14', 'AU', 'Australia', 'Australia', '61', 'OC', 'Canberra', 'AUD', 'en', '6', '15', '', ''),
         ('15', 'AW', 'Aruba', 'Aruba', '297', 'NA', 'Oranjestad', 'AWG', 'nl,pa', '6', '15', '', ''),
         ('16', 'AX', 'Åland', 'Åland', '358', 'EU', 'Mariehamn', 'EUR', 'sv', '6', '15', '', ''),
         ('17', 'AZ', 'Azerbaijan', 'Azərbaycan', '994', 'AS', 'Baku', 'AZN', 'az,hy', '6', '15', '', ''),
         ('18', 'BA', 'Bosnia and Herzegovina', 'Bosna i Hercegovina', '387', 'EU', 'Sarajevo', 'BAM',
          'bs,hr,sr', '6', '15', '', ''),
         ('19', 'BB', 'Barbados', 'Barbados', '1246', 'NA', 'Bridgetown', 'BBD', 'en', '6', '15', '', ''),
         ('20', 'BD', 'Bangladesh', 'Bangladesh', '880', 'AS', 'Dhaka', 'BDT', 'bn', '6', '15', '', ''),
         ('21', 'BE', 'Belgium', 'België', '32', 'EU', 'Brussels', 'EUR', 'nl,fr,de', '6', '15', '', ''),
         ('22', 'BF', 'Burkina Faso', 'Burkina Faso', '226', 'AF', 'Ouagadougou', 'XOF', 'fr,ff', '6', '15',
          '', ''),
         ('23', 'BG', 'Bulgaria', 'България', '359', 'EU', 'Sofia', 'BGN', 'bg', '6', '15', '', ''),
         ('24', 'BH', 'Bahrain', '‏البحرين', '973', 'AS', 'Manama', 'BHD', 'ar', '6', '15', '', ''),
         ('25', 'BI', 'Burundi', 'Burundi', '257', 'AF', 'Bujumbura', 'BIF', 'fr,rn', '6', '15', '', ''),
         ('26', 'BJ', 'Benin', 'Bénin', '229', 'AF', 'Porto-Novo', 'XOF', 'fr', '6', '15', '', ''),
         ('27', 'BL', 'Saint Barthélemy', 'Saint-Barthélemy', '590', 'NA', 'Gustavia', 'EUR', 'fr', '6',
          '15', '', ''),
         ('28', 'BM', 'Bermuda', 'Bermuda', '1441', 'NA', 'Hamilton', 'BMD', 'en', '6', '15', '', ''),
         ('29', 'BN', 'Brunei', 'Negara Brunei Darussalam', '673', 'AS', 'Bandar Seri Begawan', 'BND', 'ms',
          '6', '15', '', ''),
         ('30', 'BO', 'Bolivia', 'Bolivia', '591', 'SA', 'Sucre', 'BOB,BOV', 'es,ay,qu', '6', '15', '', ''),
         ('31', 'BQ', 'Bonaire', 'Bonaire', '5997', 'NA', 'Kralendijk', 'USD', 'nl', '6', '15', '', ''),
         ('32', 'BR', 'Brazil', 'Brasil', '55', 'SA', 'Brasília', 'BRL', 'pt', '6', '15', '', ''),
         ('33', 'BS', 'Bahamas', 'Bahamas', '1242', 'NA', 'Nassau', 'BSD', 'en', '6', '15', '', ''),
         ('34', 'BT', 'Bhutan', 'ʼbrug-yul', '975', 'AS', 'Thimphu', 'BTN,INR', 'dz', '6', '15', '', ''),
         ('35', 'BV', 'Bouvet Island', 'Bouvetøya', '', 'AN', '', 'NOK', '', '6', '15', '', ''),
         ('36', 'BW', 'Botswana', 'Botswana', '267', 'AF', 'Gaborone', 'BWP', 'en,tn', '6', '15', '', ''),
         ('37', 'BY', 'Belarus', 'Белару́сь', '375', 'EU', 'Minsk', 'BYR', 'be,ru', '6', '15', '', ''),
         ('38', 'BZ', 'Belize', 'Belize', '501', 'NA', 'Belmopan', 'BZD', 'en,es', '6', '15', '', ''),
         ('39', 'CA', 'Canada', 'Canada', '1', 'NA', 'Ottawa', 'CAD', 'en,fr', '6', '15', '', ''),
         ('40', 'CC', 'Cocos [Keeling] Islands', 'Cocos (Keeling) Islands', '61', 'AS', 'West Island',
          'AUD', 'en', '6', '15', '', ''),
         ('41', 'CD', 'Democratic Republic of the Congo', 'République démocratique du Congo', '243', 'AF',
          'Kinshasa', 'CDF', 'fr,ln,kg,sw,lu', '6', '15', '', ''),
         ('42', 'CF', 'Central African Republic', 'Ködörösêse tî Bêafrîka', '236', 'AF', 'Bangui', 'XAF',
          'fr,sg', '6', '15', '', ''),
         ('43', 'CG', 'Republic of the Congo', 'République du Congo', '242', 'AF', 'Brazzaville', 'XAF',
          'fr,ln', '6', '15', '', ''),
         ('44', 'CH', 'Switzerland', 'Schweiz', '41', 'EU', 'Bern', 'CHE,CHF,CHW', 'de,fr,it', '6', '15',
          '', ''),
         ('45', 'CI', 'Ivory Coast', 'Côte d''Ivoire', '225', 'AF', 'Yamoussoukro', 'XOF', 'fr', '6',
          '15', '', ''),
         ('46', 'CK', 'Cook Islands', 'Cook Islands', '682', 'OC', 'Avarua', 'NZD', 'en', '6', '15',
          '', ''),
         ('47', 'CL', 'Chile', 'Chile', '56', 'SA', 'Santiago', 'CLF,CLP', 'es', '6', '15', '', ''),
         ('48', 'CM', 'Cameroon', 'Cameroon', '237', 'AF', 'Yaoundé', 'XAF', 'en,fr', '6', '15', '',
          ''),
         ('49', 'CN', 'China', '中国', '86', 'AS', 'Beijing', 'CNY', 'zh', '6', '15', '', ''),
         ('50', 'CO', 'Colombia', 'Colombia', '57', 'SA', 'Bogotá', 'COP', 'es', '6', '15', '', ''),
         ('51', 'CR', 'Costa Rica', 'Costa Rica', '506', 'NA', 'San José', 'CRC', 'es', '6', '15', '',
          ''),
         ('52', 'CU', 'Cuba', 'Cuba', '53', 'NA', 'Havana', 'CUC,CUP', 'es', '6', '15', '', ''),
         ('53', 'CV', 'Cape Verde', 'Cabo Verde', '238', 'AF', 'Praia', 'CVE', 'pt', '6', '15', '', ''),
         ('54', 'CW', 'Curacao', 'Curaçao', '5999', 'NA', 'Willemstad', 'ANG', 'nl,pa,en', '6', '15',
          '', ''),
         ('55', 'CX', 'Christmas Island', 'Christmas Island', '61', 'AS', 'Flying Fish Cove', 'AUD',
          'en', '6', '15', '', ''),
         ('56', 'CY', 'Cyprus', 'Κύπρος', '357', 'EU', 'Nicosia', 'EUR', 'el,tr,hy', '6', '15', '', ''),
         ('57', 'CZ', 'Czechia', 'Česká republika', '420', 'EU', 'Prague', 'CZK', 'cs,sk', '6', '15',
          '', ''),
         ('58', 'DE', 'Germany', 'Deutschland', '49', 'EU', 'Berlin', 'EUR', 'de', '6', '15', '', ''),
         ('59', 'DJ', 'Djibouti', 'Djibouti', '253', 'AF', 'Djibouti', 'DJF', 'fr,ar', '6', '15', '', ''),
         ('60', 'DK', 'Denmark', 'Danmark', '45', 'EU', 'Copenhagen', 'DKK', 'da', '6', '15', '', ''),
         ('61', 'DM', 'Dominica', 'Dominica', '1767', 'NA', 'Roseau', 'XCD', 'en', '6', '15', '', ''),
         ('62', 'DO', 'Dominican Republic', 'República Dominicana', '1809,1829,1849', 'NA',
          'Santo Domingo', 'DOP', 'es', '6', '15', '', ''),
         ('63', 'DZ', 'Algeria', 'الجزائر', '213', 'AF', 'Algiers', 'DZD', 'ar', '6', '15', '', ''),
         ('64', 'EC', 'Ecuador', 'Ecuador', '593', 'SA', 'Quito', 'USD', 'es', '6', '15', '', ''),
         ('65', 'EE', 'Estonia', 'Eesti', '372', 'EU', 'Tallinn', 'EUR', 'et', '6', '15', '', ''),
         ('66', 'EG', 'Egypt', 'مصر‎', '20', 'AF', 'Cairo', 'EGP', 'ar', '6', '15', '', ''),
         ('67', 'EH', 'Western Sahara', 'الصحراء الغربية', '212', 'AF', 'El Aaiún', 'MAD,DZD,MRO', 'es',
          '6', '15', '', ''),
         ('68', 'ER', 'Eritrea', 'ኤርትራ', '291', 'AF', 'Asmara', 'ERN', 'ti,ar,en', '6', '15', '', ''),
         ('69', 'ET', 'Ethiopia', 'ኢትዮጵያ', '251', 'AF', 'Addis Ababa', 'ETB', 'am', '6', '15', '', ''),
         ('70', 'FI', 'Finland', 'Suomi', '358', 'EU', 'Helsinki', 'EUR', 'fi,sv', '6', '15', '', ''),
         ('71', 'FJ', 'Fiji', 'Fiji', '679', 'OC', 'Suva', 'FJD', 'en,fj,hi,ur', '6', '15', '', ''),
         ('72', 'FK', 'Falkland Islands', 'Falkland Islands', '500', 'SA', 'Stanley', 'FKP', 'en', '6',
          '15', '', ''),
         ('73', 'FM', 'Micronesia', 'Micronesia', '691', 'OC', 'Palikir', 'USD', 'en', '6', '15', '', ''),
         ('74', 'FO', 'Faroe Islands', 'Føroyar', '298', 'EU', 'Tórshavn', 'DKK', 'fo', '6', '15', '', ''),
         ('75', 'FR', 'France', 'France', '33', 'EU', 'Paris', 'EUR', 'fr', '6', '15', '', ''),
         ('76', 'GA', 'Gabon', 'Gabon', '241', 'AF', 'Libreville', 'XAF', 'fr', '6', '15', '', ''),
         ('77', 'GB', 'United Kingdom', 'United Kingdom', '44', 'EU', 'London', 'GBP', 'en', '6', '15', '',
          ''),
         ('78', 'GD', 'Grenada', 'Grenada', '1473', 'NA', 'St. George''s', 'XCD', 'en', '6', '15', '', ''),
         ('79', 'GE', 'Georgia',
          'საქართველო',
           '995', 'AS', 'Tbilisi', 'GEL', 'ka', '6', '15', '', ''),
         ('80', 'GF', 'French Guiana', 'Guyane française', '594', 'SA', 'Cayenne', 'EUR', 'fr', '6', '15',
          '', ''),
         ('81', 'GG', 'Guernsey', 'Guernsey', '44', 'EU', 'St. Peter Port', 'GBP', 'en,fr', '6', '15',
          '', ''),
         ('82', 'GH', 'Ghana', 'Ghana', '233', 'AF', 'Accra', 'GHS', 'en', '6', '15', '', ''),
         ('83', 'GI', 'Gibraltar', 'Gibraltar', '350', 'EU', 'Gibraltar', 'GIP', 'en', '6', '15', '', ''),
         ('84', 'GL', 'Greenland', 'Kalaallit Nunaat', '299', 'NA', 'Nuuk', 'DKK', 'kl', '6', '15', '', ''),
         ('85', 'GM', 'Gambia', 'Gambia', '220', 'AF', 'Banjul', 'GMD', 'en', '6', '15', '', ''),
         ('86', 'GN', 'Guinea', 'Guinée', '224', 'AF', 'Conakry', 'GNF', 'fr,ff', '6', '15', '', ''),
         ('87', 'GP', 'Guadeloupe', 'Guadeloupe', '590', 'NA', 'Basse-Terre', 'EUR', 'fr', '6', '15', '', ''),
         ('88', 'GQ', 'Equatorial Guinea', 'Guinea Ecuatorial', '240', 'AF', 'Malabo', 'XAF', 'es,fr',
          '6', '15', '', ''),
         ('89', 'GR', 'Greece', 'Ελλάδα', '30', 'EU', 'Athens', 'EUR', 'el', '6', '15', '', ''),
         ('90', 'GS', 'South Georgia and the South Sandwich Islands', 'South Georgia', '500', 'AN',
          'King Edward Point', 'GBP', 'en', '6', '15', '', ''),
         ('91', 'GT', 'Guatemala', 'Guatemala', '502', 'NA', 'Guatemala City', 'GTQ', 'es', '6', '15',
          '', ''),
         ('92', 'GU', 'Guam', 'Guam', '1671', 'OC', 'Hagåtña', 'USD', 'en,ch,es', '6', '15', '', ''),
         ('93', 'GW', 'Guinea-Bissau', 'Guiné-Bissau', '245', 'AF', 'Bissau', 'XOF', 'pt', '6', '15',
          '', ''),
         ('94', 'GY', 'Guyana', 'Guyana', '592', 'SA', 'Georgetown', 'GYD', 'en', '6', '15', '', ''),
         
         ('95', 'HK', 'Hong Kong', '香港', '852', 'AS', 'City of Victoria', 'HKD', 'zh,en', '6', '15', '', ''),
         ('96', 'HM', 'Heard Island and McDonald Islands', 'Heard Island and McDonald Islands', '',
          'AN', '', 'AUD', 'en', '6', '15', '', ''),
         ('97', 'HN', 'Honduras', 'Honduras', '504', 'NA', 'Tegucigalpa', 'HNL', 'es', '6', '15', '', ''),
         ('98', 'HR', 'Croatia', 'Hrvatska', '385', 'EU', 'Zagreb', 'HRK', 'hr', '6', '15', '', ''),
         ('99', 'HT', 'Haiti', 'Haïti', '509', 'NA', 'Port-au-Prince', 'HTG,USD', 'fr,ht', '6', '15', '', ''),
         ('100', 'HU', 'Hungary', 'Magyarország', '36', 'EU', 'Budapest', 'HUF', 'hu', '6', '15', '', ''),
         ('101', 'ID', 'Indonesia', 'Indonesia', '62', 'AS', 'Jakarta', 'IDR', 'id', '6', '15', '', ''),
         ('102', 'IE', 'Ireland', 'Éire', '353', 'EU', 'Dublin', 'EUR', 'ga,en', '6', '15', '', ''),
         ('103', 'IL', 'Israel', 'יִשְׂרָאֵל', '972', 'AS', 'Jerusalem', 'ILS', 'he,ar', '6', '15', '', ''),
         ('104', 'IM', 'Isle of Man', 'Isle of Man', '44', 'EU', 'Douglas', 'GBP', 'en,gv', '6', '15', '', ''),
         ('105', 'IN', 'India', 'भारत', '91', 'AS', 'New Delhi', 'INR', 'hi,en', '6', '15', '', ''),
         ('106', 'IO', 'British Indian Ocean Territory', 'British Indian Ocean Territory', '246', 'AS',
          'Diego Garcia', 'USD', 'en', '6', '15', '', ''),
         ('107', 'IQ', 'Iraq', 'العراق', '964', 'AS', 'Baghdad', 'IQD', 'ar,ku', '6', '15', '', ''),
         ('108', 'IR', 'Iran', 'Irān', '98', 'AS', 'Tehran', 'IRR', 'fa', '6', '15', '', ''),
         ('109', 'IS', 'Iceland', 'Ísland', '354', 'EU', 'Reykjavik', 'ISK', 'is', '6', '15', '', ''),
         ('110', 'IT', 'Italy', 'Italia', '39', 'EU', 'Rome', 'EUR', 'it', '6', '15', '', ''),
         ('111', 'JE', 'Jersey', 'Jersey', '44', 'EU', 'Saint Helier', 'GBP', 'en,fr', '6', '15', '', ''),
         ('112', 'JM', 'Jamaica', 'Jamaica', '1876', 'NA', 'Kingston', 'JMD', 'en', '6', '15', '', ''),
         ('113', 'JO', 'Jordan', 'الأردن', '962', 'AS', 'Amman', 'JOD', 'ar', '6', '15', '', ''),
         ('114', 'JP', 'Japan', '日本', '81', 'AS', 'Tokyo', 'JPY', 'ja', '6', '15', '', ''),
         ('115', 'KE', 'Kenya', 'Kenya', '254', 'AF', 'Nairobi', 'KES', 'en,sw', '6', '15', '', ''),
         ('116', 'KG', 'Kyrgyzstan', 'Кыргызстан', '996', 'AS', 'Bishkek', 'KGS', 'ky,ru', '6', '15', '', ''),
         ('117', 'KH', 'Cambodia', 'Kâmpŭchéa', '855', 'AS', 'Phnom Penh', 'KHR', 'km', '6', '15', '', ''),
         ('118', 'KI', 'Kiribati', 'Kiribati', '686', 'OC', 'South Tarawa', 'AUD', 'en', '6', '15', '', ''),
         ('119', 'KM', 'Comoros', 'Komori', '269', 'AF', 'Moroni', 'KMF', 'ar,fr', '6', '15', '', ''),
         ('120', 'KN', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', '1869', 'NA', 'Basseterre',
          'XCD', 'en', '6', '15', '', ''),
         ('121', 'KP', 'North Korea', '북한', '850', 'AS', 'Pyongyang', 'KPW', 'ko', '6', '15', '', ''),
         ('122', 'KR', 'South Korea', '대한민국', '82', 'AS', 'Seoul', 'KRW', 'ko', '6', '15', '', ''),
         ('123', 'KW', 'Kuwait', 'الكويت', '965', 'AS', 'Kuwait City', 'KWD', 'ar', '6', '15', '', ''),
         ('124', 'KY', 'Cayman Islands', 'Cayman Islands', '1345', 'NA', 'George Town', 'KYD', 'en', '6',
          '15', '', ''),
         ('125', 'KZ', 'Kazakhstan', 'Қазақстан', '76,77', 'AS', 'Astana', 'KZT', 'kk,ru', '6', '15', '', ''),
         ('126', 'LA', 'Laos', 'ສປປລາວ', '856', 'AS', 'Vientiane', 'LAK', 'lo', '6', '15', '', ''),
         ('127', 'LB', 'Lebanon', 'لبنان', '961', 'AS', 'Beirut', 'LBP', 'ar,fr', '6', '15', '', ''),
         ('128', 'LC', 'Saint Lucia', 'Saint Lucia', '1758', 'NA', 'Castries', 'XCD', 'en', '6', '15', '', ''),
         ('129', 'LI', 'Liechtenstein', 'Liechtenstein', '423', 'EU', 'Vaduz', 'CHF', 'de', '6', '15', '', ''),
         ('130', 'LK', 'Sri Lanka', 'śrī laṃkāva', '94', 'AS', 'Colombo', 'LKR', 'si,ta', '6', '15', '', ''),
         ('131', 'LR', 'Liberia', 'Liberia', '231', 'AF', 'Monrovia', 'LRD', 'en', '6', '15', '', ''),
         ('132', 'LS', 'Lesotho', 'Lesotho', '266', 'AF', 'Maseru', 'LSL,ZAR', 'en,st', '6', '15', '', ''),
         ('133', 'LT', 'Lithuania', 'Lietuva', '370', 'EU', 'Vilnius', 'LTL', 'lt', '6', '15',
          '', ''),
         ('134', 'LU', 'Luxembourg', 'Luxembourg', '352', 'EU', 'Luxembourg', 'EUR', 'fr,de,lb',
          '6', '15', '', ''),
         ('135', 'LV', 'Latvia', 'Latvija', '371', 'EU', 'Riga', 'EUR', 'lv', '6', '15', '', ''),
         ('136', 'LY', 'Libya', '‏ليبيا', '218', 'AF', 'Tripoli', 'LYD', 'ar', '6', '15', '', ''),
         ('137', 'MA', 'Morocco', 'المغرب', '212', 'AF', 'Rabat', 'MAD', 'ar', '6', '15', '', ''),
         ('138', 'MC', 'Monaco', 'Monaco', '377', 'EU', 'Monaco', 'EUR', 'fr', '6', '15', '', ''),
         ('139', 'MD', 'Moldova', 'Moldova', '373', 'EU', 'Chișinău', 'MDL', 'ro', '6', '15', '',
          ''),
         ('140', 'ME', 'Montenegro', 'Црна Гора', '382', 'EU', 'Podgorica', 'EUR', 'sr,bs,sq,hr',
          '6', '15', '', ''),
         ('141', 'MF', 'Saint Martin', 'Saint-Martin', '590', 'NA', 'Marigot', 'EUR', 'en,fr,nl',
          '6', '15', '', ''),
         ('142', 'MG', 'Madagascar', 'Madagasikara', '261', 'AF', 'Antananarivo', 'MGA', 'fr,mg',
          '6', '15', '', ''),
         ('143', 'MH', 'Marshall Islands', 'M̧ajeļ', '692', 'OC', 'Majuro', 'USD', 'en,mh', '6', '15', '', ''),
         ('144', 'MK', 'Macedonia', 'Македонија', '389', 'EU', 'Skopje', 'MKD', 'mk', '6', '15', '', ''),
         ('145', 'ML', 'Mali', 'Mali', '223', 'AF', 'Bamako', 'XOF', 'fr', '6', '15', '', ''),
         ('146', 'MM', 'Myanmar [Burma]', 'Myanma', '95', 'AS', 'Naypyidaw', 'MMK', 'my', '6', '15', '', ''),
         ('147', 'MN', 'Mongolia', 'Монгол улс', '976', 'AS', 'Ulan Bator', 'MNT', 'mn', '6', '15', '', ''),
         ('148', 'MO', 'Macao', '澳門', '853', 'AS', '', 'MOP', 'zh,pt', '6', '15', '', ''),
         ('149', 'MP', 'Northern Mariana Islands', 'Northern Mariana Islands', '1670', 'OC',
          'Saipan', 'USD', 'en,ch', '6', '15', '', ''),
         ('150', 'MQ', 'Martinique', 'Martinique', '596', 'NA', 'Fort-de-France', 'EUR', 'fr', '6', '15',
          '', ''),
         ('151', 'MR', 'Mauritania', 'موريتانيا', '222', 'AF', 'Nouakchott', 'MRO', 'ar', '6', '15', '', ''),
         ('152', 'MS', 'Montserrat', 'Montserrat', '1664', 'NA', 'Plymouth', 'XCD', 'en', '6', '15', '', ''),
         ('153', 'MT', 'Malta', 'Malta', '356', 'EU', 'Valletta', 'EUR', 'mt,en', '6', '15', '', ''),
         ('154', 'MU', 'Mauritius', 'Maurice', '230', 'AF', 'Port Louis', 'MUR', 'en', '6', '15', '', ''),
         ('155', 'MV', 'Maldives', 'Maldives', '960', 'AS', 'Malé', 'MVR', 'dv', '6', '15', '', ''),
         ('156', 'MW', 'Malawi', 'Malawi', '265', 'AF', 'Lilongwe', 'MWK', 'en,ny', '6', '15', '', ''),
         ('157', 'MX', 'Mexico', 'México', '52', 'NA', 'Mexico City', 'MXN', 'es', '6', '15', '', ''),
         ('158', 'MY', 'Malaysia', 'Malaysia', '60', 'AS', 'Kuala Lumpur', 'MYR', '', '6', '15', '', ''),
         ('159', 'MZ', 'Mozambique', 'Moçambique', '258', 'AF', 'Maputo', 'MZN', 'pt', '6', '15', '', ''),
         ('160', 'NA', 'Namibia', 'Namibia', '264', 'AF', 'Windhoek', 'NAD,ZAR', 'en,af', '6', '15', '', ''),
         ('161', 'NC', 'New Caledonia', 'Nouvelle-Calédonie', '687', 'OC', 'Nouméa', 'XPF', 'fr', '6',
          '15', '', ''),
         ('162', 'NE', 'Niger', 'Niger', '227', 'AF', 'Niamey', 'XOF', 'fr', '6', '15', '', ''),
         ('163', 'NF', 'Norfolk Island', 'Norfolk Island', '672', 'OC', 'Kingston', 'AUD', 'en', '6',
          '15', '', ''),
         ('164', 'NG', 'Nigeria', 'Nigeria', '234', 'AF', 'Abuja', 'NGN', 'en', '6', '15', '', ''),
         ('165', 'NI', 'Nicaragua', 'Nicaragua', '505', 'NA', 'Managua', 'NIO', 'es', '6', '15', '', ''),
         ('166', 'NL', 'Netherlands', 'Nederland', '31', 'EU', 'Amsterdam', 'EUR', 'nl', '6', '15', '', ''),
         ('167', 'NO', 'Norway', 'Norge', '47', 'EU', 'Oslo', 'NOK', 'no,nb,nn', '6', '15', '', ''),
         ('168', 'NP', 'Nepal', 'नपल', '977', 'AS', 'Kathmandu', 'NPR', 'ne', '6', '15', '', ''),
         ('169', 'NR', 'Nauru', 'Nauru', '674', 'OC', 'Yaren', 'AUD', 'en,na', '6', '15', '', ''),
         ('170', 'NU', 'Niue', 'Niuē', '683', 'OC', 'Alofi', 'NZD', 'en', '6', '15', '', ''),
         ('171', 'NZ', 'New Zealand', 'New Zealand', '64', 'OC', 'Wellington', 'NZD', 'en,mi', '6',
          '15', '', ''),
         ('172', 'OM', 'Oman', 'عمان', '968', 'AS', 'Muscat', 'OMR', 'ar', '6', '15', '', ''),
         ('173', 'PA', 'Panama', 'Panamá', '507', 'NA', 'Panama City', 'PAB,USD', 'es', '6', '15', '', ''),
         ('174', 'PE', 'Peru', 'Perú', '51', 'SA', 'Lima', 'PEN', 'es', '6', '15', '', ''),
         ('175', 'PF', 'French Polynesia', 'Polynésie française', '689', 'OC', 'Papeetē', 'XPF', 'fr', '6',
          '15', '', ''),
         ('176', 'PG', 'Papua New Guinea', 'Papua Niugini', '675', 'OC', 'Port Moresby', 'PGK', 'en', '6',
          '15', '', ''),
         ('177', 'PH', 'Philippines', 'Pilipinas', '63', 'AS', 'Manila', 'PHP', 'en', '6', '15', '', ''),
         ('178', 'PK', 'Pakistan', 'Pakistan', '92', 'AS', 'Islamabad', 'PKR', 'en,ur', '6', '15', '', ''),
         ('179', 'PL', 'Poland', 'Polska', '48', 'EU', 'Warsaw', 'PLN', 'pl', '6', '15', '', ''),
         ('180', 'PM', 'Saint Pierre and Miquelon', 'Saint-Pierre-et-Miquelon', '508', 'NA',
          'Saint-Pierre', 'EUR', 'fr', '6', '15', '', ''),
         ('181', 'PN', 'Pitcairn Islands', 'Pitcairn Islands', '64', 'OC', 'Adamstown', 'NZD', 'en', '6',
          '15', '', ''),
         ('182', 'PR', 'Puerto Rico', 'Puerto Rico', '1787,1939', 'NA', 'San Juan', 'USD', 'es,en', '6',
          '15', '', ''),
         ('183', 'PS', 'Palestine', 'فلسطين', '970', 'AS', 'Ramallah', 'ILS', 'ar', '6', '15', '', ''),
         ('184', 'PT', 'Portugal', 'Portugal', '351', 'EU', 'Lisbon', 'EUR', 'pt', '6', '15', '', ''),
         ('185', 'PW', 'Palau', 'Palau', '680', 'OC', 'Ngerulmud', 'USD', 'en', '6', '15', '', ''),
         ('186', 'PY', 'Paraguay', 'Paraguay', '595', 'SA', 'Asunción', 'PYG', 'es,gn', '6', '15', '', ''),
         ('187', 'QA', 'Qatar', 'قطر', '974', 'AS', 'Doha', 'QAR', 'ar', '6', '15', '', ''),
         ('188', 'RE', 'Réunion', 'La Réunion', '262', 'AF', 'Saint-Denis', 'EUR', 'fr', '6', '15', '', ''),
         ('189', 'RO', 'Romania', 'România', '40', 'EU', 'Bucharest', 'RON', 'ro', '6', '15', '', ''),
         ('190', 'RS', 'Serbia', 'Србија', '381', 'EU', 'Belgrade', 'RSD', 'sr', '6', '15', '', ''),
         ('191', 'RU', 'Russia', 'Россия', '7', 'EU', 'Moscow', 'RUB', 'ru', '6', '15', '', ''),
         ('192', 'RW', 'Rwanda', 'Rwanda', '250', 'AF', 'Kigali', 'RWF', 'rw,en,fr', '6', '15', '', ''),
         ('193', 'SA', 'Saudi Arabia', 'العربية السعودية', '966', 'AS', 'Riyadh', 'SAR', 'ar', '6',
          '15', '', ''),
         ('194', 'SB', 'Solomon Islands', 'Solomon Islands', '677', 'OC', 'Honiara', 'SDB', 'en', '6',
          '15', '', ''),
         ('195', 'SC', 'Seychelles', 'Seychelles', '248', 'AF', 'Victoria', 'SCR', 'fr,en', '6', '15',
          '', ''),
         ('196', 'SD', 'Sudan', 'السودان', '249', 'AF', 'Khartoum', 'SDG', 'ar,en', '6', '15', '', ''),
         ('197', 'SE', 'Sweden', 'Sverige', '46', 'EU', 'Stockholm', 'SEK', 'sv', '6', '15', '', ''),
         ('198', 'SG', 'Singapore', 'Singapore', '65', 'AS', 'Singapore', 'SGD', 'en,ms,ta,zh', '6',
          '15', '', ''),
         ('199', 'SH', 'Saint Helena', 'Saint Helena', '290', 'AF', 'Jamestown', 'SHP', 'en', '6', '15',
          '', ''),
         ('200', 'SI', 'Slovenia', 'Slovenija', '386', 'EU', 'Ljubljana', 'EUR', 'sl', '6', '15', '', ''),
         ('201', 'SJ', 'Svalbard and Jan Mayen', 'Svalbard og Jan Mayen', '4779', 'EU', 'Longyearbyen',
          'NOK', 'no', '6', '15', '', ''),
         ('202', 'SK', 'Slovakia', 'Slovensko', '421', 'EU', 'Bratislava', 'EUR', 'sk', '6', '15', '', ''),
         ('203', 'SL', 'Sierra Leone', 'Sierra Leone', '232', 'AF', 'Freetown', 'SLL', 'en', '6', '15', '',
          ''),
         ('204', 'SM', 'San Marino', 'San Marino', '378', 'EU', 'City of San Marino', 'EUR', 'it', '6', '15',
          '', ''),
         ('205', 'SN', 'Senegal', 'Sénégal', '221', 'AF', 'Dakar', 'XOF', 'fr', '6', '15', '', ''),
         ('206', 'SO', 'Somalia', 'Soomaaliya', '252', 'AF', 'Mogadishu', 'SOS', 'so,ar', '6', '15', '', ''),
         ('207', 'SR', 'Suriname', 'Suriname', '597', 'SA', 'Paramaribo', 'SRD', 'nl', '6', '15', '', ''),
         ('208', 'SS', 'South Sudan', 'South Sudan', '211', 'AF', 'Juba', 'SSP', 'en', '6', '15', '', ''),
         ('209', 'ST', 'São Tomé and Príncipe', 'São Tomé e Príncipe', '239', 'AF', 'São Tomé', 'STD',
          'pt', '6', '15', '', ''),
         ('210', 'SV', 'El Salvador', 'El Salvador', '503', 'NA', 'San Salvador', 'SVC,USD', 'es', '6',
          '15', '', ''),
         ('211', 'SX', 'Sint Maarten', 'Sint Maarten', '1721', 'NA', 'Philipsburg', 'ANG', 'nl,en', '6',
          '15', '', ''),
         ('212', 'SY', 'Syria', 'سوريا', '963', 'AS', 'Damascus', 'SYP', 'ar', '6', '15', '', ''),
         ('213', 'SZ', 'Swaziland', 'Swaziland', '268', 'AF', 'Lobamba', 'SZL', 'en,ss', '6', '15', '', ''),
         ('214', 'TC', 'Turks and Caicos Islands', 'Turks and Caicos Islands', '1649', 'NA',
          'Cockburn Town', 'USD', 'en', '6', '15', '', ''),
         ('215', 'TD', 'Chad', 'Tchad', '235', 'AF', 'N''Djamena', 'XAF', 'fr,ar', '6', '15', '', ''),
         ('216', 'TF', 'French Southern Territories', 'Territoire des Terres australes et antarctiques fr',
          '', 'AN', 'Port-aux-Français', 'EUR', 'fr', '6', '15', '', ''),
         ('217', 'TG', 'Togo', 'Togo', '228', 'AF', 'Lomé', 'XOF', 'fr', '6', '15', '', ''),
         ('218', 'TH', 'Thailand',
          'ประเทศไทย',
           '66', 'AS', 'Bangkok', 'THB', 'th', '6', '15', '', ''),
         ('219', 'TJ', 'Tajikistan', 'Тоҷикистон', '992', 'AS', 'Dushanbe', 'TJS', 'tg,ru', '6', '15', '',
          ''),
         ('220', 'TK', 'Tokelau', 'Tokelau', '690', 'OC', 'Fakaofo', 'NZD', 'en', '6', '15', '', ''),
         ('221', 'TL', 'East Timor', 'Timor-Leste', '670', 'OC', 'Dili', 'USD', 'pt', '6', '15', '', ''),
         ('222', 'TM', 'Turkmenistan', 'Türkmenistan', '993', 'AS', 'Ashgabat', 'TMT', 'tk,ru', '6', '15',
          '', ''),
         ('223', 'TN', 'Tunisia', 'تونس', '216', 'AF', 'Tunis', 'TND', 'ar', '6', '15', '', ''),
         ('224', 'TO', 'Tonga', 'Tonga', '676', 'OC', 'Nuku''alofa', 'TOP', 'en,to', '6', '15', '', ''),
         ('225', 'TR', 'Turkey', 'Türkiye', '90', 'AS', 'Ankara', 'TRY', 'tr', '6', '15', '', ''),
         ('226', 'TT', 'Trinidad and Tobago', 'Trinidad and Tobago', '1868', 'NA', 'Port of Spain', 'TTD',
          'en', '6', '15', '', ''),
         ('227', 'TV', 'Tuvalu', 'Tuvalu', '688', 'OC', 'Funafuti', 'AUD', 'en', '6', '15', '', ''),
         ('228', 'TW', 'Taiwan', '臺灣', '886', 'AS', 'Taipei', 'TWD', 'zh', '6', '15', '', ''),
         ('229', 'TZ', 'Tanzania', 'Tanzania', '255', 'AF', 'Dodoma', 'TZS', 'sw,en', '6', '15', '', ''),
         ('230', 'UA', 'Ukraine', 'Україна', '380', 'EU', 'Kiev', 'UAH', 'uk', '6', '15', '', ''),
         ('231', 'UG', 'Uganda', 'Uganda', '256', 'AF', 'Kampala', 'UGX', 'en,sw', '6', '15', '', ''),
         ('232', 'UM', 'U.S. Minor Outlying Islands', 'United States Minor Outlying Islands', '', 'OC',
          '', 'USD', 'en', '6', '15', '', ''),
         ('233', 'US', 'United States', 'United States', '1', 'NA', 'Washington D.C.', 'USD,USN,USS',
          'en', '6', '15', '', ''),
         ('234', 'UY', 'Uruguay', 'Uruguay', '598', 'SA', 'Montevideo', 'UYI,UYU', 'es', '6', '15', '',
          ''),
         ('235', 'UZ', 'Uzbekistan', 'O‘zbekiston', '998', 'AS', 'Tashkent', 'UZS', 'uz,ru', '6', '15',
          '', ''),
         ('236', 'VA', 'Vatican City', 'Vaticano', '39066,379', 'EU', 'Vatican City', 'EUR', 'it,la',
          '6', '15', '', ''),
         ('237', 'VC', 'Saint Vincent and the Grenadines', 'Saint Vincent and the Grenadines', '1784',
          'NA', 'Kingstown', 'XCD', 'en', '6', '15', '', ''),
         ('238', 'VE', 'Venezuela', 'Venezuela', '58', 'SA', 'Caracas', 'VEF', 'es', '6', '15', '', ''),
         ('239', 'VG', 'British Virgin Islands', 'British Virgin Islands', '1284', 'NA', 'Road Town',
          'USD', 'en', '6', '15', '', ''),
         ('240', 'VI', 'U.S. Virgin Islands', 'United States Virgin Islands', '1340', 'NA',
          'Charlotte Amalie', 'USD', 'en', '6', '15', '', ''),
         ('241', 'VN', 'Vietnam', 'Việt Nam', '84', 'AS', 'Hanoi', 'VND', 'vi', '6', '15', '', ''),
         ('242', 'VU', 'Vanuatu', 'Vanuatu', '678', 'OC', 'Port Vila', 'VUV', 'bi,en,fr', '6', '15', '', ''),
         ('243', 'WF', 'Wallis and Futuna', 'Wallis et Futuna', '681', 'OC', 'Mata-Utu', 'XPF', 'fr', '6',
          '15', '', ''),
         ('244', 'WS', 'Samoa', 'Samoa', '685', 'OC', 'Apia', 'WST', 'sm,en', '6', '15', '', ''),
         ('245', 'XK', 'Kosovo', 'Republika e Kosovës', '377,381,386', 'EU', 'Pristina', 'EUR', 'sq,sr',
          '6', '15', '', ''),
         ('246', 'YE', 'Yemen', 'اليَمَن', '967', 'AS', 'Sana''a', 'YER', 'ar', '6', '15', '', ''),
         ('247', 'YT', 'Mayotte', 'Mayotte', '262', 'AF', 'Mamoudzou', 'EUR', 'fr', '6', '15', '', ''),
         ('248', 'ZA', 'South Africa', 'South Africa', '27', 'AF', 'Pretoria', 'ZAR', 
         'af,en,nr,st,ss,tn,ts,ve,xh,zu', '6', '15', '', ''),
         ('249', 'ZM', 'Zambia', 'Zambia', '260', 'AF', 'Lusaka', 'ZMK', 'en', '6', '15', '', ''),
         ('250', 'ZW', 'Zimbabwe', 'Zimbabwe', '263', 'AF', 'Harare', 'ZWL', 'en,sn,nd', '6', '15', '', '');
        ";

        $wpdb->query($query);
    }


    //CREATE HOOKS
    // Notifications
    $ordersSQL  = "";
    $msgSQL     = "";

    $orderStates = wc_get_order_statuses();

    // creamos notificaciones para customers

    // customer orderStatusChanged
    foreach ($orderStates as $orderName => $orderTranslate) {
        $ordersSQL .= "('customer','orderStatusChanged', '".$orderName."', 0),";
    }

    // nuevo pedido
    $ordersSQL .= "('customer','newOrderHook', null, 0),";

    // pago confirmado
    $ordersSQL .= "('customer','paymentCompletedHook', null, 0),";

    // order refunded
    $ordersSQL .= "('customer','orderRefunded', null, 0),";

    // creamos notificaciones para admin
    foreach ($orderStates as $orderName => $orderTranslate) {
        // customer actionOrderStatusPostUpdate
        $ordersSQL .= "('admin','orderStatusChanged', '".$orderName."', 0),";
    }

    // nuevo pedido
    $ordersSQL .= "('admin','newOrderHook', null, 0),";

    // pago confirmado
    $ordersSQL .= "('admin','paymentCompletedHook', null, 0),";

    // order refunded
    $ordersSQL .= "('admin','orderRefunded', null, 0),";

    // deleted product
    $ordersSQL .= "('admin','deletedProduct', null, 0),";

    // clean last comma
    $ordersSQL = trim($ordersSQL, ",");

    if (! $wpdb->query("SELECT * from ".$wpdb->prefix."mensagia_sms_notifications")) {
        $wpdb->query("INSERT INTO  ".$wpdb->prefix."mensagia_sms_notifications 
        (`type`, `hook`, `option_name`, `active`) VALUES " . $ordersSQL.";");
    }

    $default_states_msg_customer = array(
        'es' => "Estimado/a {customer_firstname}, el estado de su pedido [{order_id}] ha cambiado a: ",
        'ca' => "Benvolgut/da {customer_firstname}, l\\'estat de la seva comanda [{order_id}] ".
            "ha canviat a: ",
        'gl' => "Estimado {customer_firstname}, o estado do seu pedido [{order_id}] cambiou a: ",
        'eu' => "Kaixo {customer_firstname}, zure eskaera [{order_id}] estatusa aldatu: ",
        'en' => "Dear {customer_firstname}, the status of your order [{order_id}] has been changed to: ",
        'fr' => "Cher client {firstname}, l\\'état de votre commande [{order_id}] changé: ",
        'de' => "Lieber {customer_firstname}, der Status Ihrer Bestellung [{order_id}] geändert: ",
        'it' => "Caro {customer_firstname}, lo stato del tuo nuovo ordine [{order_id}] è cambiato in:",
    );

    $default_states_msg_admin = array(
        'es' => "{shop_name}: El estado del pedido [{order_id}] ha cambiado a: ",
        'ca' => "{shop_name}: L\\'estat de la comanda [{order_id}] ha canviat a: ",
        'gl' => "{shop_name}: O estado da solicitude [{order_id}] cambiou a: ",
        'eu' => "{shop_name}: Ordena estatusa [{order_id}] duela ezarri da: ",
        'en' => "{shop_name}: Order status [{order_id}] changed to: ",
        'fr' => "{shop_name}: L\\'état de la commande [{order_id}] a changé: ",
        'de' => "{shop_name}: Der Auftragsstatus [{order_id}] hat sich geändert: ",
        'it' => "{shop_name}: Lo stato dell'ordine [{order_id}] è stato modificato in: ",
    );

    // NUEVO PEDIDO

    $default_neworder_msg_customer = array(
        'es' => "Su nuevo pedido [{order_id}] ha sido creado correctamente. ".
            "Modo de pago: {order_payment_method}. Importe: {order_total_paid}{order_currency}",
        'ca' => "La seva comanda [{order_id}] s\\'ha creat correctament. ".
            "Metode de pagament: {order_payment_method}. Import: {order_total_paid}{order_currency}",
        'gl' => "A súa nova orde [{order_id}] foi creado correctamente.".
            "Modo pagamento: {order_payment_method}. Cantidade: {order_total_paid}{order_currency}",
        'eu' => "Zure eskaera berririk [{order_id}] ek sortu du correctamente. ".
            "Modo ordainketa: {order_payment_method}. Zenbatekoa: {order_total_paid}{order_currency}",
        'en' => "Your new order [{order_id}] was created successfully. ".
            "Payment method: {order_payment_method}. Amount: {order_total_paid} {order_currency}",
        'fr' => "Votre nouvelle commande [{order_id}] a été créée avec succès. ".
            "Mode de paiement : {order_payment_method}. Montant : {order_total_paid}{order_currency}",
        'de' => "Ihre neue Bestellung [{order_id}] wurde erfolgreich erstellt. ".
            "Zahlungsweise: {order_payment_method}. Betrag: {order_total_paid}{order_currency}",
        'it' => "Il tuo nuovo ordine [{order_id}] è stato creato con successo. ".
            "Modalità di pagamento: {order_payment_method}. Importo: {order_total_paid}{order_currency}",
    );

    $default_neworder_msg_admin = array(
        'es' => "Nuevo pedido con id [{order_id}]. ".
            "Modo de pago: {order_payment_method}. Importe: {order_total_paid}{order_currency}",
        'ca' => "Nova comanda amb id [{order_id}]. ".
            "Metode de pagament: {order_payment_method}. Import: {order_total_paid}{order_currency}",
        'gl' => "Nova orde con id [{order_id}] ".
            "Pagamento: {order_payment_method}. Cantidade: {order_total_paid}{order_currency}",
        'eu' => "euNuevo pedido con id [{order_id}]".
            "Modo ordainketa: {order_payment_method}. Zenbatekoa: {order_total_paid}{order_currency}",
        'en' => "New order with id [{order_id}]. ".
            "Payment method: {order_payment_method}. Amount: {order_total_paid}{order_currency}",
        'fr' => "Nouvelle commande avec id {order_id}. ".
            "Mode de paiement : {order_payment_method}. Montant : {order_total_paid}{order_currency}",
        'de' => "Neue Bestellung mit ID {order_id}. ".
            "Zahlungsweise: {order_payment_method}. Betrag: {order_total_paid}{order_currency}",
        'it' => "Nuovo ordine con id {order_id}. ".
            "Modalità di pagamento: {order_payment_method}. Importo: {order_total_paid}{order_currency}",
    );


    // PAGO CONFIRMADO

    $default_pago_conf_msg_customer = array(
        'es' => "Se ha confirmado el pago de su pedido [{order_id}]. ".
            "Importe: {order_total_paid}{order_currency}",
        'ca' => "S\\'ha confirmat el pagament de la seva comanda [{order_id}]. ".
            "Import: {order_total_paid}{order_currency}",
        'gl' => "Confirmou o pago da súa solicitude [{order_id}]. ".
            "Pagamento: {order_total_paid}{order_currency}",
        'eu' => "Zure eskaera ordainketa berretsi egin da [{order_id}]. ".
            "Zenbatekoa: {order_total_paid}{order_currency}",
        'en' => "Payment for your order [{order_id}] has been confirmed.".
            "Amount: {order_total_paid}{order_currency}",
        'fr' => "Le paiement de votre commande {order_id} a été reçu correctement. ".
            "Montant : {order_total_paid}{order_currency}",
        'de' => "Die Bezahlung der Bestellung {order_id} wurde korrekt entgegengenommen. ".
            "Betrag: {order_total_paid}{order_currency}",
        'it' => "Il pagamento del tuo ordine {order_id} è stato ricevuto correttamente. ".
            "Importo: {order_total_paid}{order_currency}",
    );

    $default_pago_conf_msg_admin = array(
        'es' => "Se ha confirmado el pago del pedido [{order_id}]. ".
            "Modo de pago: {order_payment_method} Importe: {order_total_paid}{order_currency}",
        'ca' => "S\\'ha confirmat el pagament de la comanda [{order_id}]. ".
            "Metode de pagament: {order_payment_method} Import: {order_total_paid}{order_currency}",
        'gl' => "Confirmou a orde de pagamento [{order_id}]. ".
            "Pagamento: {order_payment_method} Cantidade: {order_total_paid}{order_currency}",
        'eu' => "Baieztatu du ordainketa ordena [{order_id}]. ".
            "Ordainketa: {order_payment_method} Zenbatekoa: {order_total_paid}{order_currency}",
        'en' => "Order payment [{order_id}] has been confirmed. ".
            "Payment method: {order_payment_method} Amount: {order_total_paid}{order_currency}",
        'fr' => "Le paiement de la commande a été confirmé {order_id}. ".
            "Mode de paiement : {order_payment_method} Montant : {order_total_paid}{order_currency}",
        'de' => "Die Bezahlung der Bestellung {order_id} wurde bestätigt. ".
            "Zahlungsweise: {order_payment_method} Betrag: {order_total_paid}{order_currency}",
        'it' => "È stato confermato il pagamento dell\\'ordine {order_id}. ".
            "Modalità di pagamento: {order_payment_method} Importo: {order_total_paid}{order_currency}",
    );


    $default_devolucion_customer = array(
        'es' => "Hemos recibido su solicitud de devolución del pedido [{order_id}]. ".
            "Hemos procedido con la devolución de {total_refunded} {order_currency_refunded}. Gracias.",
        'en' => "We have received your order refund request [{order_id}]. ".
            "We have proceeded with the return of {total_refunded} {order_currency_refunded}. Thank you.",
        'ca' => "Hem rebut la solicitud de la devolució de la comanda [{order_id}]. ".
            "Hem procedit amb la devolució de {total_refunded} {order_currency_refunded}. Gracies."
    );


    $default_devolucion_admin = array(
        'es' => "Se ha procedido a la devolución de {total_refunded} {order_currency_refunded} del pedido [{order_id}].",
        'en' => "A refund of {total_refunded} {order_currency_refunded} for the order [{order_id}] has been made.",
        'ca' => "S'ha procedit a la devolució de {total_refunded} {order_currency_refunded} de la comanda [{order_id}]."
    );

    $default_product_deleted_admin = array(
        'es' => "El producto {product_name} con id: {product_id} ha sido eliminado de la tienda.",
        'en' => "Product {product_name} with id: {product_id} has been removed from the store.",
        'ca' => 'El producte {product_name} amb id: {product_id} ha estat eliminat de la botiga.'
    );



    $notifications_customer = MensagiaSMSNotification::getNotifications('customer');
    $notifications_admin    = MensagiaSMSNotification::getNotifications('admin');
    $languages              = [substr(get_bloginfo('language'), 0, 2)];
    $default_lang           = substr(get_bloginfo('language'), 0, 2);
    $orderArray             = array();

    if ($orderStates) {
        foreach ($orderStates as $orderName => $orderTranslate) {
            $orderArray[$orderName."_".$default_lang] = $orderTranslate;
        }
    }

    // de clientes en todos los idiomas
    foreach ($notifications_customer as $notification) {
        foreach ($languages as $lang) {
            switch ($notification['hook']) {
                case 'orderStatusChanged':
                    if (isset($default_states_msg_customer[$lang])) {
                        $msg    = $default_states_msg_customer[$lang] .
                            $orderArray[$notification['option_name'] . "_" . $lang];
                        $msgSQL .= "( " . $notification['id'] . ", '" . $lang . "', '" . $msg . "'),";
                    } else {
                        $msg    = $default_states_msg_customer['en'] . $orderArray[$notification['option_name'] . "_".$default_lang];
                        $msgSQL .= "( " . $notification['id'] . ", '" . $lang . "', '" . $msg . "'),";
                    }

                    break;

                case 'newOrderHook':
                    if (isset($default_neworder_msg_customer[$lang])) {
                        $msg = $default_neworder_msg_customer[$lang];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    } else {
                        $msg = $default_neworder_msg_customer['en'];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    }
                    break;

                case 'paymentCompletedHook':
                    if (isset($default_pago_conf_msg_customer[$lang])) {
                        $msg = $default_pago_conf_msg_customer[$lang];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    } else {
                        $msg = $default_pago_conf_msg_customer['en'];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    }
                    break;

                case 'orderRefunded':
                    if (isset($default_devolucion_customer[$lang])) {
                        $msg = $default_devolucion_customer[$lang];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    } else {
                        $msg = $default_devolucion_customer['en'];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    }
                    break;
            }
        }
    }

    // de administradores en todos los idiomas
    foreach ($notifications_admin as $notification) {
        foreach ($languages as $lang) {
            switch ($notification['hook']) {
                case 'orderStatusChanged':
                    if (isset($default_states_msg_admin[$lang])) {
                        $msg    = $default_states_msg_admin[$lang] .
                            $orderArray[$notification['option_name'] . "_" . $lang];
                        $msgSQL .= "( " . $notification['id'] . ", '" . $lang . "', '" . $msg . "'),";
                    } else {
                        $msg    = $default_states_msg_admin['en'] . $orderArray[$notification['option_name'] . "_".$default_lang];
                        $msgSQL .= "( " . $notification['id'] . ", '" . $lang . "', '" . $msg . "'),";
                    }

                    break;

                case 'newOrderHook':
                    if (isset($default_neworder_msg_admin[$lang])) {
                        $msg = $default_neworder_msg_admin[$lang];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    } else {
                        $msg = $default_neworder_msg_admin['en'];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    }

                    break;

                case 'paymentCompletedHook':
                    if (isset($default_pago_conf_msg_admin[$lang])) {
                        $msg = $default_pago_conf_msg_admin[$lang];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    } else {
                        $msg = $default_pago_conf_msg_admin['en'];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    }

                    break;

                case 'orderRefunded':
                    if (isset($default_devolucion_admin[$lang])) {
                        $msg = $default_devolucion_admin[$lang];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    } else {
                        $msg = $default_devolucion_admin['en'];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    }

                    break;

                case 'deletedProduct':
                    if (isset($default_product_deleted_admin[$lang])) {
                        $msg = $default_product_deleted_admin[$lang];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    } else {
                        $msg = $default_product_deleted_admin['en'];
                        $msgSQL .= "( ".$notification['id'].", '".$lang."', '".$msg."'),";
                    }

                    break;
            }
        }
    }

    // clean last comma
    $msgSQL = trim($msgSQL, ",");

    if (! $wpdb->query("SELECT * from ".$wpdb->prefix."mensagia_sms_notifications_lang")) {
        $wpdb->query("INSERT INTO  ".$wpdb->prefix."mensagia_sms_notifications_lang 
        (`mensagia_sms_notification_id`, `lang_code`, `message`) VALUES " . $msgSQL.";");
    }
}
