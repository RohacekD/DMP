<?php
class Settings{
	public static $DBconn = array(
		"host"	=>	"localhost",
		"pass"	=>	"",
		"user"	=>	"irimitenkan",
		"dbname"=>	"requests"
	);
	
	public static $DBsettings = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
	);
	
	/**
	 * 
	 * Everything before, and TLD
	 * @var string $url
	 */
	public static $url = "http://ratan.maweb.cz";
	/**
	 * 
	 * Everything after TLD starts with backslash
	 * @var string $url_base
	 */
	public static $url_base = "/DMP";
	
	public static $access_levels = array(
		0 	=>	"Učitel",
		1 	=>	"Účetní",
		2	=>	"Zástupce",
		3	=>	"Řiditel",
		4	=>	"Admin"
	);
	
	public static $default_style = "default";
	
	public static $admin_email = "Admin@localhost.com";
}