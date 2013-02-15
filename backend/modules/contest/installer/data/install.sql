
CREATE TABLE `contest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `meta_id` int(11) DEFAULT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `success_message` text COLLATE utf8_unicode_ci NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT NULL,
  `profiles_only` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `multiple_participations` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `created_on` datetime DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;


CREATE TABLE `contest_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) DEFAULT NULL,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question` text COLLATE utf8_unicode_ci,
  `answer` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT NULL,
  `must_be_correct` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `required` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `created_on` datetime DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;


CREATE TABLE `contest_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `display_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('ok','error') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'error',
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;


CREATE TABLE `contest_user_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contest_user_id` int(11) DEFAULT NULL,
  `answer_id` int(11) DEFAULT NULL,
  `answer` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `correct` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  `points` int(11) NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=37 ;
