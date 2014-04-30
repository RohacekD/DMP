<?php
/**
 * 
 * Jednoduchý controller zobrazující články ze souborů. Stačí mu předat, který soubor se má zobrazit.
 * @author Ratan
 *
 */
class ArticleController extends Controller{//vetsinou jen obsah fragmentu
	public function run($parameters){//prebira na indexu 0 nazev clanku ktery se nachazi ve slozce views/articels
		$this->view="Articles/".$parameters[0];
		$this->data[1]="zobraz";
	}
}