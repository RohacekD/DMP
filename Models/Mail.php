<?php
/**
 * 
 * Třída, do které se uloží informace o odesílaném emailu, a ten je potom odeslán adresátovi.
 * @bug not tested
 * @author Ratan
 *
 */
class Mail{
	private $to;
	private $subject;
	private $pattern="
<html>
<head>
  <title>#subject</title>
</head>
<body>
	<h1>#subject</h1>
	#message
</body>
</html>";
	private $message;
	private $headers;
	
	public function SetAddressee($addressee) {
		$this->to=$addressee;
	}
	
	public function SetSubject($sub) {
		$this->subject=$sub;
	}
	
	public function SetMessage($mes) {
		$this->message=$mes;
	}
	
	/**
	 * 
	 * Příprava a odeslání emailu na adresu.
	 * @throws Exception
	 * @bug not tested
	 */
	public function Send() {
		if(!isset($this->to)) throw new Exception("You must set addressee first.", 1);
		if(!isset($this->to)) throw new Exception("You must set message first.", 2);
		if(!isset($this->to)) throw new Exception("You must set subject first.", 3);
		
		$this->headers  = 'MIME-Version: 1.0' . "\r\n";
		$this->headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$this->headers .=	'From: '.Settings::$admin_email . "\r\n" .
   					'Reply-To: '.Settings::$admin_email . "\r\n" .
   					'X-Mailer: PHP/' . phpversion();
		$this->message=str_replace("#message", $this->message, $this->pattern);
		$this->message=str_replace("#subject", $this->subject, $this->message);
		
		mail($this->to, $this->subject, $this->message, $this->headers);
	}
}