<?php

/**
 * 
 * Třída zajišťující veškeré operace s requesty, pro práci s nimi používá Class Request.
 * @author Ratan
 *
 */
class RequestsManager{
	private $r;
	private $ret;
	private $total_pocet;
	private $q="";
	private $q_b="SELECT *, requests.name as r_name, 
		requests.id as r_id, users.id as u_id, Create_date as date
		FROM requests JOIN users ON requests.User_id=users.id ";/**< Používám pro výběr z DB.*/
	private $q_c="SELECT count(requests.id) as pocet FROM requests JOIN users ON requests.User_id=users.id ";/**< Pro zjisteni kolik radku vybiram celkem */
	
	
	public function __construct(){
	}
	
	/**
	 * TODO: Some refactor, this function is long and can be split into smaller classes
	 * @param array $option
	 */
	public function SelectRequests($option){
		switch ($option[0]) {
			case "My Requests":
				$this->q.="WHERE User_id=?";
				$this->AskDB(array(Controller::$user->id()), $option["limit"], $option["offset"]);
			break;
			case "History":
				$a=array();
				if(isset($_POST['submit'])){
					$from = false;
					if($_POST['from']!="") {
						$this->q.="WHERE Create_date > ?";
						$a[]=$_POST['from']." 00:00:00";
						$from=true;
					}
					if($_POST['to']!="") {
						if($from) {
							$this->q." AND Create_date < ?";
						}
						else 
							$this->q."WHERE Create_date < ?";
						$a[]=$_POST['to']." 00:00:00";
					}
				}
				$this->AskDB($a, $option["limit"], $option["offset"]);
			break;
			case "New-requests":
			case "New Requests":
				$this->q.="WHERE DATEDIFF(Create_date,?) >= 0";
				$this->AskDB(array(Controller::$user->lastLogIn()), $option["limit"], $option["offset"]);
			break;
			case "Request":
				$this->q.="WHERE requests.id=?";
				$this->AskDB(array($option[1]), $option["limit"], $option["offset"]);
			break;
			default:
				;
			break;
		}
		
		foreach ($this->r as &$row) {
			$row['print']=$this->IsPrintable($row['r_id']);
			$row['edit']=$this->IsEditable($row['User_id'], $row['r_id']);
			$row['date']=Database::ToOurDate($row['date']);
			$this->ret[]=$this->MakeObject($row);
		}
		
		if($option[0]=="History") {
			if(isset($_POST['submit'])){
				if ($_POST['confirm']=="approved") {//schvalene
					foreach ($this->ret as  $n => $row) {
						if($row->CanPrint()==false) {
							unset($this->ret[$n]);
							$this->total_pocet--;
						}
					}
				}
				elseif ($_POST['confirm']=="unapproved"){//neschválené
					foreach ($this->ret as $n => $row) {
						if($row->CanPrint()) {
							unset($this->ret[$n]);
							$this->total_pocet--;
						}
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * Vrací true/false podle toho jestli je tento request tisknutelný nebo ne.
	 * Závisí na schválení všech nutných osob.
	 * @param int $id
	 */
	private function IsPrintable($id){
		 $r=Database::Query("SELECT User_id FROM requests_confirm WHERE Request_id = ?;", array($id));
		 $i=0;
		 foreach ($r as $row){
		 	$i+=UserManager::AccessLevelById($row['User_id']);
		 }
		 if($i==6) return true;
		 return false;
	}
	
	/**
	 * 
	 * Dostává User_id z requests a pokud se schoduje s přihlášeným userem tak vrátí true.
	 * @param int $id
	 */
	private function IsEditable($u_id, $id){
		if($u_id!=Controller::$user->id())return false;
		$a=Database::OneRowQuery("SELECT count(User_id) as num FROM requests_confirm WHERE Request_id=?", array($id));
		if($a['num']==0) return true;
		return false;
	}
	
	/**
	 * 
	 * Vrací počet žádanek odpovídající kritériím.
	 */
	public function GetNumRequests(){
		return sizeof($this->r);
	}
	
	/**
	 * 
	 * Vrací kompletní počet requestů.
	 */
	public function GetTotalNumRequests() {
		return $this->total_pocet;
	}
	
	/**
	 * Vrátí všechny žádanky vybrané dle kritérií. 
	 */
	public function GetRequests(){
		return $this->ret;
	}
	
	private function MakeObject($row) {
		$build = new Request();
		$build->SetDate($row['date']);
		$build->SetDescription($row['Description']);
		$build->SetId($row['r_id']);
		$build->SetUserId($row['u_id']);
		$build->SetNeed($row['Need']);
		$build->SetName($row['r_name']);
		$build->SetConfirmable(self::CanConfirm($build));
		$build->SetPrintable($row['print']);
		$build->SetEditable($row['edit']);
		return $build;
	}
	
	/**
	 * 
	 * Kontroluje jestli aktuální uživatel může request $id potvrdit.
	 * Kritérium je, že ještě nidko se stejným oprávněním jako má tento uživatel ještě tuto request nepotvrdil.
	 * @param Request $request
	 */
	public static function CanConfirm($request){
		if (Controller::$user->GetAccess()==0) return false;//učitel nic neschvaluje
		if (Controller::$user->GetAccess()==4) return false;//admin nic neschvaluje
		if ($request->GetUserId()==Controller::$user->id()) return false;//předsi to neodsouhlasí sám sobě...
		$r=Database::OneRowQuery("SELECT count(User_id) as num FROM requests_confirm 
		JOIN users ON users.id=requests_confirm.User_id 
		WHERE requests_confirm.Request_id = ? AND users.Access=?", array($request->GetId(), Controller::$user->GetAccess()));

		if ($r['num']==0)
			return true;
		return false;
		
	}
	
	/**
	 * 
	 * Potcrzení žádosti uživatelem.
	 * @param int $id id Requestu.
	 */
	public static function Confirm($id){
		if(Controller::$user->GetAccess()!=0 && Controller::$user->GetAccess()!=4) {
			$req=new RequestsManager();
			$req->SelectRequests(array("Request", $id,"limit"=>1,"offset"=>0));
			$req=$req->GetRequests();
			$req=$req[0];
			if(self::CanConfirm($req))
				Database::Insert("INSERT INTO requests_confirm VALUES (?,?,null);", array(Controller::$user->id(), $id));
		}
	}
	
	/**
	 * Připraví requestu pro tisk.
	 * @param Request $req
	 * @param string $html
	 */
	public static function PrintRequest($req, $html) {

		$html = str_replace("#description", $req->GetDescription(), $html);	
		$html = str_replace("#zebr_jm", $req->GetUserName(), $html);	
		$html = str_replace("#zebr_date", $req->GetDate(), $html);
		$html = str_replace("#confirm_jm", $req->GetConfirms(0)->GetFullName(), $html);
		$html = str_replace("#confirm_date", $req->GetConfirmsDate(0), $html);
		$html = str_replace("#reditel_jm", $req->GetConfirms(1)->GetFullName(), $html);
		$html = str_replace("#reditel_date", $req->GetConfirmsDate(1), $html);
		$html = str_replace("#ucetni_jm", $req->GetConfirms(2)->GetFullName(), $html);
		$html = str_replace("#ucetni_date", $req->GetConfirmsDate(2), $html);
		
		return $html;
	}
	
	/**
	 * 
	 * Extends querry with LIMIT and OFFSET
	 * @param array $param
	 * @param int $limit
	 * @param int $offset
	 */
	private function AskDB($param = array(), $limit, $offset) {
		$this->q_b.=$this->q." LIMIT ? OFFSET ?;";//tvorba dotazu pro data
		$this->q_c.=$this->q.";";//tvorba dotazu pro pocet
		
		$a=Database::OneRowQuery($this->q_c, $param);//dotaz na pocet celkem
		$this->total_pocet=$a["pocet"];
		
		$param[]=(int)$limit;
		$param[]=(int)$offset*$limit;

		$this->r=Database::Query($this->q_b, $param);
	}
};