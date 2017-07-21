<?php
/**
 * WebEngine CMS
 * https://webenginecms.org/
 * 
 * @version 2.0.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2017 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

define('_TBL_MI_', 'MEMB_INFO');
	define('_CLMN_USERNM_', 'memb___id');
	define('_CLMN_PASSWD_', 'memb__pwd');
	define('_CLMN_MEMBID_', 'memb_guid');
	define('_CLMN_EMAIL_', 'mail_addr');
	define('_CLMN_CREDITS_', 'cspoints');
	define('_CLMN_CREDITS_TEMP_', 'TempCredits');
	define('_CLMN_BLOCCODE_', 'bloc_code');
	define('_CLMN_VIP_STAMP_', 'VipStamp');
	define('_CLMN_MASTER_KEY_', 'MasterKey');
	define('_CLMN_SNONUMBER_', 'sno__numb');
	define('_CLMN_MEMBNAME_', 'memb_name');
	define('_CLMN_CTLCODE_', 'ctl1_code');

define('_TBL_MS_', 'MEMB_STAT');
	define('_CLMN_CONNSTAT_', 'ConnectStat');
	define('_CLMN_MS_MEMBID_', 'memb___id');
	define('_CLMN_MS_GS_', 'ServerName');
	define('_CLMN_MS_IP_', 'IP');
	
define('_TBL_AC_', 'AccountCharacter');
	define('_CLMN_AC_ID_', 'Id');
	define('_CLMN_GAMEID_1_', 'GameID1');
	define('_CLMN_GAMEID_2_', 'GameID2');
	define('_CLMN_GAMEID_3_', 'GameID3');
	define('_CLMN_GAMEID_4_', 'GameID4');
	define('_CLMN_GAMEID_5_', 'GameID5');
	define('_CLMN_GAMEIDC_', 'GameIDC');
	
define('_TBL_CHR_', 'Character');
	define('_CLMN_CHR_NAME_', 'Name');
	define('_CLMN_CHR_ACCID_', 'AccountID');
	define('_CLMN_CHR_CLASS_', 'Class');
	define('_CLMN_CHR_ZEN_', 'Money');
	define('_CLMN_CHR_LVL_', 'cLevel');
	define('_CLMN_CHR_RSTS_', 'Resets');
	define('_CLMN_CHR_LVLUP_POINT_', 'LevelUpPoint');
	define('_CLMN_CHR_STAT_STR_', 'Strength');
	define('_CLMN_CHR_STAT_AGI_', 'Dexterity');
	define('_CLMN_CHR_STAT_VIT_', 'Vitality');
	define('_CLMN_CHR_STAT_ENE_', 'Energy');
	define('_CLMN_CHR_STAT_CMD_', 'Leadership');
	define('_CLMN_CHR_PK_KILLS_', 'CirePkCount');
	define('_CLMN_CHR_PK_LEVEL_', 'PkLevel');
	define('_CLMN_CHR_PK_TIME_', 'PkTime');
	define('_CLMN_CHR_MAP_', 'MapNumber');
	define('_CLMN_CHR_MAP_X_', 'MapPosX');
	define('_CLMN_CHR_MAP_Y_', 'MapPosY');
	define('_CLMN_CHR_MAGIC_L_', 'MagicList');
	define('_CLMN_CHR_GRSTS_', 'Grand_Resets');
	define('_CLMN_CHR_PVPLS_WIN_', 'PvPLastStandWin');
	define('_CLMN_CHR_GENS_CONT_', 'GensContribution');
	define('_CLMN_CHR_GENS_TYPE_', 'GensType');
	define('_CLMN_CHR_GENS_RANK_', 'GensRank');
	define('_CLMN_CHR_GENS_KNIGHT_', 'GensKnight');
	
define('_TBL_LOGEX_', 'MuLogEx');
	define('_CLMN_LOGEX_ACCID_', 'AccountId');
	define('_CLMN_LOGEX_IP_', 'IP');
	define('_CLMN_LOGEX_TIN_', 'TimeIn');
	define('_CLMN_LOGEX_TOUT_', 'TimeOut');
	define('_CLMN_LOGEX_OD_', 'OnlineDuration');
	define('_CLMN_LOGEX_SRV_', 'Server');
	
define('_TBL_MASTERLVL_', 'T_MasterLevelSystem');
	define('_CLMN_ML_NAME_', 'CHAR_NAME');
	define('_CLMN_ML_LVL_', 'MASTER_LEVEL');
	define('_CLMN_ML_EXP_', 'ML_EXP');
	define('_CLMN_ML_NEXP_', 'ML_NEXTEXP');
	define('_CLMN_ML_POINT_', 'ML_POINT');
	
define('_TBL_MC_', 'MEMB_CREDITS');
	define('_CLMN_MC_ID_', 'memb___id');
	define('_CLMN_MC_CREDITS_', 'Credits');
	define('_CLMN_MC_TEMPCREDITS_', 'TempCredits');

define('_TBL_MUCASTLE_DATA_', 'MuCastle_DATA');
	define('_CLMN_MCD_GUILD_OWNER_', 'OWNER_GUILD');
	define('_CLMN_MCD_MONEY_', 'MONEY');
	define('_CLMN_MCD_TRC_', 'TAX_RATE_CHAOS');
	define('_CLMN_MCD_TRS_', 'TAX_RATE_STORE');
	define('_CLMN_MCD_THZ_', 'TAX_HUNT_ZONE');
	
define('_TBL_GUILD_', 'Guild');
	define('_CLMN_GUILD_NAME_', 'G_Name');
	define('_CLMN_GUILD_LOGO_', 'G_Mark');
	define('_CLMN_GUILD_SCORE_', 'G_Score');
	define('_CLMN_GUILD_MASTER_', 'G_Master');
	define('_CLMN_GUILD_NOTICE_', 'G_Notice');
	define('_CLMN_GUILD_UNION_', 'G_Union');
	
define('_TBL_GUILDMEMB_', 'GuildMember');
	define('_CLMN_GUILDMEMB_CHAR_', 'Name');
	define('_CLMN_GUILDMEMB_NAME_', 'G_Name');
	define('_CLMN_GUILDMEMB_LEVEL_', 'G_Level');
	define('_CLMN_GUILDMEMB_STATUS_', 'G_Status');
	
define('_TBL_MUCASTLE_RS_', 'MuCastle_REG_SIEGE');
	define('_CLMN_MCRS_GUILD_', 'REG_SIEGE_GUILD');
	
/*
 * custom: character_class
 */
$custom['character_class'] = array(
	0 => array('Dark Wizard', 'DW', 'dw.jpg', 'base_stats' => array('str' => 18, 'agi' => 18, 'vit' => 15, 'ene' => 30, 'cmd' => 0)),
	1 => array('Soul Master', 'SM', 'dw.jpg', 'base_stats' => array('str' => 18, 'agi' => 18, 'vit' => 15, 'ene' => 30, 'cmd' => 0)),
	3 => array('Grand Master', 'GM', 'dw.jpg', 'base_stats' => array('str' => 18, 'agi' => 18, 'vit' => 15, 'ene' => 30, 'cmd' => 0)),
	16 => array('Dark Knight', 'DK', 'dk.jpg', 'base_stats' => array('str' => 28, 'agi' => 20, 'vit' => 25, 'ene' => 10, 'cmd' => 0)),
	17 => array('Blade Knight', 'BK', 'dk.jpg', 'base_stats' => array('str' => 28, 'agi' => 20, 'vit' => 25, 'ene' => 10, 'cmd' => 0)),
	19 => array('Blade Master', 'BM', 'dk.jpg', 'base_stats' => array('str' => 28, 'agi' => 20, 'vit' => 25, 'ene' => 10, 'cmd' => 0)),
	32 => array('Elf', 'ELF', 'elf.jpg', 'base_stats' => array('str' => 22, 'agi' => 25, 'vit' => 15, 'ene' => 20, 'cmd' => 0)),
	33 => array('Muse Elf', 'ELF', 'elf.jpg', 'base_stats' => array('str' => 22, 'agi' => 25, 'vit' => 15, 'ene' => 20, 'cmd' => 0)),
	35 => array('High Elf', 'ELF', 'elf.jpg', 'base_stats' => array('str' => 22, 'agi' => 25, 'vit' => 15, 'ene' => 20, 'cmd' => 0)),
	48 => array('Magic Gladiator', 'MG', 'mg.jpg', 'base_stats' => array('str' => 26, 'agi' => 26, 'vit' => 26, 'ene' => 16, 'cmd' => 0)),
	50 => array('Duel Master', 'DM', 'mg.jpg', 'base_stats' => array('str' => 26, 'agi' => 26, 'vit' => 26, 'ene' => 16, 'cmd' => 0)),
	64 => array('Dark Lord', 'DL', 'dl.jpg', 'base_stats' => array('str' => 26, 'agi' => 20, 'vit' => 20, 'ene' => 15, 'cmd' => 25)),
	66 => array('Lord Emperor', 'LE', 'dl.jpg', 'base_stats' => array('str' => 26, 'agi' => 20, 'vit' => 20, 'ene' => 15, 'cmd' => 25)),
	80 => array('Summoner', 'SUM', 'sum.jpg', 'base_stats' => array('str' => 21, 'agi' => 21, 'vit' => 18, 'ene' => 23, 'cmd' => 0)),
	81 => array('Bloody Summoner', 'SUM', 'sum.jpg', 'base_stats' => array('str' => 21, 'agi' => 21, 'vit' => 18, 'ene' => 23, 'cmd' => 0)),
	83 => array('Dimension Master', 'SUM', 'sum.jpg', 'base_stats' => array('str' => 21, 'agi' => 21, 'vit' => 18, 'ene' => 23, 'cmd' => 0)),
	96 => array('Rage Fighter', 'RF', 'rf.jpg', 'base_stats' => array('str' => 32, 'agi' => 27, 'vit' => 25, 'ene' => 20, 'cmd' => 0)),
	98 => array('Fist Master', 'FM', 'rf.jpg', 'base_stats' => array('str' => 32, 'agi' => 27, 'vit' => 25, 'ene' => 20, 'cmd' => 0)),
	112 => array('Grow Lancer', 'GL', 'gl.jpg', 'base_stats' => array('str' => 30, 'agi' => 30, 'vit' => 25, 'ene' => 24, 'cmd' => 0)),
	114 => array('Mirage Lancer', 'ML', 'gl.jpg', 'base_stats' => array('str' => 30, 'agi' => 30, 'vit' => 25, 'ene' => 24, 'cmd' => 0)),
);

/*
 * custom: character_cmd
 * classes who use cmd stat
 */
$custom['character_cmd'] = array(64, 66);

/*
 * custom: gens_ranks
 */
$custom['gens_ranks'] = array(
	0 => 'None',
	1 => 'Grand Duke',
	2 => 'Duke',
	3 => 'Marquis',
	4 => 'Count',
	5 => 'Viscount',
	6 => 'Summoner',
	7 => 'Baron',
	8 => 'Knight Commander',
	9 => 'Superior Knight',
	10 => 'Knight',
	11 => 'Officer',
	12 => 'Lieutenant',
	13 => 'Sergeant',
	14 => 'Private',
);

/*
 * custom: map_list
 */
$custom['map_list'] = array(
	0 => 'Lorencia',
	1 => 'Dungeon',
	2 => 'Devias',
	3 => 'Noria',
	4 => 'LostTower',
	5 => 'Exile',
	6 => 'Arena',
	7 => 'Atlans',
	8 => 'Tarkan',
	9 => 'Devil Square',
	10 => 'Icarus',
	11 => 'Blood Castle 1',
	12 => 'Blood Castle 2',
	13 => 'Blood Castle 3',
	14 => 'Blood Castle 4',
	15 => 'Blood Castle 5',
	16 => 'Blood Castle 6',
	17 => 'Blood Castle 7',
	18 => 'Chaos Castle 1',
	19 => 'Chaos Castle 2',
	20 => 'Chaos Castle 3',
	21 => 'Chaos Castle 4',
	22 => 'Chaos Castle 5',
	23 => 'Chaos Castle 6',
	24 => 'Kalima 1',
	25 => 'Kalima 2',
	26 => 'Kalima 3',
	27 => 'Kalima 4',
	28 => 'Kalima 5',
	29 => 'Kalima 6',
	36 => 'Kalima 7',
	30 => 'Valley of Loren',
	31 => 'Land of Trials',
	32 => 'Devil Square',
	33 => 'Aida',
	34 => 'Crywolf Fortress',
	37 => 'Kanturu',
	38 => 'Kanturu 2',
	39 => 'Kanturu 3',
	40 => 'Silent Map',
	41 => 'Barracks of Balgass',
	42 => 'Balgass Refuge',
	45 => 'Illusion Temple 1',
	46 => 'Illusion Temple 2',
	47 => 'Illusion Temple 3',
	48 => 'Illusion Temple 4',
	49 => 'Illusion Temple 5',
	50 => 'Illusion Temple 6',
	51 => 'Elbeland',
	52 => 'Blood Castle 8',
	53 => 'Chaos Castle 7',
	56 => 'Swamp of Calmness',
	57 => 'Raklion',
	58 => 'Raklion Boss',
	62 => 'Village\'s Santa',
	63 => 'Vulcanus',
	64 => 'Duel Arena',
	65 => 'Doppelganger',
	66 => 'Doppelganger',
	67 => 'Doppelganger',
	68 => 'Doppelganger',
	69 => 'Doppelganger',
	69 => 'Empire Fortress',
	70 => 'Empire Fortress',
	71 => 'Empire Fortress',
	72 => 'Empire Fortress',
);

/*
 * custom: pk_level
 */
$custom['pk_level'] = array(
	0 => 'Normal',
	1 => 'Hero',
	2 => 'Hero',
	3 => 'Commoner',
	4 => 'Warning',
	5 => 'Murder',
	6 => 'Outlaw',
);