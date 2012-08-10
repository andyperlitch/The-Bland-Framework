/**
 * Internationalization
 * 
 * desc: Dynamic text, translation
 * deps: login.sql
 * 
*/

/* TABLE STATEMENTS */

-- main dynamic text table
CREATE TABLE `dtext` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
    `ref` varchar(100) DEFAULT NULL,                         -- reference string
    `context` text,                                          -- description of context
    `img` varchar(100) DEFAULT NULL,                         -- image of context
    `preload` bit(1) NOT NULL DEFAULT b'1',                  -- whether or not to preload
    `js` bit(1) NOT NULL DEFAULT b'0',                       -- whether or not to load into js
    PRIMARY KEY (`id`),
    UNIQUE KEY `ref` (`ref`),
    KEY `preload` (`preload`),
    KEY `js` (`js`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- table containing all languages
CREATE TABLE `dtext_all_langs` (
    `lang_abbr` varchar(20) NOT NULL DEFAULT 'en',             -- abbreviation for language
    `lang_name` varchar(100) DEFAULT NULL,                     -- language name in the language
    `lang_name_en` varchar(100) DEFAULT NULL,                  -- language name in english
    `lang_direction` enum('ltr','rtl') NOT NULL DEFAULT 'ltr', -- direction of language
    PRIMARY KEY (`lang_abbr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- table of languages currently available on the site
CREATE TABLE `dtext_langs` (
    `lang_abbr` varchar(20) NOT NULL DEFAULT '',       -- abbreviation of language, references dtext_langs_avail
    PRIMARY KEY (`lang_abbr`),
    CONSTRAINT `dtext_langs_ibfk_1` 
        FOREIGN KEY (`lang_abbr`) 
        REFERENCES `dtext_all_langs` (`lang_abbr`) 
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- actual entries of text in all languages being used by the site
CREATE TABLE `dtext_content` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,          -- id
    `text_id` bigint(20) unsigned NOT NULL,                    -- id of text item, refs `dtext`
    `lang_abbr` varchar(20) DEFAULT NULL,                      -- abbrev. of langs, refs `dtext_langs`
    `content` text,                                            -- content of entry
    `reviewed` bit(1) DEFAULT b'0',                            -- reviewed flag: 1 if reviewed
    `committed` bit(1) DEFAULT b'0',                           -- committed flag: 1 if committed by translators
    PRIMARY KEY (`id`),
    KEY `text_id` (`text_id`),
    KEY `lang_abbr` (`lang_abbr`),
    CONSTRAINT `dtext_content_ibfk_1` 
        FOREIGN KEY (`text_id`) 
        REFERENCES `dtext` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT `dtext_content_ibfk_2` 
        FOREIGN KEY (`lang_abbr`) 
        REFERENCES `dtext_langs` (`lang_abbr`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- intersection table of translators to managers
CREATE TABLE  `translators_mgrs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,          -- id
    `translator_id` bigint(20) UNSIGNED NOT NULL,              -- id of translator user
    `mgr_id` bigint(20) UNSIGNED NOT NULL,                     -- id of manager user
    PRIMARY KEY (`id`),
    KEY `translator_id` (`translator_id`),
    KEY `mgr_id` (`mgr_id`),
    CONSTRAINT `translator_fk`                                 -- refs translator users
        FOREIGN KEY (`translator_id`) 
        REFERENCES `users` (`user_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `mgr_fk`                                        -- refs translation_mgr users
        FOREIGN KEY (`mgr_id`) 
        REFERENCES `users` (`user_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=INNODB  DEFAULT CHARSET=utf8 ;

-- table of translators' languages
CREATE TABLE `translators_langs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,          -- id
    `translator_id` bigint(20) UNSIGNED NOT NULL,              -- id of translator
    `lang_abbr` varchar(20) NOT NULL DEFAULT '',               -- language abbreviation
    PRIMARY KEY (`id`),
    KEY `translator_id` (`translator_id`),
    KEY `lang_abbr` (`lang_abbr`),
    CONSTRAINT `translators_langs_ibfk_1`                      -- refs translator users
        FOREIGN KEY (`translator_id`) 
        REFERENCES `users` (`user_id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT `translators_langs_ibfk_2`                      -- refs dtext_langs
        FOREIGN KEY (`lang_abbr`) 
        REFERENCES `dtext_langs` (`lang_abbr`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=INNODB  DEFAULT CHARSET=utf8 ;

-- table of translator activities
CREATE TABLE `translator_actions` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,          -- id
    `translator_id` bigint(20) UNSIGNED NOT NULL,              -- id of translator user
    `content_id` bigint(20) unsigned,                          -- id from dtext_content
    `word_count` int(11) unsigned NOT NULL,                    -- count from action
    `type` enum('translated','proofread','flagged'),           -- type of action
    `include` bit(1) NOT NULL DEFAULT b'1',                    -- whether or not to include word count
    `translation` TEXT,                                        -- result text that was translated/proofread
    `prior_content` TEXT,                                      -- original text
    `date_reviewed` DATETIME NOT NULL,                         -- date of action
    PRIMARY KEY (`id`),
    KEY `translator_id` (`translator_id`),
    KEY `content_id` (`content_id`),
    KEY `type` (`type`),
    KEY `include` (`include`),
    CONSTRAINT `translator_fk`
        FOREIGN KEY (`translator_id`)
        REFERENCES `users` (`user_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `content_fk`
        FOREIGN KEY (`content_id`)
        REFERENCES `dtext_content` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=INNODB  DEFAULT CHARSET=utf8 ;

/* INSERT STATEMENTS */
-- all languages
INSERT INTO `dtext_all_langs` (`lang_abbr`, `lang_name`, `lang_name_en`, `lang_direction`)
    VALUES
        ('af','Afrikaans','Afrikaans','ltr'),
        ('am','Amharic','Amharic','ltr'),
        ('ar','العربية','Arabic','rtl'),
        ('az','Azərbaycan','Azerbaijani','ltr'),
        ('be','Беларускай','Belarusian','ltr'),
        ('bg','Български','Bulgarian','ltr'),
        ('bh','Bihari','Bihari','ltr'),
        ('bn','Bengali','Bengali','ltr'),
        ('bo','Tibetan','Tibetan','ltr'),
        ('br','Breton','Breton','ltr'),
        ('ca','Català','Catalan','ltr'),
        ('chr','Cherokee','Cherokee','ltr'),
        ('co','Corsican','Corsican','ltr'),
        ('cs','Český','Czech','ltr'),
        ('cy','Cymraeg','Welsh','ltr'),
        ('da','Dansk','Danish','ltr'),
        ('de','Deutsch','German','ltr'),
        ('dv','Dhivehi','Dhivehi','rtl'),
        ('el','Ελληνικά','Greek','ltr'),
        ('en','English','English','ltr'),
        ('eo','Esperanto','Esperanto','ltr'),
        ('es','Español','Spanish','ltr'),
        ('et','Eesti','Estonian','ltr'),
        ('eu','Euskal','Basque','ltr'),
        ('fa','فارسی','Persian','rtl'),
        ('fi','Suomi','Finnish','ltr'),
        ('fo','Faroese','Faroese','ltr'),
        ('fr','Français','French','ltr'),
        ('fy','Frisian','Frisian','ltr'),
        ('ga','Gaeilge','Irish','ltr'),
        ('gd','Scots_gaelic','Scots_gaelic','ltr'),
        ('gl','Galego','Galician','ltr'),
        ('gu','ગુજરાતી','Gujarati','ltr'),
        ('hi','हिंदी','Hindi','ltr'),
        ('hr','Hrvatski','Croatian','ltr'),
        ('ht','Haitian_creole','Haitian_creole','ltr'),
        ('hu','Magyar','Hungarian','ltr'),
        ('hy','Հայերեն','Armenian','ltr'),
        ('id','Indonesia','Indonesian','ltr'),
        ('is','Íslenska','Icelandic','ltr'),
        ('it','Italiano','Italian','ltr'),
        ('iu','Inuktitut','Inuktitut','ltr'),
        ('iw','עברית','Hebrew','rtl'),
        ('ja','日本の','Japanese','ltr'),
        ('jw','Javanese','Javanese','ltr'),
        ('ka','საქართველოს','Georgian','ltr'),
        ('kk','Kazakh','Kazakh','ltr'),
        ('km','Khmer','Khmer','ltr'),
        ('kn','ಕನ್ನಡ','Kannada','ltr'),
        ('ko','한국의','Korean','ltr'),
        ('ku','Kurdish','Kurdish','ltr'),
        ('ky','Kyrgyz','Kyrgyz','ltr'),
        ('la','Latin','Latin','ltr'),
        ('lb','Luxembourgish','Luxembourgish','ltr'),
        ('lo','Lao','Lao','ltr'),
        ('lt','Lietuvos','Lithuanian','ltr'),
        ('lv','Latvijas','Latvian','ltr'),
        ('mi','Maori','Maori','ltr'),
        ('mk','Македонски','Macedonian','ltr'),
        ('ml','Malayalam','Malayalam','ltr'),
        ('mn','Mongolian','Mongolian','ltr'),
        ('mr','Marathi','Marathi','ltr'),
        ('ms','Melayu','Malay','ltr'),
        ('mt','Maltija','Maltese','ltr'),
        ('my','Burmese','Burmese','ltr'),
        ('ne','Nepali','Nepali','ltr'),
        ('nl','Nederlands','Dutch','ltr'),
        ('no','Norwegian','Norwegian','ltr'),
        ('oc','Occitan','Occitan','ltr'),
        ('or','Oriya','Oriya','ltr'),
        ('pa','Punjabi','Punjabi','ltr'),
        ('pl','Polski','Polish','ltr'),
        ('ps','Pashto','Pashto','ltr'),
        ('pt-BR','Português Brasileiro','Portuguese_Brazil','ltr'),
        ('pt-PT','Português Europeu','Portuguese_Portugal','ltr'),
        ('qu','Quechua','Quechua','ltr'),
        ('ro','Român','Romanian','ltr'),
        ('ru','Русский','Russian','ltr'),
        ('sa','Sanskrit','Sanskrit','ltr'),
        ('sd','Sindhi','Sindhi','ltr'),
        ('si','Sinhalese','Sinhalese','ltr'),
        ('sk','Slovenských','Slovak','ltr'),
        ('sl','Slovenski','Slovenian','ltr'),
        ('sq','Shqiptar','Albanian','ltr'),
        ('sr','Српски','Serbian','ltr'),
        ('su','Sundanese','Sundanese','ltr'),
        ('sv','Svenskt','Swedish','ltr'),
        ('sw','Kiswahili','Swahili','ltr'),
        ('syr','Syriac','Syriac','rtl'),
        ('ta','தமிழ்','Tamil','ltr'),
        ('te','Telugu','Telugu','ltr'),
        ('tg','Tajik','Tajik','ltr'),
        ('th','ภาษาไทย','Thai','ltr'),
        ('tl','Na Filipino','Filipino','ltr'),
        ('to','Tonga','Tonga','ltr'),
        ('tr','Türk','Turkish','ltr'),
        ('tt','Tatar','Tatar','ltr'),
        ('ug','Uighur','Uighur','ltr'),
        ('uk','Український','Ukrainian','ltr'),
        ('ur','اردو','Urdu','rtl'),
        ('uz','Uzbek','Uzbek','ltr'),
        ('vi','Việt','Vietnamese','ltr'),
        ('yi','ייִדיש','Yiddish','rtl'),
        ('yo','Yoruba','Yoruba','ltr'),
        ('zh','中国的','Chinese','ltr'),
        ('zh-CN','Chinese_simplified','Chinese_simplified','ltr'),
        ('zh-TW','chinese_traditional','Chinese_traditional','ltr');
        
-- insert english into langs
INSERT INTO `dtext_langs` (`lang_abbr`) VALUES ('en');

-- insert role types for translator and manager
INSERT INTO `roles` (`role_id`, `role_name`, `role_description`) VALUES
    (NULL, 'translator', 'Translator for the site, able to translate items in specified languages.'),
    (NULL, 'translation_mgr', 'Translation manager, oversees actions of individual translators');