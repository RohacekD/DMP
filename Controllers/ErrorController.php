<?php
/**
 * 
 * Controller sloužící k zobrazování chybových hlášek.
 * Pokusí se zobrazit dle parametrů definované chyby, většinou nějaký krátrký článek a formulář s řešením.
 * Pokud se nepodaří přesměruje na defaultní chybu, kde je odkaz na index.
 * @author Ratan
 *
 */
class ErrorController extends Controller{
	public function run($parameters){
		$user_panel = new UserController();
		$user_panel->run(array("none"));
		
		$this->view="Index";
		switch ($parameters[0]) {
			case "Spatne Prihlaseni":
				$this->head['title']="Špatné přihlášení";
				$this->data['left']=new ArticleController();
				$this->data['left']->run(array("Error_login"));
				$this->data['right']=new UserController();
				$this->data['right']->run(array("Site"));
			break;
			case "Neregistrovany Pristup":
				$this->head['title']="Pokus o přístup bez přihlášení";
				$this->data['left']=new ArticleController();
				$this->data['left']->run(array("Error_login"));
				$this->data['right']=new UserController();
				$this->data['right']->run(array("Site"));
			break;
			case "Access_denied":
				$this->head['title']="Neoprávněný přístup";
				$this->data['left']=new ArticleController();
				$this->data['left']->run(array("Access_denied"));
			break;
			case "Edited_request_exists":
				$this->head['title']="Chyba editace";
				$this->data['left']=new ArticleController();
				$this->data['left']->run(array("Edited_request_exists"));
			break;
			case "Edit_after_confirm":
				$this->head['title']="Editování schválené žádosti";
				$this->data['left']=new ArticleController();
				$this->data['left']->run(array("Edit_after_confirm"));
			break;
			case "Edit_foreign_request":
				$this->head['title']="Editování cizí žádosti";
				$this->data['left']=new ArticleController();
				$this->data['left']->run(array("Edit_foreign_request"));
			break;
			case "Chyba Systemu":
			default:
				$this->head['title']="Chyba systému";
				$this->user_panel = new UserController();
				$this->user_panel->run(array("Panel"));
				$this->data['left']=new ArticleController();
				$this->data['left']->run(array("system_error"));
			break;
		}
		
	}
}