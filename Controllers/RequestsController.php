<?php
/**
 * 
 * Pokud se nastaví data['settings']=true; bude se zobrazovat lišta s možnostmi... Schválené, neschválené..
 * @author Ratan
 *
 */
class RequestsController extends Controller{
	
	private $req;
	
	public function run($parameters){//$parameters[0] >> jakej typ vybrat
		if(!UserManager::IsLogged())
			$this->redirect("/error/Neregistrovany-pristup");
			
		
		if($parameters[0]=="New"){//proste nevim jak jinak :D
			header("Location: ".settings::$url_base."/RequestsEditor/New");
			die();
		}
			
		$this->view='request';

		$this->user_panel=new UserController;
		$this->user_panel->run(array("Panel"));
		/**============================Making limit and offset======================**/
		
		$this->data['offset']=(isset($parameters[2])?$parameters[2]:0);
		$this->data['limit']=(isset($parameters[1])?$parameters[1]:(isset(self::$header)?6:24));
		$parameters["limit"]=$this->data["limit"];
		$parameters["offset"]=$this->data["offset"];
			
		switch ($parameters[0]) {
			case "History":
				$this->data['type']="Historie žádostí";//nadpis
				$this->data['settings']=true;//zapne možnosti
				$this->head['title']="Historie žádostí";
			break;
			case "My Requests":
				$this->data['type']="Moje žádosti";//nadpis
				
			break;
			case "New-requests":
			case "New Requests":
				$this->data['type']="Nové žádosti";
			break;
			case "Request":
				$parameters["offset"]=0;
				$this->view="simple_request";
			break;
			case "Print":
				$req = new RequestsManager();
				$req->SelectRequests(array(0=>"Request", 1=>$parameters[1],"limit"=> 1,"offset"=>0));
				$req=$req->GetRequests();			
				$req=$req[0];
				$req->MakeConfirms();
				
				
				
				require_once 'Models/MPDF/mpdf.php';
				$mpdf = new mPDF('utf-8', 'A4');
				$mpdf->useOnlyCoreFonts = true;
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetAuthor(Controller::$user->Name());
				$mpdf->SetTitle($req->GetName());
				$mpdf->SetCreator("Irimitenkan's requests system");
				$mpdf->SetAutoFont(0);
				
				$stylesheet = file_get_contents('css/faktura.css');
				$html = file_get_contents("Views/print_faktura.phtml");
				
				$html=RequestsManager::PrintRequest($req, $html);
				
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->WriteHTML($html, 2);
				
				$name=$req->GetName();
				$mpdf->Output($name, "D");
				die();
			break;
			case "Confirm":
				if(self::$user->GetAccess()!=0 && self::$user->GetAccess()!=4)
					RequestsManager::Confirm($parameters[1]);
				$this->redirect("/requests/request/$parameters[1]");
			break;
			default:
				;
			break;
		}
			
		/**============přidáme limit a offset pro manager=======================**/
		
		$this->req = new RequestsManager();
		$this->req->SelectRequests($parameters);
		$this->data["num_req"]=$this->req->GetTotalNumRequests();
		$this->data["type_url"]=str_replace(" ", "-", $parameters[0]);
		
		$this->data['requests']=$this->req->GetRequests();
		
		if($parameters[0]=="Request") {//vybirame jen jednu zadost
			$this->data['request']=$this->data['requests'][0];
			$this->data['request']->MakeConfirms();
		}
			
			
		if(!$this->data['requests']) {//nevratily se žádná data
			$this->NoData($parameters[0]);
		}
		
		if($parameters[0]=="History") {
			$this->data['settings']=true;//zapne možnosti
			$this->head['title']="Historie žádostí";
		}
		
		
		if(isset($_POST['from']) && $_POST['from']!="") {
			$this->data['from']=$_POST['from'];
		}
		if(isset($_POST['to']) && $_POST['to']!="") {
			$this->data['to']=$_POST['to'];
		}
	}
	
	private function NoData($param){
		switch ($param) {
			case "History":
				$this->data['error']="Ještě nebyly zadány žádné žádosti.";
			break;
			case "My Requests":
				$this->data['error']="Ještě jste o nic nežádaly.";//nadpis
				
			break;
			case "New-requests":
			case "New Requests":
				$this->data['error']="Od minula nepřibyly žádné žádosti.";
			break;
			case "Request":
			default:
				$this->redirect("/error/Chyba-systemu");
			break;
		}
	}
	
};
