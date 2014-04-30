<?php
/**
 * 
 * Class represents a request. You can take it by id from DB or build it manualy.
 * And then save it to DB again.
 * @author Ratan
 *
 */
class Request{
	/**
	 * 
	 * Request's creator id.
	 * @var int $user_id
	 */
	private $user_id;
	/**
	 * 
	 * @var User $user
	 */
	private $user;
	/**
	 * 
	 * Request id
	 * @var int $id
	 */
	private $id;
	/**
	 * 
	 * Request description
	 * @var string $description
	 */
	private $description;
	/**
	 * 
	 * Request name
	 * @var string $name
	 */
	private $name;
	/**
	 * 
	 * Money need in request
	 * @var int $need
	 */
	private $need;
	/**
	 * 
	 * When was request created
	 * @var date $date
	 */
	private $date;
	private $confirmable=false;
	private $editable = false;
	private $printable = false;
	private $confirm = array();
	private $confirm_date = array();
	/*
	 * 0 - zastupce
	 * 1 - ucetni
	 * 2 - riditel
	 */
	
	/**
	 * 
	 * Vybere request z db
	 * @bug Neni hotovo
	 * 	Protože nebylo potřeba
	 * @param int $id
	 * @throws Exception {"First attribute for Request::ChooseById must be integer.", 1}
	 */
	public function ChooseById($id){
		if(!is_int($id)) throw new Exception("First attribute for Request::ChooseById must be integer.", 1);
	}
	
	/**
	 * 
	 * Setter for id.
	 * @param int $id
	 * @throws Exception {}
	 */
	public function SetUserId($id){
		if(!is_numeric($id)) throw new Exception("First attribute for Request::SetUserId must be integer.", 1);
		$this->user_id=$id;
		$this->user = new User();
		$this->user->ChooseUserFromDB($this->user_id);
	}
	
	/**
	 * 
	 * Getter for user's id.
	 * @return int UserId
	 */
	public function GetUserId() {
		return $this->user_id;
	}
	
	/**
	 * 
	 * Getter for user's name
	 * @return string
	 */
	public function GetUserName() {
		return $this->user->GetFullName();
	}
	
	/**
	 * 
	 * Setter for id
	 * @param int $id
	 * @throws Exception {"First attribute for Request::SetId must be integer.", 1}
	 */
	public function SetId($id){
		if(!is_numeric($id)) throw new Exception("First attribute for Request::SetId must be integer.", 1);
		$this->id=$id;
	}
	
	/**
	 * 
	 * Getter for id
	 * @return int $id
	 */
	public function GetId() {
		return $this->id;
	}
	
	/**
	 * 
	 * Setter for name
	 * @param string $name
	 * @throws Exception {"First attribute for Request::SetName must be string.", 1}
	 */
	public function SetName($name){
		if(!is_string($name)) throw new Exception("First attribute for Request::SetName must be string.", 1);
		$this->name=$name;
	}
	
	/**
	 * 
	 * Getter for name
	 * @return string $name
	 */
	public function GetName() {
		return $this->name;
	}
	
	/**
	 * 
	 * Setter for request's description
	 * @param string $description
	 * @throws Exception {"First attribute for Request::SetDescription must be string.", 1}
	 */
	public function SetDescription($description){
		if(!is_string($description)) throw new Exception("First attribute for Request::SetDescription must be string.", 1);
		$this->description=$description;
	}
	
	/**
	 * 
	 * Getter for request's descritpion.
	 * @return string $description
	 */
	public function GetDescription() {
		return $this->description;
	}
	
	/**
	 * 
	 * Setter for money need for request
	 * @param int $need
	 * @throws Exception {"First attribute for Request::SetNeed must be integer.", 1}
	 */
	public function SetNeed($need){
		if(!is_numeric($need)) throw new Exception("First attribute for Request::SetNeed must be integer.", 1);
		$this->need=$need;
	}
	
	/**
	 * 
	 * Getter for moeny need for request
	 * @return int $need
	 */
	public function GetNeed() {
		return $this->need;
	}
	
	/**
	 * 
	 * Setter for created date
	 * @param date $date
	 */
	public function SetDate($date){
		$this->date=$date;
	}
	
	/**
	 * 
	 * Getter for created date
	 * @return date $date
	 */
	public function GetDate() {
		return $this->date;
	}
	
	public function SetConfirmable($con) {
		$this->confirmable=$con;
	}
	
	public function CanConfirm() {
		return $this->confirmable;
	}
	
	public function SetPrintable($print) {
		$this->printable=$print;
	}
	
	public function CanPrint() {
		return $this->printable;
	}
	
	public function SetEditable($edit) {
		$this->editable=$edit;
	}
	
	public function CanEdit() {
		return $this->editable;
	}
	
	/**
	 * 
	 * Create confirms and save it to $this->confirm[] and $this->confrim_date[];
	 * @todo: Make confirm object to easy access
	 */
	public function MakeConfirms(){
		$q=Database::Query("SELECT * FROM requests_confirm WHERE Request_id=?;", array($this->GetId()));
		foreach ($q as $value) {
			$u=new User();
			$u->ChooseUserFromDB($value['User_id']);
			$this->confirm[$u->GetAccess()-1]=$u;
			$this->confirm_date[$u->GetAccess()-1]=Database::ToOurDate($value['Date']);
		}
	}
	
	public function ConfirmBySomeone() {
		if($this->confirm==array())
			return false;
		return true;
	}
	
	public function GetConfirms($n) {
		return $this->confirm[$n];
	}
	
	public function GetConfirmsDate($n) {
		return $this->confirm_date[$n];
	}
};