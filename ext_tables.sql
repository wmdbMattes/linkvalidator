
# ---------------------
# tx_linkvalidator_link
# ---------------------
#
# fields for record:
#  - record_uid   : uid of record where link exists
#  - record_pid
#  - headline
#  - field
#  - table_name
#
# - link_title     : anchor text
# - url_response   : serialized array containing errorType and error message
# - link_type      : external, page, file or any of the custom link types
#
# NEW
# - record_sys_language_uid : we need language of record
# - record_path : path of record (rootline)
# - record_type : e.g. textmedia (for tt_content + ctype='textmedia'), plugin (for ctype='list')
# - record_sitename
#

CREATE TABLE tx_linkvalidator_link (
  uid                       int(11) NOT NULL auto_increment,
  record_uid                int(11) DEFAULT '0' NOT NULL,
  record_pid                int(11) DEFAULT '0' NOT NULL,
  record_sys_language_uid   int(11) DEFAULT '0' NOT NULL,
  record_path               varchar(255) DEFAULT '' NOT NULL,
  record_sitename           varchar(255) DEFAULT '' NOT NULL,
  record_type               varchar(255) DEFAULT '' NOT NULL,
  headline                  varchar(255) DEFAULT '' NOT NULL,
  field                     varchar(255) DEFAULT '' NOT NULL,
  table_name                varchar(255) DEFAULT '' NOT NULL,
  link_title                text,
  url                       text,
  url_response              text,
  last_check                int(11) DEFAULT '0' NOT NULL,
  link_type                 varchar(50) DEFAULT '' NOT NULL,

  PRIMARY KEY (uid),

  KEY record_uid (record_uid),
  KEY record_pid (record_pid)
);


CREATE TABLE tx_linkvalidator_check (
  uid                     int(11) NOT NULL auto_increment,
  starttime               int(11) unsigned DEFAULT '0' NOT NULL,
  endtime                 int(11) unsigned DEFAULT '0' NOT NULL,
  number_of_links         int(11) unsigned DEFAULT '0' NOT NULL,
  number_of_broken_links  int(11) unsigned DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid)
);


# -----------------------------------
# tx_linkvalidator_exclude_from_check
# -----------------------------------
# Exclude some urls from check
#

CREATE TABLE tx_linkvalidator_exclude_from_check (
  uid           int(11) NOT NULL auto_increment,
  url           varchar(255) DEFAULT '' NOT NULL,
  domain        varchar(255) DEFAULT '' NOT NULL,

  KEY domain (domain),

  PRIMARY KEY (uid)
);


CREATE TABLE pages (
  donotchecklinks tinyint(1) DEFAULT '0' NOT NULL
);