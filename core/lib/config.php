<?php

if(!defined('PLX_ROOT')) {
	header('Content-Type: text/plain; charset=utf-8');
	exit('Unknown PLX_ROOT constant');
}

const PHP_VERSION_MIN = '5.6.34';
const PLX_DEBUG = true;
const PLX_VERSION = '5.8.9';
const PLX_VERSION_DATA = '5.8.1';
const PLX_URL_REPO = 'https://www.pluxml.org';
const PLX_URL_VERSION = PLX_URL_REPO.'/download/latest-version.txt';

const EMAIL_METHODS = array(
	'sendmail' => 'sendmail',
	'smtp' => 'SMTP',
	'smtpoauth' => 'OAUTH2',
);

# Gestion des erreurs PHP
if(PLX_DEBUG) error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

# Fonction qui retourne le timestamp UNIX actuel avec les microsecondes
function getMicrotime() {
	$t = explode(' ',microtime());
	return $t[0]+$t[1];
}

# Initialisation du timer d'execution
define('PLX_MICROTIME', getMicrotime());

include PLX_ROOT . 'config.php';

$CONSTS = array(
	'XMLFILE_PARAMETERS'	=> PLX_ROOT . PLX_CONFIG_PATH . 'parametres.xml',
	'XMLFILE_CATEGORIES'	=> PLX_ROOT . PLX_CONFIG_PATH . 'categories.xml',
	'XMLFILE_STATICS'		=> PLX_ROOT . PLX_CONFIG_PATH . 'statiques.xml',
	'XMLFILE_USERS'			=> PLX_ROOT . PLX_CONFIG_PATH . 'users.xml',
	'XMLFILE_PLUGINS'		=> PLX_ROOT . PLX_CONFIG_PATH . 'plugins.xml',
	'XMLFILE_TAGS'			=> PLX_ROOT . PLX_CONFIG_PATH . 'tags.xml',
);

# Définition de l'encodage => PLX_CHARSET : UTF-8 (conseillé) ou ISO-8859-1
const PLX_CHARSET = 'UTF-8';
const XML_HEADER = '<?xml version="1.0" encoding="' . PLX_CHARSET .'"?>' . PHP_EOL;

# Langue par défaut
const DEFAULT_LANG = 'en';

# profils utilisateurs de pluxml
const PROFIL_ADMIN		= 0;
const PROFIL_MANAGER	= 1;
const PROFIL_MODERATOR	= 2;
const PROFIL_EDITOR		= 3;
const PROFIL_WRITER		= 4;
const PROFIL_SUBSCRIBER	= 5;

const SESSION_LIFETIME = 7200;

const DEFAULT_CONFIG = array(
	'title'					=> 'PluXml',
	'description'			=> '', # plxUtils::strRevCheck(L_SITE_DESCRIPTION)
	'meta_description'		=> '',
	'meta_keywords'			=> '',
	'timezone'				=> '',
	'allow_com'				=> 1, # 0, 1: everybody, 2: subscribers only
	'mod_com'				=> 0,
	'mod_art'				=> 0,
	'enable_rss'			=> 1,
	'enable_rss_comment'	=> 1,
	'capcha'				=> 1,
	'lostpassword'			=> 1,
	'style'					=> 'defaut',
	'clef'					=> '', # plxUtils::charAleatoire(15)
	'bypage'				=> 5,
	'byhomepage'			=> 0, # count of articles for the homepage. Maybe different from 'bypage'
	'bypage_archives'		=> 5,
	'bypage_tags'			=> 5,
	'bypage_admin'			=> 10,
	'bypage_admin_coms'		=> 10,
	'bypage_feed'			=> 8,
	'tri'					=> 'desc',
	'tri_coms'				=> 'asc',
	'images_l'				=> 800,
	'images_h'				=> 600,
	'miniatures_l'			=> 200,
	'miniatures_h'			=> 100,
	'thumbs'				=> 0,
	'medias'				=> 'data/medias/',
	'racine_articles'		=> 'data/articles/',
	'racine_commentaires'	=> 'data/commentaires/',
	'racine_statiques'		=> 'data/statiques/',
	'racine_themes'			=> 'themes/',
	'racine_plugins'		=> 'plugins/',
	'homestatic'			=> '',
	'hometemplate'			=> 'home.php',
	'urlrewriting'			=> 0,
	'gzip'					=> 0,
	'feed_chapo'			=> 0,
	'feed_footer'			=> '',
	'version'				=> PLX_VERSION,
	'default_lang'			=> DEFAULT_LANG,
	'userfolders'			=> 0, # Deprecated from PluXml 5.9.0
	'usersfolders'			=> 0,
	'display_empty_cat'		=> 0,
	'custom_admincss_file'	=> '',
	'email_method'			=> 'sendmail',
	'smtp_server'			=> '',
	'smtp_username'			=> '',
	'smtp_password'			=> '',
	'smtp_port'				=> '465',
	'smtp_security'			=> 'ssl',
	'smtpOauth2_emailAdress'	=> '',
	'smtpOauth2_clientId'		=> '',
	'smtpOauth2_clientSecret'	=> '',
	'smtpOauth2_refreshToken'	=> '',
);

# taille redimensionnement des images et miniatures
$img_redim = array('320x200', '500x380', '640x480');
$img_thumb = array('50x50', '75x75', '100x100');

# On sécurise notre environnement si dans php.ini: register_globals = On
if (ini_get('register_globals')) {
	$array = array('_REQUEST', '_SESSION', '_SERVER', '_ENV', '_FILES');
	foreach ($array as $value) {
		if(isset($GLOBALS[$value]))  {
			foreach ($GLOBALS[$value] as $key => $var) {
				if (isset($GLOBALS[$key]) AND $var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}
}

# fonction de chargement d'un fichier de langue
function loadLang($filename) {
	if(file_exists($filename)) {
		include_once $filename;
	}
}

# fonction qui retourne ou change le chemin des fichiers xml de configuration
function path($s, $newvalue='') {
	global $CONSTS;
	if(!empty($newvalue))
		$CONSTS[$s]=$newvalue;
	if(isset($CONSTS[$s]))
		return $CONSTS[$s];
}

# On verifie que PluXml est installé
if(!file_exists(path('XMLFILE_PARAMETERS')) and basename($_SERVER['SCRIPT_NAME']) != 'install.php') {
	header('Location: ' . PLX_ROOT . 'install.php');
	exit;
}

/*
 * Auto-chargement des librairies de classes de PluXml.
 * Le nom de la class doit commencer par plx, suivi d'une lettre majuscule.
 * Exception avec PlxTemplate
 * */
spl_autoload_register(
	function($aClass) {
       return preg_match('@^[pP]lx([A-Z]\w+)$@', $aClass, $matches) and include_once __DIR__ . '/class.plx.' . strtolower($matches[1]) . '.php';
	},
	true,
	true # PluXml first !
);

function plx_session_start() {
	session_start();
	$gc_maxlifetime = ini_get('session.gc_maxlifetime'); # Garbage collector
	$expires = time() + (defined('PLX_ADMIN') ? SESSION_LIFETIME : $gc_maxlifetime);
	if(PHP_VERSION_ID >= 70300) {
		setcookie(
			session_name(),
			session_id(),
			[
				'expires' => $expires,
				'path' => plxUtils::getRacine(true),
				'domain' => '.' . $_SERVER['SERVER_NAME'], // leading dot for compatibility or use subdomain
				'secure' => isset($_SERVER['HTTPS']),
				'httponly' => true,
				'samesite' => 'Strict', // None || Lax  || Strict
			]);
	} else {
		header('Set-Cookie: ' . session_name() . '=' . session_id() . '; expires=' .  gmdate('D, d-M-Y H:i:s', $expires) . ' GMT; Max-Age=' . $gc_maxlifetime . '; path=' . plxUtils::getRacine(true) . '; domain=' . $_SERVER['SERVER_NAME']. '; HttpOnly; SameSite=Strict');
	}
}
