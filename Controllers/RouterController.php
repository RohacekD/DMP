<?php
require_once 'Settings.class.php';

/**
 * 
 * Vytahuje informace z url a vytváří hlavní controller.
 * @author Ratan
 *
 */
class RouterController extends Controller{
	protected $controller;
	
	public function run($parameters){
		$url=$this->parse($parameters);
		$url=$this->parameters($url);
		$this->ChooseController($url[0]);
		if($url[0]!="Ajax")
			$this->view="main";
		else $this->view="none";
		
		unset($url[0]);
		$url=array_values($url);//maze prvek obsahujici tridu
		
		$this->controller->run($url);
		
		$this->MakeHead();

		$this->data['user_panel']=$this->controller->user_panel;
		$this->data['controller']=$this->controller;
	}
	//complete
	protected function parse($url){
		$url = str_replace(Settings::$url_base, "", $url);
		$url=explode("/", $url);
		
		unset($url[0]);
		$url=array_values($url);//maze prazdny prvni prvek
		
		for($i = 0;$i<count($url);$i++){//odstraneni bilych znaku
			$url[$i]=trim($url[$i]);
		}
		return $url;
	}
	//complete
	private function parameters($url= array()){
		for($i=0;$i<count($url);$i++){
			$url[$i] = str_replace('-', ' ', $url[$i]);
			$url[$i] = ucwords($url[$i]);
		}
		return $url;
	}
	
	/**
	 * 
	 * Vybere dle svého paramteru požadovaný Controller.
	 * V případě, že není zadán Controller je přesměrován na index.
	 * Pokud třída Controlleru neexistuje přesměruje na chybovou hlášku.
	 * @param string $name
	 */
	private function ChooseController($name){
		if($name=="")//default controller
			$name="Index";
			
		$contr_class=$name."Controller";//jmeno kontroleru
		
		if(file_exists("Controllers/".$contr_class.".php")){//pokud kontroler existuje
			$this->controller = new $contr_class;//vytvor jeho instanci
		}
		else
			$this->redirect("/error/Chyba_systemu");
	}

	/**
	 * 
	 * Vytvoří hlavičku stránky podle #controller jinak nahradí defaultními hodnotami.
	 */
	private function MakeHead(){
		if($this->controller->head['title']!=null)
			$this->data['title']=$this->controller->head['title'];
		else
			$this->data['title']="Školní systém žádostí";
		if(UserManager::IsLogged() && isset(self::$user))//uzivatel prihlasen 
			$this->data['style']=settings::$url_base.'/css/'.self::$user->Style().".css";
		else
			$this->data['style']=settings::$url_base.'/css/'.settings::$default_style.'.css';
	}
}
?>