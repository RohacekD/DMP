<?php
/**
 * 
 * Výchozí controller pro výchozí stránku.
 * @author Ratan
 *
 */
class IndexController extends Controller{

	public function run($parameters){
		$this->view="index";
		$this->data['a']='a';//bez toho se nic nevypise
		$this->user_panel=new UserController;//Nacti uzivatele
		$this->head['title']="Školní systém žádostí";
		$this->user_panel->run(array("Panel"));
			
		if(!UserManager::IsLogged()){
			$this->data['right']= new ArticleController();
			$this->data['right']->run(array("welcome"));
			$this->data['left']= new ArticleController();
			$this->data['left']->run(array("index"));
		}
		else{
			
			self::$header="Školní systém pro zadávání žádostí";
			$this->data['right']= new RequestsController();
			$this->data['right']->run(array("New-requests"));
			$this->data['left']= new ArticleController();
			$this->data['left']->run(array("welcome"));
		}
	}
}
?>