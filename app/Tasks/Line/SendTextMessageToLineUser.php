<?php


namespace App\Tasks\Line;


class SendTextMessageToLineUser
{

    public static function make()
    {
        return new static();
    }

    public function handle($to, $msg)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->post('url', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$this->line_msg_token(),
                ],
                'body' => json_encode([
                    'to' => (array) $to,
                    'messages' => [
                        ['type' => 'text', 'text' => $msg]
                    ]
                ], JSON_UNESCAPED_UNICODE)
            ]);
            $send_status = $res->getStatusCode();
            if ($send_status === 200){
                return true;
            }
            return 'send res status: '.$send_status;
        }catch (\Throwable $e){
            return $e->getMessage();
        }
    }


    protected function line_msg_token()
    {
        return 'ifEyibXHDIDg+NYaTBgpqZWxqIrLjdG1edfFuJHNIOE/6epdB9+E3JhgVZlt9GCjTyBwlFNA1cSr0Dr1M/EvwTN3bHdl1nIWChv9cAne86iqH+LCI1WiruJxDMpXzAEsH27DxxQ/OPpLLbTS9D6k3QdB04t89/1O/w1cDnyilFU=';
    }
}