<?php

abstract class Controller{
	protected $data = array();/**< Drží proměnné pro pattern.*/
	
	/**
	 * 
	 * Drzi si informace o uzivately kazda controller co ji chce si ji sam vytvori
	 * @var User $user
	 */
	public static $user;

	/**
	 * 
	 * Sem se uloží UserController s parametrem panel pokud chci uživatelský panel. Pokud ne tak s parametrem none.
	 * @var UserController $user_panel
	 */
	protected $user_panel;
	
	/**
	 * 
	 * Vybírá jakou pattern chce určitý controller použít. Obsahuje jen jméno souboru bez přípony.
	 * @var string $view
	 */
	protected $view = "";

	protected $head = array(
		'title' => '',
		'style' => 'css/default.css'
	);
	
	protected static $header;
	
	/**
	 * 
	 * Vypisuje #$data do šablony vybrané ve #$view.
	 */
	public function show(){
		if($this->data){
			$this->data['user']=self::$user;
			$this->data["header"]=self::$header;
			extract($this->data);
			if(file_exists("Views/".$this->view.".phtml"))
				require_once "Views/".$this->view.".phtml";
			else
				$this->redirect("/error/Chyba_systemu");
		}
	}
	
	/**
	 * 
	 * Přesměrování
	 * @bug Dlouho nefungovalo tak jsem nepoužíval..
	 * @param string $address
	 */
	public function redirect($address){
			header("Location: ".settings::$url.settings::$url_base.$address);
			die();
	}
	/**
	 * 
	 * Tuto metodu musí předefinovat každý controller.
	 * Slouží k vytvoření dat, které se dostadí do šablony, kterou si musí Controller v této metodě také zvolit.
	 * Pokud chce controller pracovat s uživatelskými daty, musí zde do #$user uložit novou instanci class User.
	 * Pokud chce controller, aby v záhlaví stránky byl panel s možnostmi uživatele, musí do #$user_panel vytvořit novou instanci class UserController. 
	 * @param array $parameters
	 */
	abstract function run($parameters);
	
	public function __toString(){
		$this->show();
	}
}