CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,                             -- id
  `ref` varchar(100) DEFAULT NULL,                                              -- reference url to retrieve
  `title` tinytext,                                                             -- title of note
  `text` text,                                                                  -- text of note
  `type` enum('success','info','notice','error') NOT NULL DEFAULT 'success',    -- type
  `sticky` bit(1) NOT NULL DEFAULT b'0',                                        -- whether note is sticky
  `title_ref` varchar(100) DEFAULT NULL,                                        -- (COMMENT OUT IF NO dtext) ref to dtext item
  `text_ref` varchar(100) DEFAULT NULL,                                         -- (COMMENT OUT IF NO dtext) ref to dtext item
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ref` (`ref`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `notifications` (`id`, `ref`, `title`, `text`, `type`, `sticky`)
VALUES
	(2,'test-success','Test Success!','This is just a little test success message.','success',b'0'),
	(3,'test-error','Test Error!','Nothing bad actually happened, but this is what an error looks like. Also it is sticky.','error',b'1'),
	(4,'test-info','Test Info...','A very informative info test. Totally.','info',b'0'),
	(5,'test-notice','Test Notice:','Notice me. Because I am a notice. Thanks.','notice',b'0');