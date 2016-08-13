<?php
/*
 * Require: curl, ssl
 * Last modified: 28.02.2014
 */

class SmtpApi {
    const BASE_URL = '';
    const ENC_METHOD = 'aes-128-cbc';

    private static $sPublicKey = '';

    //******************************************************************************************************************
    /*
     * @param string $sPublicKey Public key
     */
    public function __construct($sPublicKey=''){
        self::$sPublicKey = str_replace("\r\n","\n",trim($sPublicKey));
    }

    //******************************************************************************************************************
    /*
     * @param string $sPublicKey Public key
     */
    public function setPublicKey($sPublicKey=''){
        self::$sPublicKey = str_replace("\r\n","\n",trim($sPublicKey));
    }

    //******************************************************************************************************************
    public function ping(){
        $sRequest = json_encode(array('action'=>'ping'));
        return self::__callApi($sRequest);
    }

    //******************************************************************************************************************
    public function ips(){
        $sRequest = json_encode(array('action'=>'ips'));
        return self::__callApi($sRequest);
    }

    //******************************************************************************************************************
    public function domains(){
        $sRequest = json_encode(array('action'=>'domains'));
        return self::__callApi($sRequest);
    }

    //******************************************************************************************************************
    /*
     * @param string $sEmail email to add as sender
     */
    public function add_domain($sEmail=''){
        $aRequest = array(
            'action'    => 'add_domain',
            'email'     => $sEmail,
        );
        return self::__callApi(json_encode($aRequest));

    }

    //******************************************************************************************************************
    /*
     * @param string $sEmail email to resend verify for sender
     */
    public function verify_domain($sEmail=''){
        $aRequest = array(
            'action'    => 'verify_domain',
            'email'     => $sEmail,
        );
        return self::__callApi(json_encode($aRequest));

    }

    //******************************************************************************************************************
    /*
     * @param string $sEmail raw email
     */
    public function send_raw($sEmail=''){
        $aRequest = array(
            'action'    => 'send_raw',
            'data'     => $sEmail,
        );
        return self::__callApi(json_encode($aRequest));

    }

    //******************************************************************************************************************
    /*
     * @param array aData array of the search patterns
     * $aData = array(                          # at least one of the parameter required
     *      'date_from' => '2013-01-01',        # start date to search (from time 00:00:00)
     *      'date_to' => '2013-02-01',          # end date to search (to time 23:59:59)
     *      'sender' => 'sender@example.com',   # sender tp search
     *      'recipient' => 'recipient@example.com'  # recipient to search
     * )
     */
    public function search($aData=array()){
        $aRequest=array(
            'action'    => 'search',
        );
        if (! empty($aData)){
            $aRequest['date_from'] = (! empty($aData['date_from'])) ? $aData['date_from'] : '';
            $aRequest['date_to'] = (! empty($aData['date_to'])) ? $aData['date_to'] : '';
            $aRequest['sender'] = (! empty($aData['sender'])) ? $aData['sender'] : '';
            $aRequest['recipient'] = (! empty($aData['recipient'])) ? $aData['recipient'] : '';
        }
        return self::__callApi(json_encode($aRequest));
    }

    //******************************************************************************************************************
    /*
     * @param string $sId id of the email
     */
    public function info($sId=''){
        $aRequest = array(
            'action'    => 'info',
            'id'    => $sId,
        );
        return self::__callApi(json_encode($aRequest));

    }

    //******************************************************************************************************************
    /*
     * @param array $aData array with unsubscribe data
     * $aData = array(                              # One of more emails for unsubscribe
     *      array(
     *          'email' => 'some1@example.com',     # email to unsubscribe. Required.
     *          'comment' => 'Some comment'         # comment. Optional.
     *      ),
     * )
     */
    public function unsubscribe($aData){
        $aUnsubscribe = array();
        if (! empty($aData)){
            foreach ($aData as $data){
                if (is_array($data)){
                    $atmp = array();
                    if (! empty($data['email'])){}
                    $atmp['email'] = $data['email'];
                    if (! empty($data['comment'])){
                        $atmp['comment'] = $data['comment'];
                    }
                    if (! empty($atmp)){
                        $aUnsubscribe[] = $atmp;
                    }
                    unset($atmp);
                }
            }
        }

        $aRequest = array(
            'action'    => 'unsubscribe',
            'emails'    => $aUnsubscribe,
        );
        return self::__callApi(json_encode($aRequest));
    }

    //******************************************************************************************************************
    /* @param array $aData array with email to send
     * $aData = array(
     *      'html' => 'Html part of the email',     # Html and/or Text email part.
     *      'text' => 'Text part of the email',     # One of them requqired.
     *      'subject' => 'Subject',                 # Email subject. Optional
     *      'encoding' => 'utf8',                   # Encoding of the email
     *      'from' => array(                        # Sender information
     *          'email' => 'sender@example.com',    # Sender email. Required
     *          'name' => 'Sender Name',            # Sender name. Optipnal
     *      ).
     *      'to' => array(                          # One or more recipient
     *          array(
     *              'email' => 'recipient@example.com'. # Recipient email. Required
     *              'name' => 'Recipient Name'          # Recipient name. Optipnal
     *          ).
     *      ),
     * )
     *
     */
    public function send_email($aData=array()){
        $aMessage = array();
        $aMessage['html'] = (! empty($aData['html'])) ? $aData['html'] : '';
        $aMessage['text'] = (! empty($aData['text'])) ? $aData['text'] : '';
        $aMessage['subject'] = (! empty($aData['subject'])) ? $aData['subject'] : '';
        $aMessage['encoding'] = (! empty($aData['encoding'])) ? $aData['encoding'] : '';
        if (! empty($aData['from'])){
            $aFrom = array();
            $aFrom['name'] = (! empty($aData['from']['name'])) ? $aData['from']['name'] : '';
            $aFrom['email'] = (! empty($aData['from']['email'])) ? $aData['from']['email'] : '';
            $aMessage['from'] = $aFrom;
        }
        if (! empty($aData['to'])){
            $aTo = array();
            foreach($aData['to'] as $aRecipient){
                $aToSingle = array();
                $aToSingle['name'] = (! empty($aRecipient['name'])) ? $aRecipient['name'] : '';
                $aToSingle['email'] = (! empty($aRecipient['email'])) ? $aRecipient['email'] : '';
                $aTo[] = $aToSingle;
                unset($aToSingle);
            }
            $aMessage['to'] = $aTo;
        }
        if (! empty($aData['bcc'])){
            $aTo = array();
            foreach($aData['bcc'] as $aRecipient){
                $aToSingle = array();
                $aToSingle['name'] = (! empty($aRecipient['name'])) ? $aRecipient['name'] : '';
                $aToSingle['email'] = (! empty($aRecipient['email'])) ? $aRecipient['email'] : '';
                $aTo[] = $aToSingle;
                unset($aToSingle);
            }
            $aMessage['bcc'] = $aTo;
        }


        if ( (! empty($aMessage['encoding'])) && (! in_array(strtolower($aMessage['encoding']),array('utf8','utf-8'))) ){
            $aMessage['html'] = mb_convert_encoding($aMessage['html'],'utf8',$aMessage['encoding']);
            $aMessage['text'] = mb_convert_encoding($aMessage['text'],'utf8',$aMessage['encoding']);
            $aMessage['subject'] = mb_convert_encoding($aMessage['subject'],'utf8',$aMessage['encoding']);
            if ( (! empty($aMessage['from'])) && (! empty($aMessage['from']['name'])) ){
                $aMessage['from']['name'] = mb_convert_encoding($aMessage['from']['name'],'utf8',$aMessage['encoding']);
            }
            if (! empty($aMessage['to'])){
                foreach ($aMessage['to'] as $key=>$aTo){
                    if (! empty($aTo['name'])){
                        $aMessage['to'][$key]['name'] = mb_convert_encoding($aTo['name'],'utf8',$aMessage['encoding']);
                    }
                }
            }
            $aMessage['encoding'] = 'utf8';
        }

        $aRequest = array(
            'action'    => 'send_email',
            'message'   => $aMessage,
        );

        return self::__callApi(json_encode($aRequest));
    }

    //******************************************************************************************************************
    /*
     * @param array $aData array with email to remove from unsubscribe
     * $aData = array(              # one or more email to remove from unsubscribe
     *      "some@example.com",
     * )
     */
    public function delete_unsubscribe($aData=array()){
        $aDelUnsubscribe = array();
        if ($aData){
            foreach ($aData as $sEmail){
                if (is_string($sEmail)){
                    $aDelUnsubscribe[] = $sEmail;
                }
            }
        }
        $aRequest = array(
            'action'    => 'delete_unsubscribe',
            'emails'    => $aDelUnsubscribe,
        );
        return self::__callApi(json_encode($aRequest));
    }

    //******************************************************************************************************************
    //******************************************************************************************************************
    private static function __callApi($sData=''){
        if (function_exists('curl_version')) {
            if (function_exists('openssl_public_encrypt')){

                $sPass = sha1(microtime(true));
                openssl_public_encrypt($sPass,$sEncPass,self::$sPublicKey);

                $sIv = substr(md5(microtime(true)),0,16);
                openssl_public_encrypt($sIv,$sEncIv,self::$sPublicKey);

                $sEncData = openssl_encrypt($sData,self::ENC_METHOD,$sPass,1,$sIv);

                $aPost = array(
                    'key'   => md5(self::$sPublicKey),
                    'pass'  => $sEncPass,
                    'iv'    => $sEncIv,
                    'data'  => $sEncData,
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aPost));
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 100);
                curl_setopt($ch, CURLOPT_URL, self::BASE_URL);
                $res = curl_exec($ch);
                curl_close($ch);

                unset($sPass);
                unset($sIv);

                $aAnswer = json_decode($res, TRUE);

                if (base64_decode($aAnswer['pass']) && base64_decode($aAnswer['iv'])){
                    openssl_public_decrypt(base64_decode($aAnswer['pass']),$sPass,self::$sPublicKey);
                    openssl_public_decrypt(base64_decode($aAnswer['iv']),$sIv,self::$sPublicKey);
                    $sDataAnswer = openssl_decrypt(base64_decode($aAnswer['data']),self::ENC_METHOD,$sPass,1,$sIv);
                } else {
                    $sDataAnswer = base64_decode($aAnswer['data_unencrypted']);
                }

                return json_decode($sDataAnswer, TRUE);
            } else {
                throw new Exception('OpenSSL required, but not found');
            }
        } else {
            throw new Exception('CURL required, but not found');
        }
    }


}
?> 