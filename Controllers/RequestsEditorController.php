<?php
/**
 * 
 * Separovaný controller pro editaci requestu, v době kdy vznikal nebyla třída request a validace
 * by v controlleru RequestsController zabírala mnoho místa a učinila by ji nepřehlednou, proto je
 * oddělena
 * @author Ratan
 *
 */
class RequestsEditorController extends Controller{
	private $error = false;//pokud je true po prvnich kontrolach tak se nic do DB nezapise a vypisou se errory
	public function run($parameters){
		if(!UserManager::IsLogged())
			$this->redirect("/error/Neregistrovany-pristup");
			
		$this->user_panel=new UserController;
		
		$this->user_panel->run(array("Panel"));
		if(isset($_POST['submit'])){//kontrola+zapis+presmerovani na request
			if(!isset($_POST['Name'])) {$this->data['error'][]="Musíte zadat jméno žádosti!"; $this->error=true;}
			if(!is_numeric($_POST['Need'])) {$this->data['error'][]="Požadovaná částka musí být platné číslo!"; $this->error=true;}
			if(!is_string($_POST['Need'])) {$this->data['error'][]="Požadovaná částka musí být platné číslo!2"; $this->error=true;}
			if($parameters[0]=="New"){
				if($this->error){
					$this->data=array_merge($this->data,$_POST);
				}
				else{
					$id=Database::Insert("INSERT INTO requests VALUES(?,?,?,?,?,?)", array(null,$_POST['Name'],null,$_POST['Description'], $_POST['Need'],self::$user->id()));
					$this->redirect("/requests/request/".$id);
				}
			}
			else if($parameters[0]=="Edit"){
				if($this->error){
					$this->data=array_merge($this->data,$_POST);
				}
				else{
					$r=Database::OneRowQuery("SELECT id, User_id FROM requests WHERE id=?", array($_POST['id']));
					if($r==null || $r["User_id"]!=self::$user->id())//querry nic nevratilo...
						$this->redirect("/error/Edit_foreign_request");
					else{
						Database::Update("UPDATE requests SET Name=?, Create_date=?, Description=?, Need=? WHERE id=?;", array($_POST['Name'],null,$_POST['Description'], $_POST['Need'], $_POST["id"]));
						$this->redirect("/requests/request/".$_POST["id"]);
					}
				}
			}
			//pokud jsem dosel sem je neco spatne...
			$this->view='request_editor';
			
		}
		else{
			$this->view='request_editor';
			if($parameters[0]=="New"){
				$this->head['title']="Přidat novou žádnaku";
				$this->data[]='a';
			}
			else if($parameters[0]=="Edit"){
				$confirm = Database::OneRowQuery("SELECT count(User_id) FROM requests_confirm WHERE request_id=?", array($parameters[1]) );

				if($confirm[0]==0){//neni jeste nikym schvaleno
					$request= Database::OneRowQuery("SELECT * FROM requests WHERE id=?", array($parameters[1]));

					if($request==null){//neexistujici zaznam
						$this->redirect("/error/Edited_request_exists");
						die();
					}
					
					if($request['User_id']!=self::$user->id()){//editace ciziho requestu
						header("Location: ".settings::$url_base."/error/Edit_foreign_request");
						die();
					}
					$this->data=$request;
				}
				else{
					header("Location: ".settings::$url_base."/error/Edit_after_confirm");
					die();
				}
			}
		}
	}
};