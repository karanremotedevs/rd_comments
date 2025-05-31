CREATE TABLE tx_rdcomments_domain_model_comment (
	
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	comment int(11) unsigned DEFAULT '0' NOT NULL,
	newsuid int(11) DEFAULT '0' NOT NULL,
	username varchar(255) DEFAULT '' NOT NULL,
	usermail varchar(255) DEFAULT '' NOT NULL,
	paramlink varchar(255) DEFAULT '' NOT NULL,
	description text,
	pinned TINYINT(1) DEFAULT 0 NOT NULL,
	childcomment int(11) unsigned DEFAULT '0' NOT NULL,
	terms int(11) unsigned DEFAULT '0' NOT NULL,
    likes int(11) DEFAULT '0',

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_rdcomments_domain_model_commentlike (
    uid INT(11) NOT NULL AUTO_INCREMENT,
    pid INT(11) DEFAULT 0 NOT NULL,
    comment_uid INT(11) NOT NULL,
    ip_address VARCHAR(255) NOT NULL,
    crdate INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (uid),
);
