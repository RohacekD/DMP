<?php
/**
 * 
 * Tento controller dodává data pro zobrazení profilu uživatele.
 * @author Ratan
 *
 */
class ProfilController extends Controller{
	public function run($parameters){
		if(!UserManager::IsLogged())
			$this->redirect("/error/Neregistrovany-pristup");
		$this->view='profil';
		$this->data['name']=self::$user->Name();
		$this->data['access']=Settings::$access_levels[self::$user->GetAccess()];
		$this->data['lastLogIn']= Database::ToOurDate(self::$user->lastLogIn());
		$this->data['fullName']=self::$user->GetFullName();
		$this->data['requests']=self::$user->NumRequests();
	}
};