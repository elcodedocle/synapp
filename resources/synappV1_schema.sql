/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


CREATE DATABASE IF NOT EXISTS `synapp_db_v1_rev2` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `synapp_db_v1_rev2`;


CREATE TABLE `interface_languages` (
  `native_name` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `iso6392_code` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `users` INT(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `iso6392_code` (`iso6392_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `types` (
  `typeid` INT(11) NOT NULL AUTO_INCREMENT,
  `typedesc` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `users` (
  `user` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `pass` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `recovery` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `firstdate` INT(11) DEFAULT NULL,
  `hfirstdate` BIT(1) NOT NULL DEFAULT b'1',
  `missed_logins` INT(11) DEFAULT '0' NOT NULL,
  `last_login` INT(11) DEFAULT NULL,
  `hlast_login` BIT(1) NOT NULL DEFAULT b'1',
  `ip` INT(10) UNSIGNED DEFAULT NULL,
  `last_update` INT(11) DEFAULT NULL,
  `interface_language` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `hinterface_language` BIT(1) NOT NULL DEFAULT b'1',
  `working_group` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `hworking_group` BIT(1) NOT NULL DEFAULT b'1',
  `input_language` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `hinput_language` BIT(1) NOT NULL DEFAULT b'1',
  `hprofile` BIT(1) NOT NULL DEFAULT b'1',
  `gender` VARCHAR(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hgender` BIT(1) NOT NULL DEFAULT b'1',
  `birthday` DATE DEFAULT NULL,
  `hbirthday` BIT(1) NOT NULL DEFAULT b'1',
  `studies` VARCHAR(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hstudies` BIT(1) NOT NULL DEFAULT b'1',
  `studies_type` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hstudies_type` BIT(1) NOT NULL DEFAULT b'1',
  `studies_level` INT(11) DEFAULT NULL,
  `hstudies_level` BIT(1) NOT NULL DEFAULT b'1',
  `occupation` VARCHAR(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hoccupation` BIT(1) NOT NULL DEFAULT b'1',
  `email` VARCHAR(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hemail` BIT(1) NOT NULL DEFAULT b'1',
  `email_confirmation_code` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmed_email` BIT(1) NOT NULL DEFAULT b'0',
  `avatar` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocaptcha` BIT(1) NOT NULL DEFAULT b'0',
  `hstats` BIT(1) NOT NULL DEFAULT b'1',
  `ditloid_lock_timestamp` INT(11) DEFAULT '0',
  `ditloid_time_left_when_locked` INT(11) DEFAULT '0',
  `gotestbefore` INT(11) DEFAULT '0',
  `gotestafter` INT(11) DEFAULT '0',
  `timer_ctestb_start` INT(11) DEFAULT '0',
  `timer_ctestb_end` INT(11) DEFAULT '0',
  `timer_utestb_start` INT(11) DEFAULT '0',
  `timer_utestb_end` INT(11) DEFAULT '0',
  `timer_utesta_start` INT(11) DEFAULT '0',
  `timer_utesta_end` INT(11) DEFAULT '0',
  `timer_ctesta_start` INT(11) DEFAULT '0',
  `timer_ctesta_end` INT(11) DEFAULT '0',
  `fbid` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` INT(11) NOT NULL DEFAULT '1',
  UNIQUE KEY `user` (`user`),
  KEY `working_group` (`working_group`),
  KEY `interface_language` (`interface_language`),
  KEY `input_language` (`input_language`),
  FOREIGN KEY `fk_usrs_wgrp_grps_name` (`working_group`) REFERENCES `groups` (`name`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_usrs_iflg_ilgs_iso2` (`interface_language`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_usrs_inlg_ilgs_iso2` (`input_language`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `confirmed_emails` (
  `user` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `email` (`email`),
  KEY `user` (`user`),
  FOREIGN KEY `fk_cems_user_usrs_user` (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `image_collections` (
  `collectionid` INT(11) NOT NULL AUTO_INCREMENT,
  `collectionname` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`collectionid`),
  UNIQUE INDEX `collectionname` (`collectionname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE='utf8_unicode_ci';


CREATE TABLE `group_collections` (
  `groupid` INT(11) NULL DEFAULT NULL,
  `collectionid` INT(11) NULL DEFAULT NULL,
  INDEX `fk_gcol_gid_grps_id` (`groupid`),
  INDEX `fk_gcol_cid_icol_cid` (`collectionid`),
  CONSTRAINT `fk_gcol_cid_icol_cid` FOREIGN KEY (`collectionid`) REFERENCES `image_collections` (`collectionid`),
  CONSTRAINT `fk_gcol_gid_grps_id` FOREIGN KEY (`groupid`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE='utf8_unicode_ci';


CREATE OR REPLACE VIEW vgroup_collections AS
  SELECT gcols.groupid, grps.name groupname, gcols.collectionid, icols.collectionname
  FROM group_collections gcols
    LEFT JOIN groups grps ON grps.id = gcols.groupid
    LEFT JOIN image_collections icols ON gcols.collectionid = icols.collectionid;
  

CREATE TABLE `images` (
  `id` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `name` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `collectionid` INT(11) NOT NULL,
  `uploader` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  UNIQUE INDEX `id` (`id`),
  INDEX `fk_imgs_updr_sadm_user` (`uploader`),
  INDEX `fk_imgs_cid_icol_cid` (`collectionid`),
  CONSTRAINT `fk_imgs_cid_icol_cid` FOREIGN KEY (`collectionid`) REFERENCES `image_collections` (`collectionid`) ON UPDATE CASCADE,
  CONSTRAINT `fk_imgs_updr_sadm_user` FOREIGN KEY (`uploader`) REFERENCES `synadmin` (`user`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE OR REPLACE VIEW `vimages` AS
SELECT imgs.`id`, imgs.`name`, imgs.`collectionid`, icols.`collectionname`, imgs.`uploader`
FROM `images` imgs LEFT JOIN `image_collections` icols ON icols.`collectionid` = imgs.`collectionid`;


CREATE TABLE `associations` (
  `associd` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id1` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `id2` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_group` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `user` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `word` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_stamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` INT(11) NOT NULL,
  PRIMARY KEY (`associd`),
  UNIQUE KEY `lang_grp_type_user_ids` (`lang`,`assigned_group`,`type`,`user`,`id1`,`id2`),
  KEY `word` (`word`),
  KEY `time_stamp` (`time_stamp`),
  KEY `id1` (`id1`),
  KEY `id2` (`id2`),
  KEY `assigned_group` (`assigned_group`),
  KEY `lang` (`lang`),
  KEY `user` (`user`),
  KEY `type` (`type`),
  FOREIGN KEY `fk_asss_id1_imgs_id` (`id1`) REFERENCES `images` (`id`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_asss_id2_imgs_id` (`id2`) REFERENCES `images` (`id`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_asss_assg_grps_name` (`assigned_group`) REFERENCES `groups` (`name`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_asss_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_asss_user_usrs_user` (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_asss_type_typs_tyid` (`type`) REFERENCES `types` (`typeid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `evaluations` (
  `associd` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `evaluator` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `vote` INT(11) NOT NULL DEFAULT '0',
  `popvote` INT(11) NOT NULL DEFAULT '0',
  `time_stamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `time_stamp` (`time_stamp`),
  KEY `vote` (`vote`),
  KEY `popvote` (`popvote`),
  KEY `associd` (`associd`),
  KEY `evaluator` (`evaluator`),
  FOREIGN KEY `fk_evas_asid_asss_asid` (`associd`) REFERENCES `associations` (`associd`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_evas_eval_usrs_user` (`evaluator`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE OR REPLACE VIEW `vassociations` AS
SELECT 
  assoc.`associd`,
  assoc.`id1`,
  assoc.`id2`,
  assoc.`assigned_group`,
  assoc.`user`,
  assoc.`lang`,
  assoc.`word`,
  assoc.`time_stamp`,
  assoc.`type`,
  SUM(IF (eval.vote=2,1,0)) AS cohfullvotes, 
  SUM(IF (eval.vote=1,1,0)) AS cohhalfvotes,
  SUM(IF (eval.vote=0,1,0)) AS cohzerovotes, 
  SUM(IF (eval.popvote=2,1,0)) AS orgfullvotes, 
  SUM(IF (eval.popvote=1,1,0)) AS orghalfvotes,
  SUM(IF (eval.popvote=0,1,0)) AS orgzerovotes
FROM associations assoc 
LEFT JOIN evaluations eval ON eval.associd = assoc.associd
GROUP BY assoc.associd;


CREATE OR REPLACE VIEW `vevaluations` AS 
SELECT 
   evas.`associd`
 , evas.`evaluator`
 , evas.`vote`
 , evas.`popvote`
 , evas.`time_stamp` 
 , asss.`id1`
 , asss.`id2`
 , asss.`word`
 , asss.`user`
 , asss.`assigned_group`
 , asss.`lang`
 , asss.`type`
FROM `evaluations` AS evas
LEFT JOIN `associations` AS asss ON evas.associd = asss.associd;


CREATE TABLE `ditloids` (
  `duid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dgid` INT(11) UNSIGNED NOT NULL,
  `did` INT(11) UNSIGNED NOT NULL,
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`duid`),
  UNIQUE KEY `lang_dgid_did_wid` (`lang`,`dgid`,`did`),
  KEY `did` (`did`),
  KEY `lang` (`lang`),
  FOREIGN KEY `fk_dits_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `ditloid_words` (
  `dwuid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `duid` INT(11) UNSIGNED NOT NULL,
  `wid` INT(11) UNSIGNED NOT NULL,
  `pre` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  -- it should almost always be of size 1 (first letter of word)
  `dit` VARCHAR(16) COLLATE utf8_unicode_ci NOT NULL,
  `val` VARCHAR(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`dwuid`),
  KEY `duid` (`duid`),
  FOREIGN KEY `fk_ditws_did_dits_did` (`duid`) REFERENCES `ditloids` (`duid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `ditloid_values` (
  `dwuid` INT(11) UNSIGNED NOT NULL,
  `user` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `val` VARCHAR(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE INDEX `dwuid_user` (`dwuid`, `user`),
  KEY `dwuid` (`dwuid`),
  KEY `user` (`user`),
  FOREIGN KEY `fk_ditvs_dwuid_ditws_dwuid` (`dwuid`) REFERENCES `ditloid_words` (`dwuid`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_ditvs_user_usrs_user` (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `ditloid_results` (
  `duid` INT(11) UNSIGNED NOT NULL,
  `user` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `evaluator` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `time_stamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vote` INT(11) NOT NULL DEFAULT '0',
  `popvote` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`duid`, `user`, `evaluator`),
  KEY `duid` (`duid`),
  KEY `user` (`user`),
  KEY `evaluator` (`evaluator`),
  KEY `time_stamp` (`time_stamp`),
  KEY `vote` (`vote`),
  KEY `popvote` (`popvote`),
  FOREIGN KEY `fk_ditr_duid_dits_duid` (`duid`) REFERENCES `ditloids` (`duid`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_ditr_user_usrs_user` (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_ditr_evtr_usrs_user` (`evaluator`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE OR REPLACE VIEW `vditloid_results` AS
SELECT 
  dits.`duid`,
  dits.`dgid`,
  dits.`did`,
  dits.`lang`,
  dres.`user`,
  dres.`time_stamp`,
  usrs.`working_group`,
  SUM(IF (dres.vote=2,1,0)) AS cohfullvotes, 
  SUM(IF (dres.vote=1,1,0)) AS cohhalfvotes,
  SUM(IF (dres.vote=0,1,0)) AS cohzerovotes, 
  SUM(IF (dres.popvote=2,1,0)) AS orgfullvotes, 
  SUM(IF (dres.popvote=1,1,0)) AS orghalfvotes,
  SUM(IF (dres.popvote=0,1,0)) AS orgzerovotes,
  GROUP_CONCAT(CONCAT(IFNULL(ditws.`pre`,''), IFNULL(ditws.`dit`,''), IFNULL(ditvs.`val`,'.'), IFNULL(ditws.`post`,''))) AS val
FROM `ditloid_results` AS dres
LEFT JOIN `users` AS usrs 
ON dres.user = usrs.user
LEFT JOIN `ditloids` AS dits 
ON dits.duid = dres.duid
LEFT JOIN `ditloid_words` AS ditws 
ON dits.duid = ditws.duid
LEFT JOIN `ditloid_values` AS ditvs 
ON ditvs.dwuid = ditws.dwuid AND ditvs.user = dres.user
GROUP BY dres.`duid`, dres.`user`; -- dgid, did, lang, user


CREATE OR REPLACE VIEW `vditloid_evaluations` AS 
SELECT
  dres.`duid`,
  dits.`dgid`,
  dits.`did`,
  dits.`lang`,
  dres.`user`,
  dres.`evaluator`,
  dres.`time_stamp`,
  dres.`vote`,
  dres.`popvote`,
  IF (dres.vote=2,1,0) AS cohfullvotes, 
  IF (dres.vote=1,1,0) AS cohhalfvotes,
  IF (dres.vote=0,1,0) AS cohzerovotes, 
  IF (dres.popvote=2,1,0) AS orgfullvotes, 
  IF (dres.popvote=1,1,0) AS orghalfvotes,
  IF (dres.popvote=0,1,0) AS orgzerovotes
FROM `ditloid_results` dres
LEFT JOIN `ditloids` dits ON dits.`duid` = dres.`duid`;


CREATE OR REPLACE VIEW `vditloid_words` AS
  SELECT
    dws.duid,
    dws.dwuid,
    dds.did,
    dws.wid,
    dds.lang,
    dws.pre,
    dws.dit,
    dws.val,
    dws.post
  FROM `ditloid_words` dws
    LEFT JOIN `ditloids` dds ON dds.duid = dws.duid;


CREATE OR REPLACE VIEW `vditloid_values` AS
  SELECT
    dvs.`user`,
    dds.`lang`,
    dds.`dgid`,
    dws.`duid`,
    dvs.`dwuid`,
    dds.`did`,
    dws.`wid`,
    dvs.`val`,
    dws.`pre`,
    dws.`dit`,
    dws.`val` `refval`,
    dws.`post`
  FROM `ditloid_values` dvs
    LEFT JOIN `ditloid_words` dws ON dws.`dwuid` = dvs.`dwuid`
    LEFT JOIN `ditloids` dds ON dds.`duid` = dws.`duid`;


CREATE TABLE `test_types` (
  `ttypeid` INT(11) NOT NULL AUTO_INCREMENT,
  `ttypedesc` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`ttypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `test_resources` (
  `tresourceid` INT(11) NOT NULL,
  `tresourcelang` VARCHAR(4) COLLATE 'utf8_unicode_ci' NOT NULL,
  `tresourcedesc` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  PRIMARY KEY (`tresourceid`, `tresourcelang`),
  INDEX `fk_trcs_lang_ilgs_iso2` (`tresourcelang`),
  CONSTRAINT `fk_trcs_lang_ilgs_iso2` FOREIGN KEY (`tresourcelang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `group_resources` (
  `resourceid` INT(11) NOT NULL,
  `groupid` INT(11) NOT NULL,
  `stage` INT(11) NOT NULL,
  INDEX `fk_gres_gid_grps_id` (`groupid`),
  INDEX `fk_gres_rid_tres_trid` (`resourceid`),
  CONSTRAINT `fk_gres_gid_grps_id` FOREIGN KEY (`groupid`) REFERENCES `groups` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_gres_rid_tres_trid` FOREIGN KEY (`resourceid`) REFERENCES `test_resources` (`tresourceid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `test_type_resources` (
  `ttypeid` INT(11) NOT NULL,
  `tresourceid` INT(11) NOT NULL,
  `tresourcelang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ttypeid`, `tresourceid`, `tresourcelang`),
  FOREIGN KEY `fk_ttrs_ttid_ttyp_ttid` (`ttypeid`) REFERENCES `test_types` (`ttypeid`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_ttrs_trid_lang_trcs_trid_lang` (`tresourceid`, `tresourcelang`) REFERENCES `test_resources` (`tresourceid`, `tresourcelang`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_ttrs_lang_ilgs_iso2` (`tresourcelang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE OR REPLACE VIEW vgroup_resources AS
  SELECT
    gres.resourceid, ttres.ttypeid, gres.groupid, grps.name groupname, gres.stage, tres.tresourcelang, tres.tresourcedesc
  FROM group_resources gres
    LEFT JOIN groups grps ON grps.id = gres.groupid
    LEFT JOIN test_resources tres ON gres.resourceid = tres.tresourceid
    LEFT JOIN test_type_resources ttres ON gres.resourceid = ttres.tresourceid AND tres.tresourcelang = ttres.tresourcelang;


CREATE OR REPLACE VIEW vtest_type_resources AS
  SELECT tr.tresourceid, tr.tresourcelang, tr.tresourcedesc, ttr.ttypeid, tt.ttypedesc
  FROM test_resources tr
    LEFT JOIN test_type_resources ttr ON ttr.tresourceid = tr.tresourceid
    LEFT JOIN test_types tt ON tt.ttypeid = ttr.ttypeid;


CREATE OR REPLACE VIEW vgroup_resources_admin AS
  SELECT
    gres.resourceid, grps.id groupid, grps.name groupname, gres.stage, tres.tresourcedesc, tres.tresourcelang, gcols.collectionid, icols.collectionname, ttres.ttypeid
  FROM  groups grps
    LEFT JOIN group_resources gres ON grps.id = gres.groupid
    LEFT JOIN test_resources tres ON tres.tresourceid = gres.resourceid
    LEFT JOIN group_collections gcols ON gcols.groupid = gres.groupid
    LEFT JOIN image_collections icols ON icols.collectionid = gcols.collectionid
    LEFT JOIN test_type_resources ttres ON ttres.tresourceid = gres.resourceid AND ttres.tresourcelang = tres.tresourcelang;


CREATE TABLE `test_timings` (
  `ttypeid` INT(11) NOT NULL,
  `tresourceid` INT(11) NOT NULL,
  `tresourcelang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `user` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `time_given` INT(11) NOT NULL,
  `start_time` TIMESTAMP NOT NULL,
  `end_time` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`ttypeid`, `tresourceid`, `tresourcelang`, `user`),
  KEY `time_given` (`time_given`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  FOREIGN KEY `fk_tts_ttid_ttyp_ttid` (`ttypeid`) REFERENCES `test_types` (`ttypeid`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_tts_trid_lang_trcs_trid_lang` (`tresourceid`, `tresourcelang`) REFERENCES `test_resources` (`tresourceid`, `tresourcelang`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_tts_lang_ilgs_iso2` (`tresourcelang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_tts_user_usrs_user` (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `test_results` (
  `resultid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `ttypeid` INT(11) NOT NULL,
  `tresourceid` INT(11) NULL DEFAULT NULL,
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `val` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `firstedit` INT(11) NOT NULL DEFAULT '0',
  `lastedit` INT(11) NOT NULL DEFAULT '0',
  `orgfullvotes` INT(11) NOT NULL DEFAULT '0',
  `orghalfvotes` INT(11) NOT NULL DEFAULT '0',
  `orgzerovotes` INT(11) NOT NULL DEFAULT '0',
  `cohfullvotes` INT(11) NOT NULL DEFAULT '0',
  `cohhalfvotes` INT(11) NOT NULL DEFAULT '0',
  `cohzerovotes` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`resultid`),
  UNIQUE INDEX `lg_usr_ttyid_trid_val` (`lang`, `user`, `ttypeid`, `tresourceid`, `val`),
  KEY `orgfullvotes` (`orgfullvotes`),
  KEY `orghalfvotes` (`orghalfvotes`),
  KEY `orgzerovotes` (`orgzerovotes`),
  KEY `cohfullvotes` (`cohfullvotes`),
  KEY `cohhalfvotes` (`cohhalfvotes`),
  KEY `cohzerovotes` (`cohzerovotes`),
  KEY `user` (`user`),
  KEY `ttypeid` (`ttypeid`),
  KEY `tresourceid` (`tresourceid`),
  KEY `lang` (`lang`),
  FOREIGN KEY `fk_tres_user_usrs_user` (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_tres_ttyp_typs_tyid` (`ttypeid`) REFERENCES `test_types` (`ttypeid`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_tres_trid_trcs_trid` (`tresourceid`) REFERENCES `test_resources` (`tresourceid`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_tres_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `test_evaluations` (
  `user` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `ttypeid` INT(11) NOT NULL,
  `tresourceid` INT(11) NULL DEFAULT NULL,
  `evaluator` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `orgfullvotes` INT(11) NOT NULL DEFAULT '0',
  `orghalfvotes` INT(11) NOT NULL DEFAULT '0',
  `orgzerovotes` INT(11) NOT NULL DEFAULT '0',
  `cohfullvotes` INT(11) NOT NULL DEFAULT '0',
  `cohhalfvotes` INT(11) NOT NULL DEFAULT '0',
  `cohzerovotes` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang`, `evaluator`, `ttypeid`, `tresourceid`, `user`),
    KEY `orgfullvotes` (`orgfullvotes`),
    KEY `orghalfvotes` (`orghalfvotes`),
    KEY `orgzerovotes` (`orgzerovotes`),
    KEY `cohfullvotes` (`cohfullvotes`),
    KEY `cohhalfvotes` (`cohhalfvotes`),
    KEY `cohzerovotes` (`cohzerovotes`),
    KEY `user` (`user`),
    KEY `ttypeid` (`ttypeid`),
    KEY `tresourceid` (`tresourceid`),
    KEY `evaluator` (`evaluator`),
    KEY `lang` (`lang`),
    FOREIGN KEY `fk_tevs_user_usrs_user` (`user`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY `fk_tevs_ttyp_typs_tyid` (`ttypeid`) REFERENCES `test_types` (`ttypeid`) ON UPDATE CASCADE,
    FOREIGN KEY `fk_tevs_trid_trcs_trid` (`tresourceid`) REFERENCES `test_resources` (`tresourceid`) ON UPDATE CASCADE,
    FOREIGN KEY `fk_tevs_evtr_usrs_user` (`evaluator`) REFERENCES `users` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY `fk_tevs_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE OR REPLACE VIEW `vtest_ranks` AS
SELECT 
  `user`,
  `ttypeid`,
  `tresourceid`,
  `lang`,
  (SUM(`orgfullvotes`) + SUM(`orghalfvotes`) + SUM(`orgzerovotes`) + SUM(`cohfullvotes`) + SUM(`cohhalfvotes`) + SUM(`cohzerovotes`)) AS `evals`,
  SUM(`orgfullvotes`),
  SUM(`orghalfvotes`),
  SUM(`orgzerovotes`),
  SUM(`cohfullvotes`),
  SUM(`cohhalfvotes`),
  SUM(`cohzerovotes`)
FROM `test_evaluations`
GROUP BY `user`, `ttypeid`, `lang`, `tresourceid`;


-- ******************************************************************************************
-- ******************************************************************************************
-- **************************** ADMINISTRATIVE SECTIONS MODEL *******************************
-- * (DYNAMIC ADMIN-EDITABLE CONTENT, TASKS, RESOURCES AND USERS MANAGEMENT AND STATISTICS) *
-- ******************************************************************************************
-- ******************************************************************************************


CREATE TABLE `synadmin` (
  `uuid` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `user` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `password` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE INDEX `user` (`user`),
  KEY `email` (`email`),
  KEY `lang` (`lang`),
  FOREIGN KEY `fk_sadm_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `synadmin_session` (
  `sessionid` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `uuid` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  PRIMARY KEY (`sessionid`),
  KEY `fk_sses_uuid` (`uuid`),
  FOREIGN KEY `fk_sses_uuid_sadm_uuid` (`uuid`) REFERENCES `synadmin` (`uuid`)
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `synadmin_session_data` (
  `sessionid` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `property_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `property_value` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  KEY `property_name` (`property_name`),
  KEY `property_value` (`property_value`),
  KEY `sessionid` (`sessionid`),
  FOREIGN KEY `fk_ssed_seid_ssessynapp_db_v1_rev2synapp_db_v1_rev2_seid` (`sessionid`) REFERENCES `synadmin_session` (`sessionid`)
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `synadmin_session_log` (
  `sessionid` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `ip` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `firstseen` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastseen` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sessionid`)
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `dynamic_content_categories` (
  `categoryid` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `parentcategoryid` INT(11) DEFAULT NULL,
  `weight` INT(11) NOT NULL,
  KEY `weight` (`weight`),
  FOREIGN KEY `fk_dcc_pcid_dcc_caid` (`parentcategoryid`) REFERENCES `dynamic_content_categories` (`categoryid`) ON DELETE CASCADE ON UPDATE CASCADE
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `dynamic_content_categories_data` (
  `categoryid` INT(11) NOT NULL, 
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `categoryname` VARCHAR(255) NOT NULL,
  UNIQUE INDEX `categoryid_lang` (`categoryid`, `lang`),
  KEY `categoryid` (`categoryid`),
  KEY `lang` (`lang`),
  FOREIGN KEY `fk_dccd_caid_dcc_caid` (`categoryid`) REFERENCES `dynamic_content_categories` (`categoryid`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_dccd_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `dynamic_content` (
  `contentid` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `categoryid` INT(11) NOT NULL,
  `weight` INT(11) NOT NULL,
  KEY `categoryid` (`categoryid`),
  KEY `weight` (`weight`),
  FOREIGN KEY `fk_dco_caid_dcc_caid` (`categoryid`) REFERENCES `dynamic_content_categories` (`categoryid`) ON DELETE CASCADE ON UPDATE CASCADE
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `dynamic_content_data` (
  `contentid` INT(11) NOT NULL, 
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `contentname` TEXT, 
  `contentvalue` TEXT, 
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdby` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  `lastmodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastmodifiedby` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`contentid`, `lang`),
  KEY `contentid` (`contentid`),
  KEY `lang` (`lang`),
  KEY `created` (`created`),
  KEY `createdby` (`createdby`),
  KEY `lastmodified` (`lastmodified`),
  KEY `lastmodifiedby` (`lastmodifiedby`),
  FOREIGN KEY `fk_dco_coid_dcc_caid` (`contentid`) REFERENCES `dynamic_content` (`contentid`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_dco_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_dco_crby_dcc_user` (`createdby`) REFERENCES `synadmin` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_dco_moby_dcc_user` (`lastmodifiedby`) REFERENCES `synadmin` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


CREATE TABLE `dynamic_content_data_history` (
  `contentid` INT(11) NOT NULL, 
  `lang` VARCHAR(4) COLLATE utf8_unicode_ci NOT NULL,
  `contentname` TEXT, 
  `contentvalue` TEXT, 
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifiedby` VARCHAR(255) COLLATE 'utf8_unicode_ci' NOT NULL,
  KEY `contentid` (`contentid`),
  KEY `lang` (`lang`),
  KEY `modified` (`modified`),
  KEY `modifiedby` (`modifiedby`),
  FOREIGN KEY `fk_dch_coid_dcc_caid` (`contentid`) REFERENCES `dynamic_content` (`contentid`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY `fk_dch_lang_ilgs_iso2` (`lang`) REFERENCES `interface_languages` (`iso6392_code`) ON UPDATE CASCADE,
  FOREIGN KEY `fk_dch_moby_dcc_user` (`modifiedby`) REFERENCES `synadmin` (`user`) ON DELETE CASCADE ON UPDATE CASCADE
)
COLLATE='utf8_unicode_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB
;


DROP TRIGGER IF EXISTS `dynamic_content_log`;

CREATE TRIGGER `dynamic_content_log` 
BEFORE UPDATE ON `dynamic_content_data`
FOR EACH ROW
# noinspection SqlConstantCondition
  INSERT INTO `dynamic_content_data_history` SELECT 
    `contentid`, 
    `lang`,
    `contentname`, 
    `contentvalue`, 
    `lastmodified` AS `modified`,
    `lastmodifiedby` AS `modifiedby`
  FROM `dynamic_content_data` 
  WHERE `contentid` = NEW.`contentid`;


CREATE OR REPLACE VIEW `vdynamic_content` AS
  SELECT
    dco.`contentid`,
    dco.`categoryid`,
    dco.`weight`,
    dccd.`categoryname`,
    dcc.`weight` AS `categoryweight`,
    dcc.`parentcategoryid`,
    pdcc.`categoryname` `parentcategoryname`,
    pdc.`weight` AS `parentcategoryweight`,
    dcd.`lang`,
    dcd.`contentname`,
    dcd.`contentvalue`,
    dcd.`created`,
    dcd.`createdby`,
    dcd.`lastmodified`,
    dcd.`lastmodifiedby`
  FROM `dynamic_content` dco
    INNER JOIN `dynamic_content_categories` dcc ON dcc.`categoryid` = dco.`categoryid`
    INNER JOIN `dynamic_content_categories` pdc ON pdc.`categoryid` = dcc.`parentcategoryid`
    INNER JOIN `dynamic_content_categories_data` dccd ON dccd.`categoryid` = dco.`categoryid`
    INNER JOIN `dynamic_content_categories_data` pdcc ON pdcc.`categoryid` = dcc.`parentcategoryid` AND pdcc.`lang` = dccd.`lang`
    INNER JOIN `dynamic_content_data` dcd ON dcd.`contentid` = dco.`contentid` AND dcd.`lang` = dccd.`lang`;


CREATE OR REPLACE VIEW vuser_admin_stats_eva AS
  SELECT
    evaluator
    , COUNT(vote) evacount
    , ROUND(5*SUM(vote)/IFNULL(NULLIF(COUNT(vote),0),1),2) avgvoteeva
    , ROUND(5*SUM(popvote)/IFNULL(NULLIF(COUNT(popvote),0),1),2) avgpopvoteeva
    , ROUND((5*SUM(vote)/IFNULL(NULLIF(COUNT(vote),0),1)
             + 5*SUM(popvote)/IFNULL(NULLIF(COUNT(popvote),0),1))/2,2) avgeva
  FROM evaluations
  GROUP BY evaluator;


CREATE OR REPLACE VIEW vuser_admin_stats_ass AS
  SELECT
    user
    , IFNULL(SUM(cohfullvotes),0)+IFNULL(SUM(cohhalfvotes),0)+IFNULL(SUM(cohhalfvotes),0) asscount
    , ROUND(10*IFNULL(
      IFNULL(SUM(cohfullvotes),0)+IFNULL(SUM(cohhalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(cohfullvotes),0)+IFNULL(SUM(cohhalfvotes),0)+IFNULL(SUM(cohhalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgcohass
    , ROUND(10*IFNULL(
      IFNULL(SUM(orgfullvotes),0)+IFNULL(SUM(orghalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(orgfullvotes),0)+IFNULL(SUM(orghalfvotes),0)+IFNULL(SUM(orghalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgorgass
    , ROUND((
              10*IFNULL(
                  IFNULL(SUM(cohfullvotes),0)+IFNULL(SUM(cohhalfvotes),0)*0.5
                  ,0
              )
              /
              IFNULL(
                  NULLIF(
                      IFNULL(SUM(cohfullvotes),0)+IFNULL(SUM(cohhalfvotes),0)+IFNULL(SUM(cohhalfvotes),0)
                      ,0
                  )
                  ,1
              )
              + 10*IFNULL(
                  IFNULL(SUM(orgfullvotes),0)+IFNULL(SUM(orghalfvotes),0)*0.5
                  ,0
              )
                /
                IFNULL(
                    NULLIF(
                        IFNULL(SUM(orgfullvotes),0)+IFNULL(SUM(orghalfvotes),0)+IFNULL(SUM(orghalfvotes),0)
                        ,0
                    )
                    ,1
                )

            )/2, 2) avgass
  FROM vassociations
  GROUP BY user;


CREATE OR REPLACE VIEW vuser_admin_stats_ctest AS
  SELECT
    veva.user
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgcohctest
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgorgctest
    , ROUND((
              10*IFNULL(
                  IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
                  ,0
              )
              /
              IFNULL(
                  NULLIF(
                      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                      ,0
                  )
                  ,1
              )
              + 10*IFNULL(
                  IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
                  ,0
              )
                /
                IFNULL(
                    NULLIF(
                        IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                        ,0
                    )
                    ,1
                )

            )/2, 2) avgctest
  FROM test_evaluations veva
  WHERE veva.ttypeid = 1
  GROUP BY veva.user;


CREATE OR REPLACE VIEW vuser_admin_stats_utest AS
  SELECT
    veva.user
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgcohutest
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgorgutest
    , ROUND((
              10*IFNULL(
                  IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
                  ,0
              )
              /
              IFNULL(
                  NULLIF(
                      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                      ,0
                  )
                  ,1
              )
              + 10*IFNULL(
                  IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
                  ,0
              )
                /
                IFNULL(
                    NULLIF(
                        IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                        ,0
                    )
                    ,1
                )

            )/2, 2) avgutest
  FROM test_evaluations veva
  WHERE veva.ttypeid = 2
  GROUP BY veva.user;


CREATE OR REPLACE VIEW vuser_admin_stats_dtest AS
  SELECT
    veva.user
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgcohdtest
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgorgdtest
    , ROUND((
              10*IFNULL(
                  IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
                  ,0
              )
              /
              IFNULL(
                  NULLIF(
                      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                      ,0
                  )
                  ,1
              )
              + 10*IFNULL(
                  IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
                  ,0
              )
                /
                IFNULL(
                    NULLIF(
                        IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                        ,0
                    )
                    ,1
                )

            )/2, 2) avgdtest
  FROM vditloid_evaluations veva
  GROUP BY veva.user;


CREATE OR REPLACE VIEW vuser_admin_stats_cteste AS
  SELECT
    veva.evaluator
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgcohcteste
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgorgcteste
    , ROUND((
              10*IFNULL(
                  IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
                  ,0
              )
              /
              IFNULL(
                  NULLIF(
                      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                      ,0
                  )
                  ,1
              )
              + 10*IFNULL(
                  IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
                  ,0
              )
                /
                IFNULL(
                    NULLIF(
                        IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                        ,0
                    )
                    ,1
                )

            )/2, 2) avgcteste
  FROM test_evaluations veva
  WHERE veva.ttypeid = 1
  GROUP BY veva.evaluator;


CREATE OR REPLACE VIEW vuser_admin_stats_uteste AS
  SELECT
    veva.evaluator
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgcohuteste
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgorguteste
    , ROUND((
              10*IFNULL(
                  IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
                  ,0
              )
              /
              IFNULL(
                  NULLIF(
                      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                      ,0
                  )
                  ,1
              )
              + 10*IFNULL(
                  IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
                  ,0
              )
                /
                IFNULL(
                    NULLIF(
                        IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                        ,0
                    )
                    ,1
                )

            )/2, 2) avguteste
  FROM test_evaluations veva
  WHERE veva.ttypeid = 2
  GROUP BY veva.evaluator;


CREATE OR REPLACE VIEW vuser_admin_stats_dteste AS
  SELECT
    veva.evaluator
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgcohdteste
    , ROUND(10*IFNULL(
      IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
      ,0
  )
            /
            IFNULL(
                NULLIF(
                    IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                    ,0
                )
                ,1
            )
  , 2
      ) avgorgdteste
    , ROUND((
              10*IFNULL(
                  IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)*0.5
                  ,0
              )
              /
              IFNULL(
                  NULLIF(
                      IFNULL(SUM(veva.cohfullvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)+IFNULL(SUM(veva.cohhalfvotes),0)
                      ,0
                  )
                  ,1
              )
              + 10*IFNULL(
                  IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)*0.5
                  ,0
              )
                /
                IFNULL(
                    NULLIF(
                        IFNULL(SUM(veva.orgfullvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)+IFNULL(SUM(veva.orghalfvotes),0)
                        ,0
                    )
                    ,1
                )

            )/2, 2) avgdteste
  FROM vditloid_evaluations veva
  GROUP BY veva.evaluator;


-- -------------------------------------------------------
--  THIS ONE REALLY, REALLY SHOULD BE A MATERIALIZED VIEW
-- -------------------------------------------------------
CREATE OR REPLACE VIEW vuser_admin_stats AS
SELECT
  usrs.user
  , usrs.email
  , usrs.confirmed_email
  , usrs.working_group
  , FROM_UNIXTIME(usrs.firstdate) first_login
  , FROM_UNIXTIME(usrs.last_login) last_login
  , usrs.interface_language
  , TIMESTAMPDIFF(YEAR,usrs.birthday,CURDATE()) age
  , usrs.gender
  , usrs.occupation
  , usrs.studies
  , usrs.active
  , IF(usrs.ditloid_lock_timestamp = 0 OR usrs.ditloid_lock_timestamp IS NULL, NULL, DATE_SUB(FROM_UNIXTIME(usrs.ditloid_lock_timestamp), INTERVAL 30 * 60 - usrs.ditloid_time_left_when_locked SECOND)) timer_dtest_start
  , IF(usrs.ditloid_lock_timestamp = 0 OR usrs.ditloid_lock_timestamp IS NULL, NULL, 30 * 60 - usrs.ditloid_time_left_when_locked) dtesttime
  , IF(usrs.timer_ctestb_start = 0 OR usrs.timer_ctestb_start IS NULL, NULL, FROM_UNIXTIME(usrs.timer_ctestb_start)) timer_ctestb_start
  , IF(usrs.timer_ctestb_start = 0 OR usrs.timer_ctestb_start IS NULL OR usrs.timer_ctestb_end = 0 OR usrs.timer_ctestb_end IS NULL, NULL, TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(usrs.timer_ctestb_start),FROM_UNIXTIME(usrs.timer_ctestb_end))) ctestbtime
  , IF(usrs.timer_ctesta_start = 0 OR usrs.timer_ctesta_start IS NULL, NULL, FROM_UNIXTIME(usrs.timer_ctesta_start)) timer_ctesta_start
  , IF(usrs.timer_ctesta_start = 0 OR usrs.timer_ctesta_start IS NULL OR usrs.timer_ctesta_end = 0 OR usrs.timer_ctesta_end IS NULL, NULL, TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(usrs.timer_ctesta_start),FROM_UNIXTIME(usrs.timer_ctesta_end))) ctestatime
  , IF(usrs.timer_utestb_start = 0 OR usrs.timer_utestb_start IS NULL, NULL, FROM_UNIXTIME(usrs.timer_utestb_start)) timer_utestb_start
  , IF(usrs.timer_utestb_start = 0 OR usrs.timer_utestb_start IS NULL OR usrs.timer_utestb_end = 0 OR usrs.timer_utestb_end IS NULL, NULL, TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(usrs.timer_utestb_start),FROM_UNIXTIME(usrs.timer_utestb_end))) utestbtime
  , IF(usrs.timer_utesta_start = 0 OR usrs.timer_utesta_start IS NULL, NULL, FROM_UNIXTIME(usrs.timer_utesta_start)) timer_utesta_start
  , IF(usrs.timer_utesta_start = 0 OR usrs.timer_utesta_start IS NULL OR usrs.timer_utesta_start = 0 OR usrs.timer_utesta_start IS NULL, NULL, TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(usrs.timer_utesta_start),FROM_UNIXTIME(usrs.timer_utesta_end))) utestatime
  , vas.asscount, vas.avgcohass, vas.avgorgass, vas.avgass
  , eva.evacount, eva.avgvoteeva, eva.avgpopvoteeva, eva.avgeva
  , vtrt1.avgcohctest, vtrt1.avgorgctest, vtrt1.avgctest
  , vtrt2.avgcohutest, vtrt2.avgorgutest, vtrt2.avgutest
  , vtrd.avgcohdtest, vtrd.avgorgdtest, vtrd.avgdtest
  , vtrt1e.avgcohcteste, vtrt1e.avgorgcteste, vtrt1e.avgcteste
  , vtrt2e.avgcohuteste, vtrt2e.avgorguteste, vtrt2e.avguteste
  , vtrde.avgcohdteste, vtrde.avgorgdteste, vtrde.avgdteste
FROM users usrs
  LEFT JOIN vuser_admin_stats_ass vas ON usrs.user = vas.user
  LEFT JOIN vuser_admin_stats_eva eva ON usrs.user = eva.evaluator
  LEFT JOIN vuser_admin_stats_ctest vtrt1 ON usrs.user = vtrt1.user
  LEFT JOIN vuser_admin_stats_utest vtrt2 ON usrs.user = vtrt2.user
  LEFT JOIN vuser_admin_stats_dtest vtrd ON usrs.user = vtrd.user
  LEFT JOIN vuser_admin_stats_cteste vtrt1e ON usrs.user = vtrt1e.evaluator
  LEFT JOIN vuser_admin_stats_uteste vtrt2e ON usrs.user = vtrt2e.evaluator
  LEFT JOIN vuser_admin_stats_dteste vtrde ON usrs.user = vtrde.evaluator;



-- **************************************************************************
-- **************************************************************************
-- ************************* BUNDLED APPLICATION DATA ***********************
-- **************************************************************************
-- **************************************************************************


-- 2 rows (aprox)
INSERT INTO `interface_languages` (`native_name`, `iso6392_code`, `users`) VALUES
  ('español', 'spa', 0),
  ('english', 'eng', 0);


-- 2 rows (aprox)
INSERT INTO `groups` (`id`, `name`) VALUES
  (1, 'DEFAULT_GROUP_A'),
  (2, 'DEFAULT_GROUP_B');


-- adds default admin user. 
-- (Bundled default admin managed data will be associated to this user)
INSERT INTO `synadmin` (`uuid`, `user`, `password`, `email`, `lang`) VALUES
-- this user has no login until a password is established using the provided php script 
  (UUID(), 'defaultadminuser', 'nologin', 'noemail@example.com', 'eng');


INSERT INTO `dynamic_content_categories` (categoryid, parentcategoryid, weight) VALUES
-- DEFAULT CONTENT CATEGORY (ROOT PARENT CATEGORY)
  (1,null,10),
-- FAQ CONTENT CATEGORY
  (2,1,20),
-- FAQ CONTENT CATEGORY - ABOUT SYNAPP
  (3,2,30),
-- FAQ CONTENT CATEGORY - REGISTRATION AND ACCESS
  (4,2,40),
-- FAQ CONTENT CATEGORY - USE OF THE APPLICATION
  (5,2,50),
-- FAQ CONTENT CATEGORY - PRIVACY POLICY
  (6,2,60),
-- FAQ CONTENT CATEGORY - OTHER DOUBTS AND PROBLEMS
  (7,2,70);

INSERT INTO `dynamic_content_categories_data` (categoryid, lang, categoryname) VALUES
-- DEFAULT CONTENT CATEGORY INFO
  (1, 'spa', 'synapp'),
  (1, 'eng', 'synapp'),
-- FAQ CONTENT CATEGORY INFO
  (2, 'spa', 'FAQ'),
  (2, 'eng', 'FAQ'),
-- FAQ CONTENT CATEGORY INFO - ABOUT SYNAPP
  (3, 'spa', 'SOBRE SYNAPP'),
  (3, 'eng', 'ABOUT SYNAPP'),
-- FAQ CONTENT CATEGORY INFO - REGISTRATION AND ACCESS
  (4, 'spa', 'REGISTRO Y ACCESO'),
  (4, 'eng', 'REGISTRATION AND ACCESS'),
-- FAQ CONTENT CATEGORY INFO - USE OF THE APPLICATION
  (5, 'spa', 'USO DE LA APLICACIÓN'),
  (5, 'eng', 'USE OF THE APPLICATION'),
-- FAQ CONTENT CATEGORY INFO - PRIVACY POLICY
  (6, 'spa', 'POLÍTICA DE PRIVACIDAD'),
  (6, 'eng', 'PRIVACY POLICY'),
-- FAQ CONTENT CATEGORY INFO - OTHER DOUBTS AND PROBLEMS
  (7, 'spa', 'OTRAS DUDAS Y PROBLEMAS'),
  (7, 'eng', 'OTHER DOUBTS AND PROBLEMS');

-- FAQ CONTENT
INSERT INTO `dynamic_content` (contentid, categoryid, weight) VALUES
  (1,2,10),
  (2,3,20),
  (3,3,30),
  (4,4,40),
  (5,4,50),
  (6,4,60),
  (7,5,70),
  (8,5,80),
  (9,5,90),
  (10,5,100),
  (11,5,110),
  (12,5,120),
  (13,5,130),
  (14,5,140),
  (15,5,150),
  (16,5,160),
  (17,5,170),
  (18,6,180),
  (19,7,190);

-- FAQ CONTENT DATA
INSERT INTO `dynamic_content_data` (contentid, lang, contentname, contentvalue, createdby, lastmodifiedby) VALUES
  (1, 'eng',
   'User Guide &amp; Frequently Asked Questions',
   'Description, user manual, terms of use and most frequently asked questions about SynAPP',
   'defaultadminuser', 'defaultadminuser'),
  (1, 'spa',
   'Gu&iacute;a de usuario y preguntas m&aacute;s frecuentes',
   'Descripci&oacute;n, instrucciones y preguntas m&aacute;s frecuentes sobre la aplicaci&oacute;n',
   'defaultadminuser', 'defaultadminuser'),
  (2, 'eng',
   'What is SynAPP?',
   '<p class="answer">SynAPP is an online experiment on collaborative computation and development of creativity.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (2, 'spa',
   '&iquest;Qu&eacute; es SynAPP?',
   '<p class="answer">SynAPP es un experimento online sobre creatividad, estudio de modelos del proceso creativo y crowd computing.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (3, 'eng',
   'What is the purpose of SynAPP? / How does SynAPP work?',
   '<p class="answer">The purpose of SynAPP is to enable its users to explore and try to improve their
                        creative skills by:</p>

                    <p class="list">-Associating random pairs of images with a word or short phrase.</p>

                    <p class="list">-Evaluating (voting) other user''s associations.</p>

                    <p class="list">-Getting feedback of their progress from other user''s evaluations and a few standard
                        creativity tests.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (3, 'spa',
   '&iquest;Cual es el objetivo del experimento SynAPP?',
   '<p class="answer">Permitir a sus participantes explorar y posiblemente mejorar su capacidad creativa mediante:</p>

                    <p class="list">-La asociaci&oacute;n de parejas de im&aacute;genes con palabras o frases cortas.</p>

                    <p class="list">-La evaluaci&oacute;n de asociaciones realizadas por otros participantes.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (4, 'eng',
   'How do I Register?',
   '<p class="answer">Go to link: REG. FORM. and fill the form as requested.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (4, 'spa',
   '&iquest;C&oacute;mo me Registro?',
   '<p class="answer">Ir al siguiente enlace: FORMULARIO DE REGISTRO.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (5, 'eng',
   'How do I log into the application?',
   '<p class="answer">From the home page, introducing your username and password. If you don''t have an
                        username and password
                        read right above how to register to get them. If you forgot any of them read right below how to
                        recover access to
                        your account.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (5, 'spa',
   '&iquest;C&oacute;mo entro en la aplicaci&oacute;n?',
   '<p class="answer">Accediendo a la p&aacute;gina de inicio e introduciendo el nombre de usuario y contrase&ntilde;a elegidos, previo registro.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (6, 'eng',
   'I forgot my user and/or password. How can I recover them?',
   '<p class="answer">If you provided a valid email address after registration, go here and follow the
                        steps: RECOVER
                        USER/PASSWORD.</p>

                    <p class="list">-If you have any problem during this process, please contact the administrator using
                        the contact form
                        provided at the bottom of this page.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (6, 'spa',
   'He olvidado mi nombre de usuario y/o contrase&ntilde;a. &iquest;Qu&eacute; puedo hacer?',
   '<p class="answer">Si indic&oacute; una direcci&oacute;n de email en su perfil tras registrarse, vaya al siguiente enlace y siga las instrucciones:
    RECUPERAR USUARIO/CONTRASE&ntilde;A.</p>

                    <p class="list">-Si tiene cualquier problema con este proceso, contacte con el administrador.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (7, 'eng',
   'Menu bar',
   '<p>Once you''ve logged in, an unfolding menu bar shows permantly on the top of every page of the
                        application, allowing
                        the user to navigate through its different sections.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (7, 'spa',
   'La barra de menu',
   '<p>Una vez realizado el acceso a la aplicaci&oacute;n con su nombre de usuario y contrase&ntilde;a se mostrar&aacute; en todo momento en la
    parte superior de la p&aacute;gina una barra de men&uacute; con los enlaces requeridos para navegar por la aplicaci&oacute;n y acceder a
    sus distintas secciones, decritas a continuaci&oacute;n.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (8, 'eng',
   'The tests',
   '<p>When a new user registers or has been using the application for a while, he/she is requested to
                        do 3 standard
                        creativity tests.</p>

                    <p class="nlist">-The first test is a word equation puzzle: E.g., "24 = H. in O. D." is properly
                        solved as "24 = Hours in
                        One Day". Different equations are used on the tests performed before and after using the
                        application.</p>

                    <p class="nlist">-In the second test the user is asked to give as many uses as possible to an
                        object. Two different
                        objects are used: One for the test performed before, and the other for the test performed after
                        using SynAPP.</p>

                    <p class="nlist">-The third test is similar to the second, but the user is asked to find objects
                        with a common property
                        instead.</p>

                    <p>Each answer coming from these tests is evaluated by the rest of the users in a similar way as the
                        image-image
                        associations.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (8, 'spa',
   'El test',
   '<p>Cuando un usuario se registra o ha participado durante un tiempo en el experimento, se le solicita
    completar hasta 3 test para evaluar su capacidad creativa. El acceso al resto de la aplicaci&oacute;n quedar&aacute; bloqueado 
    mientras no se haya realizado el test, para el cual se proporciona un tiempo m&aacute;ximo de 15 o 30 minutos desde que el usuario
    decida comenzarlo.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (9, 'eng',
   'Why can''t I access the tests?',
   '<p class="answer">Tests can only be accessed either if you have just started using the application
                        or you have been using
                        it long enough. If you''re in either of this situations and still can''t take any of the 3 tests,
                        please contact the
                        administrator using the contact form below.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (9, 'spa',
   '&iquest;Por qu&eacute; no puedo acceder al los test?',
   '<p class="answer">El test s&oacute;lo es accesible una vez ,al comenzar a usar la aplicaci&oacute;n o tras un tiempo de uso de la misma, y durante un
    m&aacute;ximo de 30 minutos desde su comienzo. Hasta entonces o despu&eacute;s de entonces estar&aacute; bloqueado.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (10, 'eng',
   'Why can''t I evaluate other users'' tests?',
   '<p class="answer">Users can only evaluate other users'' tests after taking them themselves. If you
                        have taken a test and
                        still can''t evaluate, please contact the administrator using the contact form below.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (10, 'spa',
   '',
   '',
   'defaultadminuser', 'defaultadminuser'),
  (11, 'eng',
   'The application',
   '<p>SynAPP can be used in 2 ways: Association (simultaneous or sequential) and Evaluation.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (11, 'spa',
   'La aplicaci&oacute;n',
   '<p>SynAPP tiene 2 modos de uso: Asociaci&oacute;n y Evaluaci&oacute;n. El modo de Asociaci&oacute;n tiene 2 submodos: Secuencial y Simult&aacute;nea.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (12, 'eng',
   'Sequential Associations',
   '<p>2 images are presented sequentially, one 5 seconds after the other. The participant relates both
                        by typing a word or
                        short phrase.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (12, 'spa',
   'Asociar: Modo Secuencial',
   '<p>Se muestran al participante dos im&aacute;genes secuencialmente: Primero una y tras unos segundos otra. El participante debe
    tratar de relacionar ambas de una forma coherente y original, describir esa asociaci&oacute;n en el campo indicado y
    enviarla para su evaluaci&oacute;n.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (13, 'eng',
   'Simultaneous Associations',
   '<p>2 images are presented simultaneously, one on the left and another one on the right side of the
                        page. The participant
                        relates both by typing a word or short phrase.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (13, 'spa',
   'Asociar: Modo Simult&aacute;neo',
   '<p>Se muestran al participante dos im&aacute;genes simult&aacute;neamente. El participante debe tratar de relacionar ambas de una
    forma coherente y original, describir esa asociaci&oacute;n en el campo indicado y enviarla para su evaluaci&oacute;n.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (14, 'eng',
   'Evaluations',
   '<p>Associations by other users are presented to be given 0, 0.5 or 1 originality points and 0, 0.5
                        or 1 coherence points
                        according to the user''s criteria.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (14, 'spa',
   'Evaluar',
   '<p>Se muestra al participante la asociaci&oacute;n realizada por otro participante y se pide que la eval&uacute;e en funci&oacute;n de su
    originalidad y coherencia.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (15, 'eng',
   'Why can''t I access the stats or evaluations page?',
   '<p class="answer">Every user is requested to provide a valid email address to activate this
                        function, which you can edit
                        on the profile page (menu: my account-&gt;profile).</p>',
   'defaultadminuser', 'defaultadminuser'),
  (15, 'spa',
   '&iquest;Por qu&eacute; no puedo acceder a la p&aacute;gina de estad&iacute;sticas o evaluaciones?',
   '<p>Se solicita proporcionar una direcci&oacute;n de email v&aacute;lida para activar estas funciones. La secci&oacute;n sobre el perfil en
    esta p&aacute;gina le explica c&oacute;mo acceder a su perfil para hacerlo.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (16, 'eng',
   'Profile',
   '',
   'defaultadminuser', 'defaultadminuser'),
  (16, 'spa',
   'Perfil',
   '',
   'defaultadminuser', 'defaultadminuser'),
  (17, 'eng',
   'How can I change my profile settings, password, shared info, etc?',
   '<p class="answer">You can edit your profile in the profile page.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (17, 'spa',
   '&iquest;Como puedo proporcionar una direcci&oacute;n de email v&aacute;lida?',
   '<p>Seleccionar el enlace "usuario" en el men&uacute; superior, donde "usuario" es el nombre de usuario elegido. Se mostrar&aacute; su
    p&aacute;gina de perfil, donde podr&aacute; introducir una direcci&oacute;n de email que deber&aacute; confirmar siguiendo el enlace en el email
    de confirmaci&oacute;n enviado a la direcci&oacute;n proporcionada.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (18, 'eng',
   '',
   '<p class="answer">Any input to SynAPP, including profile data, is shared by default between users for statistics and evaluation purposes. Users are encouraged to visit their profile settings page, where they can find and configure privacy settings related to this data sharing. We do not take responsablity on what users may do with the info shared by any user of SynAPP. Since the application is still in a beta development stage we also cannot guarantee the privacy of any information further than to our best effort (still above many popular Internet sites'' average...).</p>',
   'defaultadminuser', 'defaultadminuser'),
  (18, 'spa',
   '&iquest;Cu&aacute;l es la politica de privacidad? &iquest;Por qu&eacute; debo proporcionar mi direcci&oacute;n de correo electr&oacute;nico?',
   '<p class="answer">Todos los datos recopilados en este experimento ser&aacute;n tratados an&oacute;nimamente y de acuerdo con la legislaci&oacute;n vigente.
    Cada participante podr&aacute; elegir qu&eacute; datos revelar al resto de participantes y no se revelar&aacute;n datos personales de
    ning&uacute;n participante si su consentimiento expreso, ni ser&aacute;n empleados con otro prop&oacute;sito que el estudio en curso.</p>

<p class="answer">Se requiere proporcionar una direcci&oacute;n v&aacute;lida de correo electr&oacute;nico para:</p>
    <p class="list">-Dificultar el uso inapropiado de la aplicaci&oacute;n y obtener datos m&aacute;s fiables.</p>
    <p class="list">-Recuperar el acceso a la cuenta en caso de que el participante no disponga del nombre de usuario o password
    empleados en el registro.</p>
    <p class="list">-Informar a los usuarios, en caso de que as&iacute; lo deseen, sobre actualizaciones y cambios en la aplicaci&oacute;n.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (19, 'eng',
   '',
   '<p>SynAPP is a beta project under development:</p>

                    <p class="nlist">-If you find any errors or flaws or have any doubt about its use, please contact
                        the administrator.</p>

                    <p class="nlist">-We need your opinion to improve this application: EVERY comment will be
                        appreciated.</p>',
   'defaultadminuser', 'defaultadminuser'),
  (19, 'spa',
   '',
   '<p>SynAPP es un proyecto en desarrollo:</p>

                    <p class="nlist">-Si encuentra alg&uacute;n error o tiene cualquier duda sobre su uso, por favor comuniqueselo al administrador.</p>

                    <p class="nlist">-Ay&uacute;denos a mejorarla envi&aacute;ndonos su opini&oacute;n: Todo comentario ser&aacute; apreciado.</p>',
   'defaultadminuser', 'defaultadminuser');


-- 2 rows (aprox)
INSERT INTO `image_collections` (`collectionid`, `collectionname`) VALUES
  (1,'DEFAULT_GROUP_A'),
  (2,'DEFAULT_GROUP_B');


-- 2 rows (aprox)
INSERT INTO `group_collections` (`collectionid`, `groupid`) VALUES
  (1,1),
  (2,2);


-- 30 rows (aprox)
INSERT INTO `images` (`id`, `name`, `collectionid`, `uploader`) VALUES
  ('../../vendor/synappv1/default-task-resources/images/test10A.jpg', 'default group A test image 10', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test10B.jpg', 'default group B test image 10', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test11A.jpg', 'default group A test image 11', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test11B.jpg', 'default group B test image 11', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test12A.jpg', 'default group A test image 12', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test12B.jpg', 'default group B test image 12', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test13A.jpg', 'default group A test image 13', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test13B.jpg', 'default group B test image 13', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test14A.jpg', 'default group A test image 14', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test14B.jpg', 'default group B test image 14', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test15A.jpg', 'default group A test image 15', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test15B.jpg', 'default group B test image 15', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test1A.jpg', 'default group A test image 1', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test1B.jpg', 'default group B test image 1', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test2A.jpg', 'default group A test image 2', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test2B.jpg', 'default group B test image 2', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test3A.jpg', 'default group A test image 3', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test3B.jpg', 'default group B test image 3', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test4A.jpg', 'default group A test image 4', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test4B.jpg', 'default group B test image 4', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test5A.jpg', 'default group A test image 5', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test5B.jpg', 'default group B test image 5', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test6A.jpg', 'default group A test image 6', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test6B.jpg', 'default group B test image 6', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test7A.jpg', 'default group A test image 7', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test7B.jpg', 'default group B test image 7', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test8A.jpg', 'default group A test image 8', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test8B.jpg', 'default group B test image 8', 2, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test9A.jpg', 'default group A test image 9', 1, 'defaultadminuser'),
  ('../../vendor/synappv1/default-task-resources/images/test9B.jpg', 'default group B test image 9', 2, 'defaultadminuser');


-- 44 rows (aprox)
INSERT INTO `ditloids` (`duid`, `dgid`, `did`, `lang`) VALUES
  (1, 0, 1, 'eng'),
  (2, 0, 2,'eng'),
  (3, 0, 3, 'eng'),
  (4, 0, 4, 'eng'),
  (5, 0, 5, 'eng'),
  (6, 0, 6, 'eng'),
  (7, 0, 7, 'eng'),
  (8, 0, 8, 'eng'),
  (9, 0, 9, 'eng'),
  (10, 0, 10, 'eng'),
  (11, 0, 11, 'eng'),
  (12, 0, 12, 'eng'),
  (13, 0, 13, 'eng'),
  (14, 0, 14, 'eng'),
  (15, 0, 15, 'eng'),
  (16, 0, 16, 'eng'),
  (17, 0, 17, 'eng'),
  (18, 0, 18, 'eng'),
  (19, 0, 19, 'eng'),
  (20, 0, 20, 'eng'),
  (21, 0, 21, 'eng'),
  (22, 0, 22, 'eng'),
  (23, 0, 1, 'spa'),
  (24, 0, 2, 'spa'),
  (25, 0, 3, 'spa'),
  (26, 0, 4, 'spa'),
  (27, 0, 5, 'spa'),
  (28, 0, 6, 'spa'),
  (29, 0, 7, 'spa'),
  (30, 0, 8, 'spa'),
  (31, 0, 9, 'spa'),
  (32, 0, 10, 'spa'),
  (33, 0, 11, 'spa'),
  (34, 0, 12, 'spa'),
  (35, 0, 13, 'spa'),
  (36, 0, 14, 'spa'),
  (37, 0, 15, 'spa'),
  (38, 0, 16, 'spa'),
  (39, 0, 17, 'spa'),
  (40, 0, 18, 'spa'),
  (41, 0, 19, 'spa'),
  (42, 0, 20, 'spa'),
  (43, 0, 21, 'spa'),
  (44, 0, 22, 'spa');


-- 67 rows (aprox)
INSERT INTO `ditloid_words` (`duid`, `wid`, `pre`, `dit`, `val`, `post`) VALUES
  (1, 1, '8 = ', 'B', 'lack', ''),
  (2, 1, '5 = ', 'O', 'ne', ''),
  (3, 1, '24 = a ', 'D', 'ozen', ''),
  (4, 1, '1 = ', 'S', 'un', ' on'),
  (5, 1, '6 = ', 'G', 'uitar', ''),
  (6, 1, '2 = ', 'C', 'olours', ' on'),
  (7, 1, '0 = ', 'C', 'elsius', ''),
  (8, 1, '2 = ', 'G', 'oals', ' on'),
  (9, 1, '', 'H', 'ypotenuse', ''),
  (10, 1, '300 = ', 'S', 'oldiers', ''),
  (11, 1, '3 = ', 'S', 'ides', ' on'),
  (12, 1, '33 = ', 'E', 'leven', ''),
  (13, 1, '4 = ', 'Q', 'uarters', ' in'),
  (14, 1, '1 = ', 'R', 'ails', ' on'),
  (15, 1, '53 = ', 'W', 'eeks', ' plus'),
  (16, 1, '4 = ', 'D', 'igits', ' in'),
  (17, 1, '2012 = ', 'E', 'nd', ' of'),
  (18, 1, '5 = ', 'P', 'layers', ' on'),
  (19, 1, '1 in ', 'H', 'and', ' >'),
  (20, 1, '11 = ', 'M', 'onths', ''),
  (21, 1, '64 = ', 'S', 'ixth', ''),
  (22, 1, '', 'A', 'pple', ' ='),
  (22, 2, ' ', 'E', 've', '\'s'),
  (1, 2, ' ', 'B', 'all', '\'s'),
  (2, 2, ' ', 'N', 'umber', ''),
  (3, 2, ' ', 'C', 'ouples', ''),
  (4, 2, ' the ', 'S', 'olar', ''),
  (5, 2, ' ', 'S', 'trings', ''),
  (6, 2, ' the ', 'J', 'apanese', ''),
  (7, 3, ' which ', 'W', 'ater', ''),
  (8, 2, ' a ', 'F', 'ootball', ''),
  (9, 4, ' the ', 'S', 'um', ' of'),
  (10, 2, ' in ', 'T', 'hermopylae', ''),
  (11, 2, ' a ', 'C', 'aution', ''),
  (12, 2, ' ', 'T', 'imes', ''),
  (13, 2, ' a ', 'W', 'hole', ''),
  (16, 3, ' ', 'C', 'ard', ''),
  (15, 3, ' ', 'D', 'ay', ' in'),
  (16, 4, ' ', 'P', 'in', ''),
  (17, 2, ' the ', 'M', 'ayan', ''),
  (18, 2, ' a ', 'B', 'asketball', ''),
  (19, 2, ' 100 ', 'F', 'lying', ''),
  (20, 2, ' ', 'W', 'ith', ''),
  (21, 2, ' ', 'P', 'ower', ' of'),
  (1, 3, ' ', 'N', 'umber', ''),
  (1, 4, ' in ', 'S', 'nooker', ''),
  (4, 3, ' ', 'S', 'ystem', ''),
  (6, 3, ' ', 'F', 'lag', ''),
  (7, 4, ' ', 'F', 'reezes', ''),
  (7, 2, ' ', 'D', 'egrees', ' at '),
  (8, 3, ' ', 'F', 'ield', ''),
  (9, 5, ' the ', 'S', 'quared', ''),
  (9, 6, ' ', 'C', 'atheti', ''),
  (9, 3, ' ', 'R', 'oot', ' of'),
  (11, 3, ' ', 'S', 'ign', ''),
  (12, 3, ' ', 'T', 'hree', ''),
  (15, 4, ' a ', 'Y', 'ear', ''),
  (15, 2, ' ', 'O', 'ne', ''),
  (14, 2, ' a ', 'M', 'onorail', ''),
  (16, 2, ' a ', 'C', 'redit', ''),
  (17, 3, ' ', 'C', 'alendar', ''),
  (18, 3, ' ', 'T', 'eam', ''),
  (19, 3, ' (', 'B', 'irds', ')'),
  (20, 3, ' 30 ', 'D', 'ays', ''),
  (21, 3, ' ', 'T', 'wo', ''),
  (22, 3, ' ', 'T', 'emptation', ''),
  (9, 2, ' = ', 'S', 'quared', '');


-- 67 rows (aprox)
INSERT INTO `ditloid_words` (`duid`, `wid`, `pre`, `dit`, `val`, `post`) VALUES
  (23, 1, '8 = ', 'N', 'umero', ' de la'),
  (24, 1, '5 = ', 'U', 'n', ''),
  (25, 1, '24 = una ', 'D', 'ozena', ' de'),
  (26, 1, '1 = ', 'S', 'ol', ' en'),
  (27, 1, '6 = ', 'C', 'uerdas', ' en una'),
  (28, 1, '2 = ', 'C', 'olores', ' en'),
  (29, 1, '0 = ', 'G', 'rados', ''),
  (30, 1, '2 = ', 'P', 'orterias', ' en '),
  (31, 1, 'La ', 'H', 'ipotenusa', ''),
  (32, 1, '300 = ', 'E', 'spartanos', ' en'),
  (33, 1, '3 = ', 'L', 'ados', ' en'),
  (34, 1, '33 = ', 'O', 'nce', ''),
  (35, 1, '4 = ', 'C', 'uartos', ' en'),
  (36, 1, '1 = ', 'R', 'ail', ' en'),
  (37, 1, '53 = ', 'S', 'emanas', ' y '),
  (38, 1, '4 = ', 'D', 'igitos', ' en '),
  (39, 1, '2012 = ', 'F', 'in', ' del'),
  (40, 1, '5 = ', 'J', 'ugadores', ' en'),
  (41, 1, '1 en ', 'M', 'ano', ' >'),
  (42, 1, '11 = ', 'M', 'eses', ''),
  (43, 1, '64 = ', 'S', 'exta', ''),
  (44, 1, '', 'M', 'anzana', ' = la'),
  (44, 2, ' ', 'T', 'entacion', ' de'),
  (23, 2, ' ', 'B', 'ola', ''),
  (24, 2, ' ', 'N', 'umero', ''),
  (25, 2, ' ', 'P', 'arejas', ''),
  (26, 2, ' el ', 'S', 'istema', ''),
  (27, 2, ' ', 'G', 'uitarra', ''),
  (28, 2, ' la ', 'B', 'andera', ''),
  (29, 3, ' que el ', 'A', 'gua', ' se'),
  (30, 2, ' un ', 'C', 'ampo', ' de'),
  (31, 4, ' la ', 'S', 'uma', ' de'),
  (32, 2, ' las ', 'T', 'ermopilas', ''),
  (33, 2, ' una ', 'S', 'eñal', ' de'),
  (34, 2, ' ', 'V', 'eces', ''),
  (35, 2, ' un ', 'T', 'odo', ''),
  (38, 3, ' ', 'T', 'arjeta', ' de'),
  (37, 3, ' ', 'D', 'ia', ' en '),
  (38, 4, ' ', 'C', 'redito', ''),
  (39, 2, ' the ', 'C', 'alendario', ''),
  (40, 2, ' un ', 'E', 'quipo', ' de'),
  (41, 2, ' 100 ', 'V', 'olando', ''),
  (42, 2, ' ', 'C', 'on', ''),
  (43, 2, ' ', 'P', 'otencia', ' de'),
  (23, 3, ' ', 'N', 'egra', ''),
  (23, 4, ' en ', 'S', 'nooker', ''),
  (26, 3, ' ', 'S', 'olar', ''),
  (28, 3, ' ', 'E', 'spañola', ''),
  (29, 4, ' ', 'C', 'ongela', ''),
  (29, 2, ' ', 'C', 'elsius', ' a los '),
  (30, 3, ' ', 'F', 'utbol', ''),
  (31, 5, ' los ', 'C', 'atetos', ' al'),
  (31, 6, ' ', 'C', 'uadrado', ''),
  (31, 3, ' ', 'C', 'uadrada', ' de'),
  (33, 3, ' ', 'P', 'eligro', ''),
  (34, 3, ' ', 'T', 'res', ''),
  (37, 4, ' un ', 'A', 'ño', ''),
  (37, 2, ' ', 'U', 'n', ''),
  (36, 2, ' un ', 'M', 'onorail', ''),
  (38, 2, ' un ', 'P', 'in', ' de una'),
  (39, 3, ' ', 'M', 'aya', ''),
  (40, 3, ' ', 'B', 'aloncesto', ''),
  (41, 3, ' (', 'P', 'ajaros', ')'),
  (42, 3, ' 30 ', 'D', 'ias', ''),
  (43, 3, ' ', 'D', 'os', ''),
  (44, 3, ' ', 'E', 'va', ''),
  (31, 2, ' = ', 'R', 'aiz', '');


-- 2 rows (aprox)
INSERT INTO `types` (`typeid`, `typedesc`) VALUES
  (1, 'watrix'),
  (2, 'watrixflash');
    
    
-- 2 rows (aprox)
INSERT INTO `test_types` (`ttypeid`, `ttypedesc`) VALUES
  (1, 'ctest'),
  (2, 'utest');
  

-- 16 rows (aprox)
INSERT INTO `test_resources` (`tresourceid`, `tresourcelang`, `tresourcedesc`) VALUES
  (1, 'eng', 'wheels'),
  (1, 'spa', 'ruedas'),
  (2, 'eng', 'holes'),
  (2, 'spa', 'agujeros'),
  (3, 'eng', 'buttons'),
  (3, 'spa', 'botones'),
  (4, 'eng', 'handles'),
  (4, 'spa', 'mangos'),
  (5, 'eng', 'a clip'),
  (5, 'spa', 'un clip'),
  (6, 'eng', 'a wristband'),
  (6, 'spa', 'una muñequera'),
  (7, 'eng', 'a spring'),
  (7, 'spa', 'un muelle'),
  (8, 'eng', 'a sock'),
  (8, 'spa', 'un calcetin');


-- 8 rows (aprox)
INSERT INTO `group_resources` (`groupid`, `resourceid`, `stage`) VALUES
  (1,1,2),
  (2,2,2),
  (1,3,1),
  (2,4,1),
  (1,5,2),
  (2,6,2),
  (1,7,1),
  (2,8,1);


-- 16 rows (aprox)
INSERT INTO `test_type_resources` (`ttypeid`, `tresourceid`, `tresourcelang`) VALUES
  (1, 1, 'eng'),
  (1, 1, 'spa'),
  (1, 2, 'eng'),
  (1, 2, 'spa'),
  (1, 3, 'eng'),
  (1, 3, 'spa'),
  (1, 4, 'eng'),
  (1, 4, 'spa'),
  (2, 5, 'eng'),
  (2, 5, 'spa'),
  (2, 6, 'eng'),
  (2, 6, 'spa'),
  (2, 7, 'eng'),
  (2, 7, 'spa'),
  (2, 8, 'eng'),
  (2, 8, 'spa');


/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
