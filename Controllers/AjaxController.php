<?php
/**
 * 
 * Zajišťuje AJAX komunikaci a validaci formulářů. Tento kontroler nedefinuje šablonu,
 * odesílá somotná data pro javascript na druhé straně.
 * @author Ratan
 *
 */
class AjaxController extends Controller{
	public function run($parameters){
		switch ($_POST["form"]) {
			case "user":
				$this->User($_POST["form"]);
			break;
			
			default:
				;
			break;
		}
	}
	
	
	/**
	 * 
	 * Tato funkce se používá pokud byl AJAX volán formulářem, který sousisí s uživateli.
	 * @param array $parameters
	 */
	private function User($parameters){
		switch ($_POST["type"]) {
			case "name":
				if(!UserManager::UniqName($_POST["value"], $_SESSION["user_id"]))
					echo "Uživatelské jméno již někdo používá.";
				die();
			break;
			case "email":
				if(!UserManager::ValidEmail($_POST["value"], $_SESSION["user_id"]))
					echo "Email není validní.";
				if(!UserManager::UniqEmail($_POST["value"], $_SESSION["user_id"])){
					echo "Tento email již někdo používá.";
				}
			break;
			case "pass":
				if(!UserManager::ValidPass($_POST["value"]))
					echo "Toto helso je moc krátké";
			break;
			
			default:
				;
			break;
		}
	}
}