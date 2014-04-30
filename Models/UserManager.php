<?php
/**
 * 
 * Třída sloužící k manipulaci s uživateli. K ukládání používá Class User 
 * @author Ratan
 * @bug Need refactor
 *
 */
class UserManager{
	/**
	 * Validace přihlášení. Pokud je přihlášení validní vrátí true a přihlásí uživatele, jinak přesměruje na chybovou hlášku.
	 * @param $login Přihlašovací jméno
	 * @param $password Přihlašovací heslo
	 */
	public static function LogIn($login, $password){
		$user=Database::OneRowQuery("SELECT id FROM users WHERE Name = ? AND Pass = ?", array($login, md5($password)));
		if($user == false){
	   			header("Location: ".settings::$url_base."/error/Spatne-prihlaseni");
		}
		else{
			$_SESSION["user_id"]=$user['id'];
	   			header("Location: ".settings::$url_base."/");
			return true;
		}
	}
	
	/**
	 * 
	 * Volá se na začátku scriptu. Zjišťuje jestli uživatel chce něco jako třeba:
	 * Přihlásit se odhlásit...
	 */
	public static function WantSomething(){//pokud bylo pozadano o neco z post okolo loginu
		if(isset($_POST['login'])){
				if(isset($_POST["name"]) and isset($_POST["pass"])){//jsou nastaveny vsechny informace?
					UserManager::LogIn($_POST['name'], $_POST["pass"]);
				}
		}
		else if(isset($_POST['logout'])){
			UserManager::LogOut();
		}
	}
	
	/**
	 * Odhlásí aktuálního uživatele
	 */
	public function LogOut(){
		Database::Update("UPDATE users SET Last_login = CURRENT_TIMESTAMP() WHERE id=?", array($_SESSION["user_id"]));
		unset($_SESSION["user_id"]);
   		header("Location: ".settings::$url_base."/") or die(settings::$url_base."/");
	}
	
	
	/**
	 * Vrací true pokud je uživatel přihlášen a provede kontrolu platnosti přihlášení.
	 */
	public static function IsLogged(){
		if(isset($_SESSION['user_id'])){
			$num=Database::OneRowQuery("SELECT count(`id`) FROM users WHERE `id` = ?", array($_SESSION['user_id']));
			if($num[0]==1)
				return true;
		}
		return false;
	}
	
	/**
	 * 
	 * Vrací access level uživatele podle jeho zadaného id.
	 * @param int $id
	 */
	public static function AccessLevelById($id){
		$r=Database::OneRowQuery("SELECT Access FROM users WHERE `id` = ?;", array($id));
		return $r['Access'];
	}

	/**
	 * Zjistí jestli bylo požádáno o změnu nastavení a o jakou se jedná.
	 */
	public static function Settings(){
		if(isset($_POST['settings'])){
			if(!self::Settings_set()){
				return 1;/**< Něco je špatně s částí settings */
			}
		}
		if(isset($_POST['account'])){
			if(!self::Settings_acc()){
				return 2;/**< Něco je špatně s částí account */
			}
		}
	}

	private static function Settings_set(){
		if(file_exists("css/".$_POST['style'].".css")){
			$Send=false;
			if(isset($_POST['email'])){
				$Send=true;
			}
			Database::Update("UPDATE users SET Send = ?, Style = ? WHERE id = ?;", array($Send, $_POST['style'], Controller::$user->id()));
			return true;
		}
		else return false;
	}

	/**
	 * 
	 * Zjišťuje jestli jsou zadané údaje validní. Vrací true pokud ano, False pokud ne
	 * @return boolean 
	 */
	private static function Settings_acc(){
		if(isset($_POST['old_pass'])) {
			$pass=Database::OneRowQuery("SELECT count(id) FROM users WHERE pass=MD5(?);", array($_POST['old_pass']));
			if($pass==false)
				return false;
			if(!self::ValidEmail($_POST['email']))
				return false;
			if(!self::UniqName($_POST['name'], $_SESSION['user_id']))
				return false;
			if(!self::UniqEmail($_POST['email'], $_SESSION['user_id']))
				return false;
			if(!isset($_POST["last_name"]) || $_POST["last_name"]=="")
				return false;
			if(!isset($_POST["first_name"]) || $_POST["first_name"]=="")
				return false;
			
			if(isset($_POST['pass']) && isset($_POST['pass_r']) && $_POST['pass'] && $_POST['pass_r']){
				if($_POST['pass']==$_POST['pass_r']){
					if(self::ValidPass($_POST['pass']))
						Database::Update("UPDATE users SET Name=?, First_name=?, Last_name=?, Title=?, Email=?, Pass=MD5(?) WHERE id=?;", array($_POST['name'], $_POST["first_name"], $_POST["last_name"], $_POST['title'], $_POST['email'], $_POST['pass'], Controller::$user->id()));
					else return false;
				}
				else
					return false;
			}
			else{
				Database::Update("UPDATE users SET Name=?, First_name=?, Last_name=?, Title=?, Email=? WHERE id=?;", array($_POST['name'], $_POST["first_name"], $_POST["last_name"], $_POST['title'], $_POST['email'], Controller::$user->id()));
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * Zjišťuje jestli stejné jméno má již někdo jiný než dotayovaný user.
	 * @param string $name
	 * @param int $actual_user
	 * @return boolean
	 */
	public static function UniqName($name, $actual_user){
		$r=Database::OneRowQuery("SELECT count(id) FROM users WHERE name=? && id!=?;", array($name, $actual_user));
		if ($r[0]==0) return true;
		return false;
	}
	
	/**
	 * 
	 * Zjišťuje jestli stejný email má již někdo jiný než dotazovaný user.
	 * @param string $email
	 * @param int $actual_user
	 * @return boolean
	 */
	public static function UniqEmail($email, $actual_user){
		$r=Database::OneRowQuery("SELECT count(id) FROM users WHERE email=? && id!=?;", array($email, $actual_user));
		if ($r[0]==0) return true;
		return false;
	}
	
	/**
	 * 
	 * Zjišťuje jestli je email validní.
	 * @param string $email
	 * @return boolean
	 */
	public static function ValidEmail($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * 
	 * Tests if passwrod is longer than 9 characters
	 * @param string $pass
	 * @return boolean
	 */
	public static function ValidPass($pass){
		return strlen($pass)>9;
	}
	
	/**
	 * 
	 * Podle přijatého objektu zapíše uživatele do DB.
	 * @param User $user
	 * @throws Exception {"Attribute 1 of UserManager::UserToDB must be object", 1}
	 * @throws Exception {"Bad attribute type", 1}
	 * @bug Nedodělaná vertze pro editaci uživatele, není potřeba jen pokud by někdo rozšiřoval.
	 */
	public static function UserToDB($user){
		if(!is_object($user)) throw new Exception("Attribute 1 of UserManager::UserToDB must be object", 1);
		if(get_class($user)!="User") throw new Exception("Bad attribute type", 1);
		
		if (!$user->id()){//new user
			Database::Insert("INSERT INTO users (Name, Pass, Email, Send, Access, Style, First_name, Last_name, Title) VALUES(?, MD5(?), ?, ?, ?, ?, ?, ?, ?)", array($user->Name(), $user->GetPass(), $user->Email(), $user->Send(), $user->GetAccess(), $user->Style(), $user->GetFirstName(), $user->GetLastName(), $user->GetTitle()));
		}
		else{//user edit
			
		}
	}
	
	/**
	 * Převede data o uživateli do vytisknutelné podoby.
	 * @param User $user 
	 * @param string $html HTML code with tags for insert data
	 * @return string HTML kod pro tisk
	 */
	public static function PrintUser($user, $html) {
		$html = str_replace("#name", $user->GetFullName(), $html);
		$html = str_replace("#login", $user->Name(), $html);
		$html = str_replace("#pass_l", $user->GetPass(), $html);
		$html = str_replace("#path", Settings::$url.Settings::$url_base, $html);
		return $html;
	}
}