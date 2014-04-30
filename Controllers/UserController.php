<?php

/**
 * 
 * Controller zajišťující práci s uživateli, od zobrazení profilu, uživatelské lišty, jejich nastavení
 * @author Ratan
 *
 */
class UserController extends Controller{
	private $type="panel";
	public function run($parameters){
		$this->data['logged']=UserManager::IsLogged();//pro jakoukoli akci tohoto controleru je dulezita informace jsemli je uzivatel prihlasen

		if(UserManager::IsLogged()){
			self::$user = new User();
			self::$user->ChooseUserFromDB($_SESSION['user_id']);
		}
		$this->ChooseView($parameters);
		
	}
	
	private function ChooseView($parametres){
		if($parametres[0]=='none'){
			$this->view='none';
			return;
		}
		
		if($this->data['logged']==false){
			switch ($parametres[0]){
				case "Panel":
					$this->view='user_panel_unloged';
				break;
				case "Site":
					$this->view='user_site_unloged';
				break;
				default:
					$this->redirect("/error/Neregistrovany-pristup");
				break;
			}
		}
		else
			switch ($parametres[0]){
				case "Panel":
					$this->view='user_panel';
				break;
				case "Profil":
					$this->head['title']="Profil uživatele";
					$this->view='index';
					self::$header="Profil uživatele";
					
					$this->user_panel=new UserController;
					$this->user_panel->run(array("Panel"));
					
					$this->data['left']= new ProfilController();
					$this->data['left']->run(array('profil'));
					$this->data['right']= new RequestsController();
					$this->data['right']->run(array('My Requests'));
				break;
				case "Settings":
					$errors=UserManager::Settings();
					if($errors==1)
						$this->data['account_error']="Zadané hodnoty nejsou validní";
					else if($errors==2)
						$this->data['settings_error']="Zadané hodnoty nejsou validní nebo jste nezadal heslo";
					$this->view='user_settings';
					$this->head['title']="Nastavení uživatele";
					$this->user_panel=new UserController();
					$this->user_panel->run("panel");
				break;
				default:
					$this->view='user_panel';
				break;
			}
	}
}