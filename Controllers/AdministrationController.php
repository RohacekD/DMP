<?php
/**
 * 
 * Tento kontroler zajišťuje vše okolo administrace systému.
 * @author Ratan
 *
 */
class AdministrationController extends Controller{
	public function run($parameters){
		
		$this->user_panel = new UserController();
		$this->user_panel->run(array("Panel"));
		
		if (!UserManager::IsLogged() || self::$user->GetAccess()<4) {
			$this->redirect("/error/Access_denied");
		}
		
		switch ($parameters[0]) {
			case "Users":
				$this->head['title']="Správa uživatelů";
				$this->view="administration_users";
				if(!isset($parameters[1])) {
					$this->data['limit']=10;
					$this->data['page']=1;
				}
				else {
					$this->data['limit']=$parameters[1];
					$this->data['page']=$parameters[2];
				}
				$r=Database::Query("SELECT id, Name, Email, Access, First_name, Last_name, Title FROM users ORDER BY Name LIMIT ? OFFSET ?;", array(intval($this->data['limit']), intval(($this->data['page']-1)*$this->data['limit'])));
				$c=Database::OneRowQuery("SELECT count(id) as count FROM users;", array());
				
				$this->data['next']=($this->data['limit']*$this->data['page']<$c['count']);
				foreach ($r as $value) {
					$u = new User();
					$u->SetId($value['id']);
					$u->SetAccess(intval($value['Access']));
					$u->SetEmail($value['Email']);
					$u->SetName($value['Name']);
					$u->SetTitle($value['Title']);
					$u->SetFirstName($value['First_name']);
					$u->SetLastName($value['Last_name']);
					$this->data['users'][]=$u;
				}
			break;
			case "Delete Users":
				Database::DeleteOne("DELETE FROM users WHERE id=?", array($parameters[1]));
				$this->redirect("/administration/users/".$parameters[2]."/".$parameters[3]);
				die();
			break;
			case "Reset Pass":
				$u=new User();
				$u->ChooseUserFromDB($parameters[1]);
				echo $parameters[1];
				$pass=$this->GenPass();
				$u->SetPass($pass);
				Database::Update("UPDATE users SET Pass=MD5(?) WHERE id=?", array($pass, $parameters[1]));
				
				require_once 'Models/MPDF/mpdf.php';
				$mpdf = new mPDF('utf-8', 'A4');
				$mpdf->useOnlyCoreFonts = true;
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetAuthor(Controller::$user->Name());
				$mpdf->SetTitle($u->Name());
				$mpdf->SetCreator("Irimitenkan's requests system");
				$mpdf->SetAutoFont(0);
				
				$html = file_get_contents("Views/print_user.phtml");
				$html = UserManager::PrintUser($u, $html);
			
				$mpdf->WriteHTML($html, 2);
				
				
				$name=$u->Name();
				$mpdf->Output($name, "D");
				$this->redirect("/administration/users/".$parameters[2]."/".$parameters[3]);
			break;
			case "Settings":
				$this->head['title']="Nastavení systému";
				$this->view="system_settings";
				$this->data["docasne"]="smazto";
			break;
			case "Edit Users":
				if(isset($_POST['submit'])) {
					if(!UserManager::ValidEmail($_POST['email'])) {
						return false;
					}
					Database::Update("UPDATE users SET First_name=?, Last_name=?, Email=?, Title=?, Access=? WHERE id=?", array($_POST['firstName'],$_POST['lastName'], $_POST['email'], $_POST['title'],$_POST['access'], $parameters[1]));
				}
				
				$this->head['title']="Editace uživatele";
				$this->view="administration_user";
				$this->data['user_e']=new User();
				$this->data['user_e']->ChooseUserFromDB($parameters[1]);
			break;
			case "New User":
				$this->view="administration_new_user";
				$this->head['title']="Vytváření nových uživatelů.";
				$this->data['a']="a";
				$this->data['error']=false;
				$exceptions=0;
				if(isset($_POST['submit'])) {
					try {
						foreach ($_POST['name'] as $n => $value) {
								$u=new User();
								if (is_numeric($_POST['access'][$n])) $_POST['access'][$n]=(int)$_POST['access'][$n];
								else $this->redirect("error/Chyba-systemu");//standardně se nestane...
								$u->SetAccess($_POST['access'][$n]);
								$u->SetEmail($_POST['email'][$n]);
								$u->SetName($_POST['name'][$n]);
								$u->SetTitle($_POST['title'][$n]);
								$u->SetStyle(Settings::$default_style);
								$u->SetPass($this->GenPass());
								$u->SetSend(true);
								$u->SetStyle("NULL");
								$u->SendInfo(isset($_POST['info_email'][$n]));
								$u->SetFirstName($_POST['firstName'][$n]);
								$u->SetLastName($_POST['lastName'][$n]);
								$users[]=$u;
						}
					}
					catch (Exception $e) {
						$this->data=array_merge($this->data, $_POST);
						$this->data['count']=count($_POST['name']);
						$this->data['error']=true;
						return;
					}
					
					$html = file_get_contents("Views/print_user.phtml");
					foreach ($users as &$value) {
						UserManager::UserToDB($value);
						if($value->info_email()){
							$m=new Mail();
							$m->SetAddressee($value->Email());
							$m->SetMessage("<p>Můžete se přihlásit na <a href=\"".Settings::$url.Settings::$url_base."\">této adrese</a> s přihlašovacím jménem: ".$value->Name()." a heslem: ".$value->GetPass()."</p>");
							$m->SetSubject("Registrace na webu ".Settings::$url.Settings::$url_base);
							$m->Send();
						}
						$pdf.=UserManager::PrintUser($value, $html);
						
					}
					
					require_once 'Models/MPDF/mpdf.php';
					$mpdf = new mPDF('utf-8', 'A4');
					$mpdf->useOnlyCoreFonts = true;
					$mpdf->SetDisplayMode('fullpage');
					$mpdf->SetAuthor(Controller::$user->Name());
					$mpdf->SetTitle($u->Name());
					$mpdf->SetCreator("Irimitenkan's requests system");
					$mpdf->SetAutoFont(0);
					
					$html = UserManager::PrintUser($u, $pdf);
				
					$mpdf->WriteHTML($html, 2);
					
					
					$name=count($users);
					$mpdf->Output($name, "D");
				}
			break;
			default:
	   			$this->redirect("/error/Chyba_systemu");
			break;
		}
	}
	
	/**
	 * 
	 * Generuje 10ti znakové heslo z velkých a malých znaků a čísel.
	 * @return String
	 */
	public function GenPass(){
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array();
	    $alphaLength = strlen($alphabet) - 1;
	    for ($i = 0; $i < 9; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass);
	}
};