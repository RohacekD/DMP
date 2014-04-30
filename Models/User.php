<?php
/**
 * Exceptions:
 * "First attribute for User::SetName must be string.", 1
 * "First attribute for User::SetAccess() must be integer.", 2
 * "First attribute for User::SetStyle() must be string.", 3
 * "First attribute for User::SetEmail() must be string.", 4
 * "First attribute for User::SetEmail() must be valid email.", 5
 * "First attribute for User::SetSend() must be boolean.", 6
 * "First attribute for User::SetName must be uniqve Name.", 7
 * "First attribute for User::SetFirstName() must be string.", 8
 * "First attribute for User::SetLastName() must be string.", 9
 * "First attribute for User::SetLastName() must be valid.", 10
 * "First attribute for User::SetFirstName() must be valid.", 11
 * "First attribute for User::SetEmail must be uniqve Email.", 12
 */
/**
 * Třída reprezentující uživatele. Ať přihlášeného nebo vytvářeného.
 * @author Ratan
 *
 */
class User{
	private $name;
	private $first_name;
	private $last_name;
	private $access;
	private $style;
	private $id;
	private $email;
	private $send;
	private $info;
	private $title;
	private $pass;/**< Jen pro vytvareni a editaci adminem. */
	public function __construct(){
	}
	
	/**
	 * Vybere uživatele z databáze dle id a uloží data do objektu.
	 * @param int $id
	 */
	public function ChooseUserFromDB($id){
		$quary=Database::OneRowQuery("SELECT * FROM users WHERE `id`=?", array($id));
		$this->id=$id;
		if($quary==false){
			header("Location: ".settings::$url_base."/error/Vnitrni chyba");
			die();
		}
		/* tady nepoužívám settery protože data z DB považuju za validní. */
		$this->name=$quary['Name'];
		$this->access=$quary['Access'];
		$this->email=$quary['Email'];
		$this->send=$quary['Send'];
		$this->Last_login=$quary['Last_login'];
		$this->first_name=$quary['First_name'];
		$this->last_name=$quary['Last_name'];
		if($quary['Title']!=NULL)
			$this->title=$quary['Title'];
		
		
		if($quary['Style']!=null && $quary['Style']!="NULL")
			$this->style=$quary['Style'];
		else
			$this->style="default";
	}

	/**
	 * Getter for id
	 */
	public function id(){ return $this->id;}
	
	public function SetId($id){
		$this->id=$id;
	}

	/**
	 * Getter for name
	 */
	public function Name(){
		return $this->name;
	}

	/**
	 * Setter for name
	 * @param string $name
	 * @throws Exception {}
 	 * "First attribute for User::SetName must be uniqve Name.", 7
 	 * "First attribute for User::SetName must be string.", 1
	 */
	public function SetName($name){
		if(!is_string($name)) throw new Exception("First attribute for User::SetName must be string.", 1);
		if(isset($this->id)){
			if(!UserManager::UniqName($name, $this->id)) throw new Exception("First attribute for User::SetName must be uniqve Name.", 7);
		}
		else {
			if(!UserManager::UniqName($name, 0)) throw new Exception("First attribute for User::SetName must be uniqve Name.", 7);
		}
			$this->name=$name;
	}

	/**
	 * Getter for access level.
	 */
	public function GetAccess(){
		return $this->access;
	}

	/**
	 * Setter for access
	 * @param int $access
	 * @throws Exception {"First attribute for User::SetAccess() must be integer.", 2}
	 */
	public function SetAccess($access){
		if(!is_int($access)) throw new Exception("First attribute for User::SetAccess() must be integer.", 2);
		$this->access=$access;
	}

	/**
	 * Getter for style
	 */
	public function Style(){
		return $this->style;
	}

	/**
	 * Setter for style
	 * @param string $style
	 * @throws Exception {"First attribute for User::SetStyle() must be string.", 3}
	 */
	public function SetStyle($style) {
		if(!is_string($style)) throw new Exception("First attribute for User::SetStyle() must be string.", 3);
		$this->style=$style;
	}

	/**
	 * Getter for last Login time
	 */
	public function lastLogIn(){
		return $this->Last_login;
	}

	/**
	 * Getter for email
	 */
	public function Email(){
		return $this->email;
	}

	/**
	 * Setter for email
	 * @param string $email
	 * @throws Exception {"First attribute for User::SetEmail() must be string.", 4}
	 * @throws Exception {"First attribute for User::SetEmail() must be valid email.", 5}
	 * @throws Exception {"First attribute for User::SetEmail must be uniqve Email.", 12}
	 */
	public function SetEmail($email) {
		if(!is_string($email)) throw new Exception("First attribute for User::SetEmail() must be string.", 4);
		if(!UserManager::ValidEmail($email)) throw new Exception("First attribute for User::SetEmail() must be valid email.", 5);
		if($this->id!=null){
			if(!UserManager::UniqEmail($email, $this->id)) throw new Exception("First attribute for User::SetEmail must be uniqve Email.", 12);
		}
		else{
			if(!UserManager::UniqEmail($email, 0)) throw new Exception("First attribute for User::SetEmail must be uniqve Email.", 12);
		}
			$this->email=$email;
	}

	/**
	 * Getter for send. True if user want to send news to email.
	 */
	public function Send(){
		return $this->send;
	}

	/**
	 * Setter for send. True if user want to send news to email.
	 * @param bool $send
	 * @throws Exception {"First attribute for User::SetSend() must be boolean.", 6}
	 */
	public function SetSend($send) {
		if(!is_bool($send)) throw new Exception("First attribute for User::SetSend() must be boolean.", 6);
		$this->send=$send;
	}

	/**
	 * @return int Num of posted Requests
	 */
	public function NumRequests(){
		$r=Database::OneRowQuery("SELECT count(id) as num FROM requests WHERE User_id=?", array($this->id));
		return $r["num"];
	}

	/**
	 * Getter for password. Only for situations when admin edit/create users.
	 * @return string
	 */
	public function GetPass(){
		return $this->pass;
	}
	
	/**
	 * Setter for password. Only for situations when admin edit/create users.
	 * @param string $pass
	 */
	public function SetPass($pass){
		$this->pass=$pass;
	}
	
	/**
	 * Setter for first_name
	 * @param string $first_name
	 * @throws Exception {"First attribute for User::SetFirstName() must be string.", 8}
	 * @throws Exception {"First attribute for User::SetFirstName() must be valid.", 11}
	 */
	public function SetFirstName($first_name) {
		if(!is_string($first_name)) throw new Exception("First attribute for User::SetFirstName() must be string.", 8);
		if($first_name=="") throw new Exception("First attribute for User::SetFirstName() must be valid.", 11);
		$this->first_name=$first_name;
	}
	
	
	/**
	 * Setter for last_name
	 * @param string $last_name
	 * @throws Exception {"First attribute for User::SetLastName() must be string.", 9}
	 * @throws Exception {"First attribute for User::SetLastName() must be valid.", 10}
	 */
	public function SetLastName($last_name) {
		if(!is_string($last_name)) throw new Exception("First attribute for User::SetLastName() must be string.", 9);
		if($last_name=="") throw new Exception("First attribute for User::SetLastName() must be valid.", 10);
		$this->last_name=$last_name;
	}

	/**
	 * Getter for first_name.
	 * @return string
	 */
	public function GetFirstName(){
		return $this->first_name;
	}

	/**
	 * Getter for last_name.
	 * @return string
	 */
	public function GetLastName(){
		return $this->last_name;
	}
	
	public function GetFullName(){
		return $this->GetTitle()." ".$this->last_name." ".$this->first_name;
	}
	
	public function GetTitle(){
		if($this->title!="NULL")
			return $this->title;
		return null;
	}
	
	public function SetTitle($title){
		if($title!=NULL)
			$this->title=$title;
	}
	
	public function SendInfo($send){
		$this->info=$send;
	}
	
	public function info_email(){
		return $this->info;
	}
}