CREATE TABLE `tl_article` (
  `af_enable` char(1) NOT NULL default '',
  `af_criteria` text NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_af_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `published` char(1) NOT NULL default '',
  `template` varchar(255) NOT NULL default '',
  `sortindex` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_af_criteria` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `published` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_module` (
  `af_groups` text NULL
  `af_defaultfilter` varchar(32) NOT NULL default '',
  `af_sorting` varchar(32) NOT NULL default '',
  `af_showfilter` char(1) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;