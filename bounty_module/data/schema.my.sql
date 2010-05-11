CREATE TABLE `bounty` (
  `bounty_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `sponsor` varchar(255) NOT NULL,
  `winner` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `value` float(5,2) NOT NULL,
  `status_id` smallint(6) NOT NULL,
  `target_completion_date` date NOT NULL,
  `date_created` date NOT NULL default '0000-00-00',
  `last_updated` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`bounty_id`)
);

CREATE TABLE `bounty_status` (
  `bounty_status_id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`bounty_status_id`)
);