<?php
namespace  App\Helpers;
class WhatsappAPI{


    private $id=4907;
    private $key="2bc3ab6639dfdb26c0f05af3e9f49c75486fe7c9";

  public function __construct($id, $key){

	$this->id = $id;
	$this->key = $key;

  }

  public function send($send_to, $message_body){

    $data = array('to' => $send_to, 'msg' => $message_body);

    $url = "https://onyxberry.com/services/wapi/Client/sendMessage";
    $url = $url.'/'.$this->id.'/'.$this->key;
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec( $ch );
    return $response;
  }
}

?>
