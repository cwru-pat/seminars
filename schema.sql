
CREATE TABLE `presenters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1

CREATE TABLE `seminars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `announced` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("admins", "", "Comma separated list of phpCAS IDs of people with administrative permissions");
INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("email", "", "Automated Email reply-to Address");
INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("server", 'phantom.case.edu' , "Host and path preceding root directory of site.");
INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("titlemail", 335, "Number of hours before talk to ask for titles");
INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("titleminder", 96, "Number of hours before talk to ask again about titles");
INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("announcetime", 15, "Number of hours before seminar to send email announcement");
INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("lastupdate", 0, "Unix Timestamp of last job run time");
INSERT INTO `settings`
  (`name`, `value`, `description`)
  VALUES
  ("seminartime", '5.12.45' , "w.G.i of standard seminar time");

CREATE TABLE `talks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `presenter` int(11) NOT NULL,
  `seminar` int(11) NOT NULL,
  `title` varchar(1000) NOT NULL,
  `edit_key` varchar(40) NOT NULL,
  `keymailed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
